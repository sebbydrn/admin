<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Section;
use App\Page;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class SectionController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_section')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_section')->only(['create', 'add']);
        $this->middleware('permission:edit_section')->only(['edit', 'update', 'publish']);
        $this->middleware('permission:delete_section')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('sections.index');
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

        return view('sections.create')->with(compact('pages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'display_name' => 'required|unique:cms.sections,display_name',
            'url' => 'required|unique:cms.sections,url',
            'page' => 'required',
            'is_public' => 'required',
            'is_dynamic' => 'required'
        ]);

        $section_model = new Section();

        $data = array(
            'page_id' => $request->page,
            'display_name' => $request->display_name,
            'url' => $request->url,
            'is_public' => $request->is_public,
            'is_dynamic' => $request->is_dynamic
        );

        DB::beginTransaction();
        try {
            // Add section
            $res = Section::create($data);
            $section_id = $res->section_id;

            // Add log
            $log = array(
                'section_id' => $section_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new section",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $section_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Section successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('sections.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get section
        $section_model = new Section();
        $data = $section_model->section($id);
        $date_created = $section_model->get_date_created($id);
        $date_updated = $section_model->get_date_updated($id);

        return view('sections.show')
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
        $section_model = new Section();
        $data = $section_model->section($id);

        return view('sections.edit')
        ->with(compact('pages'))
        ->with(compact('data'));
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
            'display_name' => 'required|unique:cms.sections,display_name,'.$id.',section_id',
            'url' => 'required|unique:cms.sections,url,'.$id.',section_id',
            'page' => 'required',
            'is_public' => 'required',
            'is_dynamic' => 'required'
        ]);

        $data = array(
            'display_name' => $request->display_name,
            'url' => $request->url,
            'page' => $request->page,
            'is_public' => $request->is_public,
            'is_dynamic' => $request->is_dynamic
        );

        $old_data = array(
            'old_display_name' => $request->old_display_name,
            'old_url' => $request->old_url,
            'old_page' => $request->old_page,
            'old_is_public' => $request->old_is_public,
            'old_is_dynamic' => $request->old_is_dynamic
        );

        DB::beginTransaction();
        try {
            // Update section
            $section = Section::find($id);
            $section->page_id = $request->page;
            $section->display_name = $request->display_name;
            $section->url = $request->url;
            $section->is_public = $request->is_public;
            $section->is_dynamic = $request->is_dynamic;
            $section->save();

            $section_model = new Section();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'section_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated section",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $section_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Section successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('sections.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $section_model = new Section();

        // Delete log
        $log = array(
            'section_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted section",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $section_model->delete_section($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $section_model = new Section();
        $sections = $section_model->sections();

        $data = array();

        foreach ($sections as $section) {
            $data[] = array(
                'section_id' => $section->section_id,
                'name' => $section->display_name,
                'url' => $section->url,
                'page' => $section->page_name,
                'is_public' => $section->is_public,
                'is_dynamic' => $section->is_dynamic,
                'is_published' => $section->is_published
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

            if ($data['is_dynamic'] == 1) {
                $status .= '<span class="badge badge-success">Dynamic</span>&nbsp;&nbsp';
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
            if (Entrust::can('view_section')) {
                $buttons .= '<a href="'.route('sections.show', $data['section_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_section')) {
                $buttons .= '<a href="'.route('sections.edit', $data['section_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_published'] == '' || $data['is_published'] == 0) {
                    $buttons .= '<button onclick="publish_section('.$data['section_id'].')" class="btn btn-success btn-sm action_buttons" title="Publish"><i class="fa fa-upload"></i> Publish</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_section')) {
                $buttons .= '<button onclick="delete_section('.$data['section_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function publish(Request $request) {
        $section_id = $request->section_id;

        DB::beginTransaction();
        try {
            // Update section
            $section = Section::find($section_id);
            $section->is_published = 1;
            $section->save();

            $section_model = new Section();

            $log = array(
                'section_id' => $section_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Published section",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'old_value' => 0,
                'new_value' => 1,
                'OS' => $this->operating_system()
            );

            $res = $section_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }
}
