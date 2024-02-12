<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PhilriceStation;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Entrust;

class StationController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_philrice_station')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_philrice_station')->only(['create', 'add']);
        $this->middleware('permission:edit_philrice_station')->only(['edit', 'update']);
        $this->middleware('permission:delete_philrice_station')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('stations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('stations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:philrice_station,name',
            'station_code' => 'required|unique:philrice_station,station_code'
        ]);

        $philrice_station = new PhilriceStation;

        $data = array(
            'name' => $request->name,
            'station_code' => $request->station_code
        );

        DB::beginTransaction();
        try {
            // Add PhilRice station
            $res = PhilRiceStation::create($data);
            $philrice_station_id = $res->philrice_station_id;

            // Add log
            $log = array(
                'philrice_station_id' => $philrice_station_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new PhilRice station",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );
            $res2 = $philrice_station->add_log($log);
            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'PhilRice station successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('philrice_stations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get PhilRice station
        $philrice_station = new PhilRiceStation;
        $data = $philrice_station->station($id);
        $date_created = $philrice_station->get_date_created($id);
        $date_updated = $philrice_station->get_date_updated($id);

        return view('stations.show')
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
        $philrice_station = new PhilRiceStation;
        $data = $philrice_station->station($id);

        return view('stations.edit')->with(compact('data'));
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
            'name' => 'required|unique:philrice_station,name,'.$id.',philrice_station_id',
            'station_code' => 'required|unique:philrice_station,station_code,'.$id.',philrice_station_id'
        ]);

        $data = array(
            'name' => $request->name,
            'station_code' => $request->station_code
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_station_code' => $request->old_station_code
        );

        DB::beginTransaction();
        try {
            // Update PhilRice Station
            $philrice_station = PhilRiceStation::find($id);
            $philrice_station->name = $request->name;
            $philrice_station->station_code = $request->station_code;
            $philrice_station->save();

            $philrice_station2 = new PhilRiceStation;

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'philrice_station_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated PhilRice station",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res2 = $philrice_station2->add_log($log);
                }
            }

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'PhilRice station successfully updated.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('philrice_stations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $philrice_station = new PhilRiceStation;

        // Delete log
        $log = array(
            'philrice_station_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted PhilRice station",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $philrice_station->delete_station($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $philrice_station = new PhilriceStation;
        $stations = $philrice_station->stations();

        $data = array();

        foreach ($stations as $item) {
            $data[] = array(
                'id' => $item->philrice_station_id,
                'name' => $item->name,
                'station_code' => $item->station_code
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_philrice_station')) {
                $buttons .= '<a href="'.route('philrice_stations.show', $data['id']).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_philrice_station')) {
                $buttons .= '<a href="'.route('philrice_stations.edit', $data['id']).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_philrice_station')) {
                $buttons .= '<button onclick="delete_station('.$data['id'].')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }

            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);        
    }
}
