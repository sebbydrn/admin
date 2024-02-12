<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Downloadable;
use App\DownloadableActivities;
use App\DownloadableCategory;
use App\AffiliationAccess;
use DB, Auth, Entrust, Storage;
use Yajra\Datatables\Datatables;

class DownloadableController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_downloadables')->only(['index', 'datatable']);
        $this->middleware('permission:add_downloadables')->only(['create', 'store']);
        $this->middleware('permission:edit_downloadables')->only(['edit', 'update', 'publish']);
        $this->middleware('permission:delete_downloadables')->only(['destroy']);
    }
    
	public function index() {
		return view('downloadable.index');
	}

	public function create() {
		$downloadableCategories = DownloadableCategory::select('downloadable_category_id', 'display_name')->get();

        // Affiliations
        $affiliations = $this->affiliations();

		return view('downloadable.create', compact(['downloadableCategories', 'affiliations']));
	}

	public function store(Request $request) {
		$this->validate($request, [
            'display_name' => 'required',
            'version' => 'required',
            'downloadable_category' => 'required',
            'is_public' => 'required',
            'affiliation_access' => 'required_if:is_public,0'
        ]);

		$fileName = '';

        // upload file to portal public/downloadables folder
        if ($request->hasFile('uploadFile')) {
            $uploadFile = $request->file('uploadFile');
            $fileName = $request->display_name . '_' . time() . '.' . $uploadFile->getClientOriginalExtension();
            Storage::disk('downloadables')->put($fileName, file_get_contents($uploadFile->getRealPath()));
        }

        DB::beginTransaction();
        try {
        	$downloadable = new Downloadable;
        	$downloadable->display_name = $request->display_name;
            $downloadable->version = $request->version;

        	if ($request->link != "") {
        		$downloadable->link = $request->link;
        	} else {
        		$downloadable->link = env('APP_URL') . '/portal/public/downloadables/' . $fileName;
        	}

        	$downloadable->downloadable_category_id = $request->downloadable_category;
        	$downloadable->is_public = $request->is_public;
        	$downloadable->is_published = 0;
        	$downloadable->save();
        	$downloadableID = $downloadable->downloadable_id;

            // Insert affiliation access
            if ($request->affiliation_access) {
                foreach ($request->affiliation_access as $affiliation_access) {
                    $affiliationAccess = new AffiliationAccess;
                    $affiliationAccess->downloadable_id = $downloadableID;
                    $affiliationAccess->affiliation_id = $affiliation_access;
                    $affiliationAccess->save();
                }
            } else {
                $affiliationAccess = new AffiliationAccess;
                $affiliationAccess->downloadable_id = $downloadableID;
                $affiliationAccess->affiliation_id = 0;
                $affiliationAccess->save();
            }

        	// Insert log
        	$activity = new DownloadableActivities;
        	$activity->downloadable_id = $downloadableID;
    		$activity->user_id = Auth::user()->user_id;
    		$activity->browser = $this->browser();
    		$activity->activity = "Added new downloadable";
    		$activity->device = $this->device();
    		$activity->ip_env_address = $request->ip();
    		$activity->ip_server_address = $request->server('SERVER_ADDR');
    		$activity->OS = $this->operating_system();
    		$activity->save();

        	// Commit transaction
    		DB::commit();
    		
            // Return success message
            $request->session()->flash('success', 'Downloadable successfully added.');
        } catch (Exception $e) {
        	DB::rollback();
    		
            // Return error message
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('downloadables.index');

	}

    public function datatable() {
        // Get data
        $downloadables = Downloadable::get();

        $data = collect($downloadables);

        return Datatables::of($data)
        ->addColumn('downloadable_category', function($data) {
            $downloadableCategoryID = $data->downloadable_category_id;

            $downlodableCategory = DownloadableCategory::select('display_name')
                                                        ->where('downloadable_category_id', '=', $downloadableCategoryID)
                                                        ->first();

            return $downlodableCategory->display_name;
        })
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
            if (Entrust::can('edit_downloadables')) {
                $buttons .= '<a href="'.route('downloadables.edit', $data['downloadable_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_published'] == '' || $data['is_published'] == 0) {
                    $buttons .= '<button onclick="publish_downloadable('.$data['downloadable_id'].')" class="btn btn-success btn-sm action_buttons" title="Publish"><i class="fa fa-upload"></i> Publish</button>&nbsp;&nbsp;';
                } else if ($data['is_published'] == 1) {
                    $buttons .= '<button onclick="unpublish_downloadable('.$data['downloadable_id'].')" class="btn btn-danger btn-sm action_buttons" title="Unpublish"><i class="fa fa-ban"></i> Unpublish</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_downloadables')) {
                $buttons .= '<button onclick="delete_downloadable('.$data['downloadable_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function publish(Request $request) {
        $downloadableID = $request->downloadableID;

        DB::beginTransaction();
        try {
            // Update downloadable
            $downloadable = Downloadable::find($downloadableID);
            $downloadable->is_published = 1;
            $downloadable->save();

            // Insert log
            $activity = new DownloadableActivities;
            $activity->downloadable_id = $downloadableID;
            $activity->user_id = Auth::user()->user_id;
            $activity->browser = $this->browser();
            $activity->activity = "Published downloadable";
            $activity->device = $this->device();
            $activity->ip_env_address = $request->ip();
            $activity->ip_server_address = $request->server('SERVER_ADDR');
            $activity->OS = $this->operating_system();
            $activity->save();
            
            // Commit transaction
            DB::commit();

            $res = "success";
        } catch (Exception $e) {
             DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }

    public function unpublish(Request $request) {
        $downloadableID = $request->downloadableID;

        DB::beginTransaction();
        try {
            // Update downloadable
            $downloadable = Downloadable::find($downloadableID);
            $downloadable->is_published = 0;
            $downloadable->save();

            // Insert log
            $activity = new DownloadableActivities;
            $activity->downloadable_id = $downloadableID;
            $activity->user_id = Auth::user()->user_id;
            $activity->browser = $this->browser();
            $activity->activity = "Unpublished downloadable";
            $activity->device = $this->device();
            $activity->ip_env_address = $request->ip();
            $activity->ip_server_address = $request->server('SERVER_ADDR');
            $activity->OS = $this->operating_system();
            $activity->save();
            
            // Commit transaction
            DB::commit();

            $res = "success";
        } catch (Exception $e) {
             DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }

    public function destroy(Request $request, $id) {
        DB::beginTransaction();
        try {
            // Delete downloadable
            $downloadable = Downloadable::destroy($id);

            // Delete affiliation access
            $affiliationAccess = AffiliationAccess::where('downloadable_id', '=', $id)->delete();

            // Add log
            $activity = new DownloadableActivities;
            $activity->downloadable_id = $id;
            $activity->user_id = Auth::user()->user_id;
            $activity->browser = $this->browser();
            $activity->activity = "Deleted downloadable";
            $activity->device = $this->device();
            $activity->ip_env_address = $request->ip();
            $activity->ip_server_address = $request->server('SERVER_ADDR');
            $activity->OS = $this->operating_system();
            $activity->save();

            // Commit transaction
            DB::commit();

            // Return success message
            echo json_encode("success");
        } catch (Exception $e) {
            // Rollback transcation
            DB::rollback();
            
            // Return error message
            echo json_encode($e->getMessage());
        }
    }

    public function edit($id) {
        $data = Downloadable::find($id);

        $downloadableCategories = DownloadableCategory::select('downloadable_category_id', 'display_name')->get();

        // Affiliations
        $affiliations = $this->affiliations();

        $affiliation_access = AffiliationAccess::select('affiliation_id')->where('downloadable_id', '=', $id)->get();
        $affiliationAccess = array();

        foreach ($affiliation_access as $item) {
            array_push($affiliationAccess, $item->affiliation_id);
        }

        return view('downloadable.edit', compact(['data', 'downloadableCategories', 'affiliations', 'affiliationAccess']));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'display_name' => 'required',
            'version' => 'required',
            'downloadable_category' => 'required',
            'is_public' => 'required',
            'affiliation_access' => 'required_if:is_public,0'
        ]);

        // Updated downloadable category data
        $downloadable_data = array(
            'display_name' => $request->display_name,
            'version' => $request->version,
            'downloadable_category_id' => $request->downloadable_category,
            'is_public' => $request->is_public,
        );

        // Old downloadable data
        $old_downloadable_data = array(
            'old_display_name' => $request->old_display_name,
            'old_version' => $request->old_version,
            'old_downloadable_category_id' => $request->old_downloadable_category,
            'old_link' => $request->old_link,
            'old_is_public' => $request->old_is_public
        );

        $fileName = '';

        // upload file to portal public/downloadables folder
        if ($request->hasFile('uploadFile')) {
            $uploadFile = $request->file('uploadFile');
            $fileName = $request->display_name . '_' . time() . '.' . $uploadFile->getClientOriginalExtension();
            Storage::disk('downloadables')->put($fileName, file_get_contents($uploadFile->getRealPath()));
            $downloadable_data['link'] = env('APP_URL') . '/portal/public/downloadables/' . $fileName;
        }

        if ($request->link != "") {
            $downloadable_data['link'] = $request->link;
        }

        DB::beginTransaction();
        try {
            // Update downloadable
            $downloadable = Downloadable::find($id);
            $downloadable->display_name = $downloadable_data['display_name'];
            $downloadable->version = $downloadable_data['version'];
            $downloadable->downloadable_category_id = $downloadable_data['downloadable_category_id'];

            if (isset($downloadable_data['link'])) {
                $downloadable->link = $downloadable_data['link'];
            }

            $downloadable->is_public = $downloadable_data['is_public'];
            $downloadable->save();

            if ($request->affiliation_access) {
                // TODO: Add log for old affiliation access

                // Delete old affiliation access
                $affiliationAccess = AffiliationAccess::where('downloadable_id', '=', $id)->delete();

                // Insert new affiliation access
                foreach ($request->affiliation_access as $affiliation_access) {
                    $affiliationAccess = new AffiliationAccess;
                    $affiliationAccess->downloadable_id = $id;
                    $affiliationAccess->affiliation_id = $affiliation_access;
                    $affiliationAccess->save();
                }
            } else {
                // Delete old affiliation access
                $affiliationAccess = AffiliationAccess::where('downloadable_id', '=', $id)->delete();

                // Insert new affiliation access
                $affiliationAccess = new AffiliationAccess;
                $affiliationAccess->downloadable_id = $id;
                $affiliationAccess->affiliation_id = 0;
                $affiliationAccess->save();
            }
            // Check if original value is different from update value
            // If true save as log
            foreach ($downloadable_data as $key => $value) {
                if ($old_downloadable_data['old_'.$key] != $value) {
                    $log = new DownloadableActivities;
                    $log->downloadable_id = $id;
                    $log->user_id = Auth::user()->user_id;
                    $log->browser = $this->browser();
                    $log->activity = "Updated downloadable";
                    $log->device = $this->device();
                    $log->ip_env_address = $request->ip();
                    $log->ip_server_address = $request->server('SERVER_ADDR');
                    $log->old_value = $old_downloadable_data['old_'.$key];
                    $log->new_value = $value;
                    $log->OS = $this->operating_system();
                    $log->save();
                }
            }

            // Commit transaction
            DB::commit();

            // Return success message
            $request->session()->flash('success', 'Downloadable successfully updated.');
        } catch (Exception $e) {
            // Rollback transcation
            DB::rollback();
            
            // Return error message
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('downloadables.index');
    }

}
