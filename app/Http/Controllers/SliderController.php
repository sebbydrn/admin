<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slider;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

class SliderController extends Controller {

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
        return view('sliders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg',
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

        $slider_model = new Slider();

        $data = array(
            'name' => $request->name,
            'link' => $request->link,
            'image' => $fileName
        );

        DB::beginTransaction();
        try {
            // Add slider
            $res = Slider::create($data);
            $slider_id = $res->slider_id;

            // Add log
            $log = array(
                'slider_id' => $slider_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new slider",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $slider_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Slider successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('sliders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get slider
        $slider_model = new Slider();
        $data = $slider_model->slider($id);
        $date_created = $slider_model->get_date_created($id);
        $date_updated = $slider_model->get_date_updated($id);

        return view('sliders.show')
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
        $slider_model = new Slider();
        $data = $slider_model->slider($id);

        return view('sliders.edit')->with(compact('data'));
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
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
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
            'name' => $request->name,
            'link' => $request->link,
            'image' => $fileName
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_link' => $request->old_link,
            'old_image' => $request->old_image
        );

        DB::beginTransaction();
        try {
            // Update slider
            $slider = Slider::find($id);
            $slider->name = $request->name;
            $slider->link = $request->link;
            $slider->image = $fileName;
            $slider->save();

            $slider_model = new Slider();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'slider_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated slider",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $slider_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Slider successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $slider_model = new Slider();

        // Delete log
        $log = array(
            'slider_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted slider",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $slider_model->delete_slider($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $slider_model = new Slider();
        $sliders = $slider_model->sliders();

        $data = array();

        foreach ($sliders as $slider) {
            $data[] = array(
                'slider_id' => $slider->slider_id,
                'name' => $slider->name,
                'link' => $slider->link
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_slider')) {
                $buttons .= '<a href="'.route('sliders.show', $data['slider_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_slider')) {
                $buttons .= '<a href="'.route('sliders.edit', $data['slider_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_slider')) {
                $buttons .= '<button onclick="delete_slider('.$data['slider_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
