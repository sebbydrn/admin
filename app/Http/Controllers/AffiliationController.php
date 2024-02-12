<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Affiliation;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Entrust;

class AffiliationController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_affiliation')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_affiliation')->only(['create', 'add']);
        $this->middleware('permission:edit_affiliation')->only(['edit', 'update']);
        $this->middleware('permission:delete_affiliation')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('affiliations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('affiliations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:affiliations,name',
        ]);

        $affiliation = new Affiliation;

        $data = array('name' => $request->name);

        DB::beginTransaction();
        try {
            // Add system
            $res = Affiliation::create($data);
            $affiliation_id = $res->affiliation_id;

            // Add log
            $log = array(
                'affiliation_id' => $affiliation_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new affiliation",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );
            $res2 = $affiliation->add_log($log);
            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Affiliation successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('affiliations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get affiliation
        $affiliation = new Affiliation;
        $data = $affiliation->affiliation($id);
        $date_created = $affiliation->get_date_created($id);
        $date_updated = $affiliation->get_date_updated($id);

        return view('affiliations.show')
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
        $affiliation = new Affiliation;
        $data = $affiliation->affiliation($id);

        return view('affiliations.edit')->with(compact('data'));
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
            'name' => 'required|unique:affiliations,name,'.$id.',affiliation_id',
        ]);

        $data = array(
            'name' => $request->name
        );

        $old_data = array(
            'old_name' => $request->old_name
        );

        DB::beginTransaction();
        try {
            // Update affiliation
            $affiliation = Affiliation::find($id);
            $affiliation->name = $request->name;
            $affiliation->save();

            $affiliation2 = new Affiliation;

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'affiliation_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated affiliation",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res2 = $affiliation2->add_log($log);
                }
            }

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Affiliation successfully updated.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('affiliations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $affiliation = new Affiliation;

        // Delete log
        $log = array(
            'affiliation_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted affiliation",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $affiliation->delete_affiliation($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $affiliation = new Affiliation;
        $affiliations = $affiliation->affiliations();

        $data = array();

        foreach ($affiliations as $item) {
            $data[] = array(
                'id' => $item->affiliation_id,
                'name' => $item->name
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('add_affiliation')) {
                $buttons .= '<a href="'.route('affiliations.show', $data['id']).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_affiliation')) {
                $buttons .= '<a href="'.route('affiliations.edit', $data['id']).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_affiliation')) {
                $buttons .= '<button onclick="delete_affiliation('.$data['id'].')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
