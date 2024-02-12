<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class ContactController extends Controller {
    
    public function __construct() {
        $this->middleware('permission:view_contact')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_contact')->only(['create', 'add']);
        $this->middleware('permission:edit_contact')->only(['edit', 'update']);
        $this->middleware('permission:delete_contact')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('contacts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:cms.contacts,name',
            'contact_detail' => 'required|unique:cms.contacts,contact_detail'
        ]);

        $contact_model = new Contact();

        $data = array(
            'name' => $request->name,
            'contact_detail' => $request->contact_detail
        );

        DB::beginTransaction();
        try {
            // Add contact
            $res = Contact::create($data);
            $contact_id = $res->contact_id;

            // Add log
            $log = array(
                'contact_id' => $contact_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new contact",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $contact_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Contact successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get contact
        $contact_model = new Contact();
        $data = $contact_model->contact($id);
        $date_created = $contact_model->get_date_created($id);
        $date_updated = $contact_model->get_date_updated($id);

        return view('contacts.show')
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
        $contact_model = new Contact();
        $data = $contact_model->contact($id);

        return view('contacts.edit')->with(compact('data'));
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
            'name' => 'required|unique:cms.contacts,name,'.$id.',contact_id',
            'contact_detail' => 'required|unique:cms.contacts,contact_detail,'.$id.',contact_id'
        ]);

        $data = array(
            'name' => $request->name,
            'contact_detail' => $request->contact_detail
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_contact_detail' => $request->old_contact_detail
        );

        DB::beginTransaction();
        try {
            // Update contact
            $contact = Contact::find($id);
            $contact->name = $request->name;
            $contact->contact_detail = $request->contact_detail;
            $contact->save();

            $contact_model = new Contact();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'contact_id' => $id,
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

                    $res = $contact_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Contact successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $contact_model = new Contact();

        // Delete log
        $log = array(
            'contact_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted contact",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $contact_model->delete_contact($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $contact_model = new Contact();
        $contacts = $contact_model->contacts();

        $data = array();

        foreach ($contacts as $contact) {
            $data[] = array(
                'contact_id' => $contact->contact_id,
                'name' => $contact->name,
                'contact_detail' => $contact->contact_detail
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_contact')) {
                $buttons .= '<a href="'.route('contacts.show', $data['contact_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_contact')) {
                $buttons .= '<a href="'.route('contacts.edit', $data['contact_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_contact')) {
                $buttons .= '<button onclick="delete_contact('.$data['contact_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
