<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Partner;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

class PartnerController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_partner')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_partner')->only(['create', 'add']);
        $this->middleware('permission:edit_partner')->only(['edit', 'update']);
        $this->middleware('permission:delete_partner')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('partners.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('partners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:cms.partners,name',
            'short_name' => 'required|unique:cms.partners,short_name',
            'description' => 'required',
            'website' => 'nullable|unique:cms.partners,website',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $fileName = '';

        // upload file to portal public/uploads folder
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $fileName = time() . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image->getRealPath());

            $img->stream(); // <-- Key point

            Storage::disk('portal')->put($fileName, $img);
        }

        $partner_model = new Partner();

        $data = array(
            'name' => $request->name,
            'short_name' => $request->short_name,
            'description' => htmlspecialchars($request->description),
            'website' => $request->website,
            'logo' => $fileName
        );

        DB::beginTransaction();
        try {
            // Add partner
            $res = Partner::create($data);
            $partner_id = $res->partner_id;

            // Add log
            $log = array(
                'partner_id' => $partner_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new partner",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $partner_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Partner successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('partners.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get partner
        $partner_model = new Partner();
        $data = $partner_model->partner($id);
        $date_created = $partner_model->get_date_created($id);
        $date_updated = $partner_model->get_date_updated($id);

        return view('partners.show')
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
        $partner_model = new Partner();
        $data = $partner_model->partner($id);

        return view('partners.edit')->with(compact('data'));
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
            'name' => 'required|unique:cms.partners,name,'.$id.',partner_id',
            'short_name' => 'required|unique:cms.partners,short_name,'.$id.',partner_id',
            'description' => 'required',
            'website' => 'required|unique:cms.partners,website,'.$id.',partner_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $fileName = '';

        // upload file to portal public/uploads folder
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $fileName = time() . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image->getRealPath());

            $img->stream(); // <-- Key point

            Storage::disk('portal')->put($fileName, $img);
        } else {
            $fileName = $request->old_logo; // if no file to upload file name should be the name of old image
        }

        $data = array(
            'name' => $request->name,
            'short_name' => $request->short_name,
            'description' => htmlspecialchars($request->description),
            'website' => $request->website,
            'logo' => $fileName
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_short_name' => $request->old_short_name,
            'old_description' => htmlspecialchars($request->old_description),
            'old_website' => $request->old_website,
            'old_logo' => $request->old_logo
        );

        DB::beginTransaction();
        try {
            // Update partner
            $partner = Partner::find($id);
            $partner->name = $request->name;
            $partner->short_name = $request->short_name;
            $partner->description = htmlspecialchars($request->description);
            $partner->website = $request->website;
            $partner->logo = $fileName;
            $partner->save();

            $partner_model = new Partner();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'partner_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated partner",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $partner_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Partner successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('partners.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $partner_model = new Partner();

        // Delete log
        $log = array(
            'partner_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted partner",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $partner_model->delete_partner($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $partner_model = new Partner();
        $partners = $partner_model->partners();

        $data = array();

        foreach ($partners as $partner) {
            $data[] = array(
                'partner_id' => $partner->partner_id,
                'name' => $partner->name,
                'description' => str_limit($partner->description, 500, '...'),
                'website' => $partner->website
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_partner')) {
                $buttons .= '<a href="'.route('partners.show', $data['partner_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_partner')) {
                $buttons .= '<a href="'.route('partners.edit', $data['partner_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_partner')) {
                $buttons .= '<button onclick="delete_partner('.$data['partner_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
