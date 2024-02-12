<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Link;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class LinkController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_link')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_link')->only(['create', 'add']);
        $this->middleware('permission:edit_link')->only(['edit', 'update']);
        $this->middleware('permission:delete_link')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('links.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('links.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:cms.links,name',
            'link' => 'required|unique:cms.links,link'
        ]);

        $link_model = new Link();

        $data = array(
            'name' => $request->name,
            'link' => $request->link
        );

        DB::beginTransaction();
        try {
            // Add link
            $res = Link::create($data);
            $link_id = $res->link_id;

            // Add log
            $log = array(
                'link_id' => $link_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new link",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $link_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Link successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('links.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get link
        $link_model = new Link();
        $data = $link_model->link($id);
        $date_created = $link_model->get_date_created($id);
        $date_updated = $link_model->get_date_updated($id);

        return view('links.show')
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
        $link_model = new Link();
        $data = $link_model->link($id);

        return view('links.edit')->with(compact('data'));
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
            'name' => 'required|unique:cms.links,name,'.$id.',link_id',
            'link' => 'required|unique:cms.links,link,'.$id.',link_id'
        ]);

        $data = array(
            'name' => $request->name,
            'link' => $request->link
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_link' => $request->old_link
        );

        DB::beginTransaction();
        try {
            // Update link
            $link = Link::find($id);
            $link->name = $request->name;
            $link->link = $request->link;
            $link->save();

            $link_model = new Link();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'link_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated link",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $link_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Link successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('links.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $link_model = new Link();

        // Delete log
        $log = array(
            'link_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted link",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $link_model->delete_link($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $link_model = new Link();
        $links = $link_model->links();

        $data = array();

        foreach ($links as $link) {
            $data[] = array(
                'link_id' => $link->link_id,
                'name' => $link->name,
                'link' => $link->link
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_link')) {
                $buttons .= '<a href="'.route('links.show', $data['link_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_link')) {
                $buttons .= '<a href="'.route('links.edit', $data['link_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_link')) {
                $buttons .= '<button onclick="delete_link('.$data['link_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
