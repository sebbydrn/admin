<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class PageController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_page')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_page')->only(['create', 'add']);
        $this->middleware('permission:edit_page')->only(['edit', 'update', 'publish']);
        $this->middleware('permission:delete_page')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'display_name' => 'required|unique:cms.pages,display_name',
            'url' => 'required|unique:cms.pages,url',
            'is_public' => 'required'
        ]);

        $page_model = new Page();

        $data = array(
            'display_name' => $request->display_name,
            'url' => $request->url,
            'is_public' => $request->is_public
        );

        DB::beginTransaction();
        try {
            // Add page
            $res = Page::create($data);
            $page_id = $res->page_id;

            // Add log
            $log = array(
                'page_id' => $page_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new page",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $page_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Page successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('pages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get page
        $page_model = new Page();
        $data = $page_model->page($id);
        $date_created = $page_model->get_date_created($id);
        $date_updated = $page_model->get_date_updated($id);

        return view('pages.show')
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
        // Get data
        $page_model = new Page();
        $data = $page_model->page($id);

        return view('pages.edit')->with(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'display_name' => 'required|unique:cms.pages,display_name,'.$id.',page_id',
            'url' => 'required|unique:cms.pages,url,'.$id.',page_id',
            'is_public' => 'required'
        ]);

        $data = array(
            'display_name' => $request->display_name,
            'url' => $request->url,
            'is_public' => $request->is_public
        );

        $old_data = array(
            'old_display_name' => $request->old_display_name,
            'old_url' => $request->old_url,
            'old_is_public' => $request->old_is_public
        );

        DB::beginTransaction();
        try {
            // Update page
            $page = Page::find($id);
            $page->display_name = $request->display_name;
            $page->url = $request->url;
            $page->is_public = $request->is_public;
            $page->save();

            $page_model = new Page();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'page_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated page",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $page_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Page successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('pages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $page_model = new Page();

        // Delete log
        $log = array(
            'page_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted page",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $page_model->delete_page($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $page_model = new Page();
        $pages = $page_model->pages();

        $data = array();

        foreach ($pages as $page) {
            $data[] = array(
                'page_id' => $page->page_id,
                'name' => $page->display_name,
                'url' => $page->url,
                'is_public' => $page->is_public,
                'is_published' => $page->is_published
            );
        }

        return Datatables::of($data)
        ->addColumn('status', function($data) {
            $status = '';
            if ($data['is_public'] == 1) {
                $status .= '<span class="badge badge-success">Public</span>&nbsp;&nbsp;';
            } else if ($data['is_public'] == 0) {
                $status .= '<span class="badge badge-danger">Private</span>&nbsp;&nbsp;';
            }

            if ($data['is_published'] == 1) {
                $status .= '<span class="badge badge-success">Published</span>';
            } else if ($data['is_published'] == '') {
                $status .= '<span class="badge badge-danger">Unpublished</span>';
            }

            return $status;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_page')) {
                $buttons .= '<a href="'.route('pages.show', $data['page_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_page')) {
                $buttons .= '<a href="'.route('pages.edit', $data['page_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_published'] == '' || $data['is_published'] == 0) {
                    $buttons .= '<button onclick="publish_page('.$data['page_id'].')" class="btn btn-success btn-sm action_buttons" title="Publish"><i class="fa fa-upload"></i> Publish</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_page')) {
                $buttons .= '<button onclick="delete_page('.$data['page_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function publish(Request $request) {
        $page_id = $request->page_id;

        DB::beginTransaction();
        try {
            // Update page
            $page = Page::find($page_id);
            $page->is_published = 1;
            $page->save();

            $page_model = new Page();

            $log = array(
                'page_id' => $page_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Published page",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'old_value' => 0,
                'new_value' => 1,
                'OS' => $this->operating_system()
            );

            $res = $page_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }
}
