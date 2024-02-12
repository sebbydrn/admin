<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Section;
use App\Page;
use App\Content;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ContentController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_content')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_content')->only(['create', 'add']);
        $this->middleware('permission:edit_content')->only(['edit', 'update', 'publish']);
        $this->middleware('permission:delete_content')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('contents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // Get pages
        $page_model = new Page();
        $pages = $page_model->pages();

        return view('contents.create')->with(compact('pages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'page' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $fileName = '';

        // upload file to portal public/uploads folder
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image->getRealPath());

            $img->stream(); // <-- Key point

            Storage::disk('portal')->put($fileName, $img);
        }

        $content_model = new Content();

        $data = array(
            'page_id' => $request->page,
            'section_id' => $request->section,
            'subtitle' => $request->subtitle,
            'content' => htmlspecialchars($request->content),
            'image' => $fileName
        );

        DB::beginTransaction();
        try {
            // Add content
            $res = Content::create($data);
            $content_id = $res->content_id;

            // Add log
            $log = array(
                'content_id' => $content_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new content",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $content_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Content successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('contents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get content
        $content_model = new Content();
        $data = $content_model->content($id);
        $date_created = $content_model->get_date_created($id);
        $date_updated = $content_model->get_date_updated($id);

        return view('contents.show')
        ->with(compact('data'))
        ->with(compact('date_created'))
        ->with(compact('date_updated'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Get pages
        $page_model = new Page();
        $pages = $page_model->pages();

        // Get data
        $content_model = new Content();
        $data = $content_model->content($id);

        // Get sections
        $sections = $content_model->sections($data->page_id);

        return view('contents.edit')
        ->with(compact('pages'))
        ->with(compact('data'))
        ->with(compact('sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $this->validate($request, [
            'page' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $fileName = '';

        // upload file to portal public/uploads folder
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image->getRealPath());

            $img->stream(); // <-- Key point

            Storage::disk('portal')->put($fileName, $img);
        } else {
            $fileName = $request->old_image; // if no file to upload file name should be the name of old image
        }

        $data = array(
            'page' => $request->page,
            'section' => $request->section,
            'subtitle' => $request->subtitle,
            'content' => htmlspecialchars($request->content),
            'image' => $fileName
        );

        $old_data = array(
            'old_page' => $request->old_page,
            'old_section' => $request->old_section,
            'old_subtitle' => $request->old_subtitle,
            'old_content' => htmlspecialchars($request->old_content),
            'old_image' => $request->old_image
        );

        DB::beginTransaction();
        try {
            // Update content
            $content = Content::find($id);
            $content->page_id = $request->page;
            $content->section_id = $request->section;
            $content->subtitle = $request->subtitle;
            $content->content = htmlspecialchars($request->content);
            $content->image = $fileName;
            $content->save();

            $content_model = new Content();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'content_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated content",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $content_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Content successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('contents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $content_model = new Content();

        // Delete log
        $log = array(
            'content_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted content",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $content_model->delete_content($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $content_model = new Content();
        $contents = $content_model->contents();

        $data = array();

        foreach ($contents as $content) {
            $data[] = array(
                'content_id' => $content->content_id,
                'page' => $content->page_name,
                'section' => $content->section_name,
                'subtitle' => $content->subtitle,
                'content' => str_limit($content->content, 500, '...'),
                'is_published' => $content->is_published
            );
        }

        return Datatables::of($data)
        ->addColumn('status', function($data) {
            $status = '';

            if ($data['is_published'] == 1) {
                $status .= '<span class="badge badge-success">Published</span>';
            } else if ($data['is_published'] == '') {
                $status .= '<span class="badge badge-danger">Unpublished</span>';
            }

            return $status;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_content')) {
                $buttons .= '<a href="'.route('contents.show', $data['content_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_content')) {
                $buttons .= '<a href="'.route('contents.edit', $data['content_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_published'] == '' || $data['is_published'] == 0) {
                    $buttons .= '<button onclick="publish_content('.$data['content_id'].')" class="btn btn-success btn-sm action_buttons" title="Publish"><i class="fa fa-upload"></i> Publish</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_content')) {
                $buttons .= '<button onclick="delete_content('.$data['content_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function sections(Request $request) {
        $page_id = $request->page_id;

        // Get sections
        $content_model = new Content();
        $sections = $content_model->sections($page_id);

        echo json_encode($sections);
    }

    public function publish(Request $request) {
        $content_id = $request->content_id;

        DB::beginTransaction();
        try {
            // Update content
            $content = Content::find($content_id);
            $content->is_published = 1;
            $content->save();

            $content_model = new Content();

            $log = array(
                'content_id' => $content_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Published content",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'old_value' => 0,
                'new_value' => 1,
                'OS' => $this->operating_system()
            );

            $res = $content_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }
}
