<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\DownloadableCategory;
use App\DownloadableCategoryActivities;
use DB, Auth, Entrust;
use Yajra\Datatables\Datatables;

class DownloadableCategoriesController extends Controller {

	public function __construct() {
        $this->middleware('permission:view_downloadable_categories')->only(['index', 'datatable']);
        $this->middleware('permission:add_downloadable_category')->only(['create', 'store']);
        $this->middleware('permission:edit_downloadable_category')->only(['edit', 'update', 'publish']);
        $this->middleware('permission:delete_downloadable_category')->only(['destroy']);
    }
    
	public function index() {
		return view('downloadable_category.index');
	}

	public function create() {
		return view('downloadable_category.create');
	}

	public function store(Request $request) {
		$this->validate($request, [
            'display_name' => 'required|unique:cms.downloadable_categories,display_name',
            'is_public' => 'required'
        ]);

        DB::beginTransaction();
        try {
        	// Insert downloadable category to database
        	$downloadableCategory = new DownloadableCategory;
        	$downloadableCategory->display_name = $request->display_name;
        	$downloadableCategory->is_public = $request->is_public;
        	$downloadableCategory->is_published = 0;
        	$downloadableCategory->save();
        	$downloadableCategoryID = $downloadableCategory->downloadable_category_id;

        	// Insert log
        	$activity = new DownloadableCategoryActivities;
        	$activity->downloadable_category_id = $downloadableCategoryID;
    		$activity->user_id = Auth::user()->user_id;
    		$activity->browser = $this->browser();
    		$activity->activity = "Added new downloadable category";
    		$activity->device = $this->device();
    		$activity->ip_env_address = $request->ip();
    		$activity->ip_server_address = $request->server('SERVER_ADDR');
    		$activity->OS = $this->operating_system();
    		$activity->save();
    		
            // Commit transaction
    		DB::commit();
    		
            // Return success message
            $request->session()->flash('success', 'Downloadable category successfully added.');
        } catch (Exception $e) {
        	DB::rollback();
    		
            // Return error message
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('downloadable_categories.index');
	}

	public function datatable() {
		// Get data
		$downloadableCategories = DownloadableCategory::get();

		$data = collect($downloadableCategories);

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
            if (Entrust::can('edit_downloadable_category')) {
                $buttons .= '<a href="'.route('downloadable_categories.edit', $data['downloadable_category_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_published'] == '' || $data['is_published'] == 0) {
                    $buttons .= '<button onclick="publish_downloadable_category('.$data['downloadable_category_id'].')" class="btn btn-success btn-sm action_buttons" title="Publish"><i class="fa fa-upload"></i> Publish</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_downloadable_category')) {
                $buttons .= '<button onclick="delete_downloadable_category('.$data['downloadable_category_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
	}

	public function publish(Request $request) {
		$downloadableCategoryID = $request->downloadableCategoryID;

		DB::beginTransaction();
		try {
			// Update downloadable category
			$downloadableCategory = DownloadableCategory::find($downloadableCategoryID);
			$downloadableCategory->is_published = 1;
			$downloadableCategory->save();

			// Insert log
        	$activity = new DownloadableCategoryActivities;
        	$activity->downloadable_category_id = $downloadableCategoryID;
    		$activity->user_id = Auth::user()->user_id;
    		$activity->browser = $this->browser();
    		$activity->activity = "Published downloadable category";
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
            // Delete downloadable category
            $downloadableCategory = DownloadableCategory::destroy($id);

            // Add log
            $activity = new DownloadableCategoryActivities;
            $activity->downloadable_category_id = $id;
            $activity->user_id = Auth::user()->user_id;
            $activity->browser = $this->browser();
            $activity->activity = "Deleted downloadable category";
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
		$data = DownloadableCategory::find($id);

		return view('downloadable_category.edit', compact(['data']));
	}

	public function update(Request $request, $id) {
		$this->validate($request, [
            'display_name' => 'required|unique:cms.downloadable_categories,display_name,'.$id.',downloadable_category_id',
            'is_public' => 'required',
        ]);

        // Updated downloadable category data
        $downloadable_category_data = array(
            'display_name' => $request->display_name,
            'is_public' => $request->is_public,
        );

        // Old downloadable category data
        $old_downloadable_category_data = array(
            'old_display_name' => $request->old_display_name,
            'old_is_public' => $request->old_is_public
        );

        DB::beginTransaction();
        try {
            // Update downloadable category
            $downloadableCategory = DownloadableCategory::find($id);
            $downloadableCategory->display_name = $downloadable_category_data['display_name'];
            $downloadableCategory->is_public = $downloadable_category_data['is_public'];
            $downloadableCategory->save();

            // Check if original value is different from update value
            // If true save as log
            foreach ($downloadable_category_data as $key => $value) {
                if ($old_downloadable_category_data['old_'.$key] != $value) {
                    $log = new DownloadableCategoryActivities;
                    $log->downloadable_category_id = $id;
                    $log->user_id = Auth::user()->user_id;
                    $log->browser = $this->browser();
                    $log->activity = "Updated downloadable category";
                    $log->device = $this->device();
                    $log->ip_env_address = $request->ip();
                    $log->ip_server_address = $request->server('SERVER_ADDR');
                    $log->old_value = $old_downloadable_category_data['old_'.$key];
                    $log->new_value = $value;
                    $log->OS = $this->operating_system();
                    $log->save();
                }
            }

            // Commit transaction
            DB::commit();

            // Return success message
            $request->session()->flash('success', 'Downloadable category successfully updated.');
        } catch (Exception $e) {
            // Rollback transcation
            DB::rollback();
            
            // Return error message
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('downloadable_categories.index');
	}

}
