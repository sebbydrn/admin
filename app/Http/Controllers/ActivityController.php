<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class ActivityController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_activity')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_activity')->only(['create', 'add']);
        $this->middleware('permission:edit_activity')->only(['edit', 'update']);
        $this->middleware('permission:delete_activity')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('activities.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('activities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:activities,name',
        ]);

        $activity_model = new Activity;

        $data = array(
            'name' => $request->name
        );

        DB::beginTransaction();
        try {
            // Add activity
            $res = Activity::create($data);
            $activity_id = $res->activity_id;

            // Add log
            $log = array(
                'activity_id' => $activity_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new activity",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $activity_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Activity successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('activities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get activity
        $activity_model = new Activity;
        $data = $activity_model->activity($id);
        $date_created = $activity_model->get_date_created($id);
        $date_updated = $activity_model->get_date_updated($id);

        return view('activities.show')
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
    public function edit($id)
    {
        // Get data
        $activity_model = new Activity;
        $data = $activity_model->activity($id);

        return view('activities.edit')->with(compact('data'));
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
            'name' => 'required|unique:activities,name,'.$id.',activity_id',
        ]);

        $data = array(
            'name' => $request->name
        );

        $old_data = array(
            'old_name' => $request->old_name
        );

        DB::beginTransaction();
        try {
            // Update activity
            $activity = Activity::find($id);
            $activity->name = $request->name;
            $activity->save();

            $activity_model = new Activity;

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'activity_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated activity",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $activity_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Activity successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('activities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $activity_model = new Activity;

        // Delete log
        $log = array(
            'activity_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted activity",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $activity_model->delete_activity($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $activity_model = new Activity();
        $activities = $activity_model->activities();

        $data = array();

        foreach ($activities as $item) {
            $data[] = array(
                'id' => $item->activity_id,
                'name' => $item->name
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_activity')) {
                $buttons .= '<a href="'.route('activities.show', $data['id']).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_activity')) {
                $buttons .= '<a href="'.route('activities.edit', $data['id']).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_activity')) {
                $buttons .= '<button onclick="delete_activity('.$data['id'].')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
