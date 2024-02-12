<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

/* CHECK IF USER IS LOGGED IN */
Route::get('check_logged_in', 'LockController@check_logged_in')->name('check_logged_in');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index')->name('home');

    /* USER MANAGEMENT ROUTES */
    Route::get('users/datatable', ['as' => 'users.datatable', 'uses' => 'UserController@datatable']);
    Route::post('users/regions', 'UserController@region_code')->name('users.regions.region_code');
    Route::post('users/municipalities', 'UserController@municipalities')->name('users.municipalities');
    Route::get('users/add_roles/{user_id}', 'UserController@add_roles')->name('users.roles.add');
    Route::post('users/store_roles', 'UserController@store_roles')->name('users.roles.store');
    Route::post('users/destroy_roles', 'UserController@destroy_roles')->name('users.roles.destroy');
    Route::get('users/change_password/{user_id}', 'UserController@change_password')->name('users.change_password');
    Route::post('users/change_password', 'UserController@store_password')->name('users.change_password.store');
    Route::post('users/deactivate', 'UserController@deactivate')->name('users.deactivate');
    Route::post('users/activate', 'UserController@activate')->name('users.activate');
    Route::post('users/force_logout', 'UserController@force_logout')->name('users.force_logout');
    Route::resource('users', 'UserController');
    /* END OF USER MANAGEMENT ROUTES */

    /* ROLES ROUTES */
    Route::get('roles/datatable', ['as' => 'roles.datatable', 'uses' => 'RoleController@datatable']);
    Route::resource('roles', 'RoleController');
    /* END OF ROLES ROUTES */

    /* PERMISSIONS ROUTES */
    Route::get('permissions/datatable', ['as' => 'permissions.datatable', 'uses' => 'PermissionController@datatable']);
    Route::resource('permissions', 'PermissionController');
    /* END OF PERMISSIONS ROUTES */

    /* PENDING REGISTRATION ROUTES */
    Route::get('pending_registrations/datatable', 'PendingRegistrationController@datatable')->name('pending_registrations.datatable');
    Route::get('pending_registrations/disapprove/{id}', 'PendingRegistrationController@disapprove')->name('pending_registrations.disapprove');
    Route::put('pending_registrations/update_disapprove/{id}', 'PendingRegistrationController@update_disapprove')->name('pending_registrations.update_disapprove');
    Route::resource('pending_registrations', 'PendingRegistrationController');
    /* END OF PENDING REGISTRATION ROUTES */

    /* SYSTEMS ROUTES */
    Route::get('systems/datatable', 'SystemController@datatable')->name('systems.datatable');
    Route::resource('systems', 'SystemController');
    /* END OF SYSTEMS ROUTES */

    /* AFFILIATION ROUTES */
    Route::get('affiliations/datatable', 'AffiliationController@datatable')->name('affiliations.datatable');
    Route::resource('affiliations', 'AffiliationController');
    /* END OF AFFILIATION ROUTES */

    /* PHILRICE STATION ROUTES */
    Route::get('philrice_stations/datatable', 'StationController@datatable')->name('philrice_stations.datatable');
    Route::resource('philrice_stations', 'StationController');
    /* END OF PHILRICE STATIONS ROUTES */

    /* ACTIVITIES ROUTES */
    Route::get('activities/datatable', 'ActivityController@datatable')->name('activities.datatable');
    Route::resource('activities', 'ActivityController');
    /* END OF ACTIVITIES ROUTES */

    /* PAGES ROUTES */
    Route::get('pages/datatable', 'PageController@datatable')->name('pages.datatable');
    Route::post('pages/publish', 'PageController@publish')->name('pages.publish');
    Route::resource('pages', 'PageController');
    /* END OF PAGES ROUTES */

    /* SECTION ROUTES */
    Route::get('sections/datatable', 'SectionController@datatable')->name('sections.datatable');
    Route::post('sections/publish', 'SectionController@publish')->name('sections.publish');
    Route::resource('sections', 'SectionController');
    /* END OF SECTION ROUTES */

    /* CONTENTS ROUTES */
    Route::get('contents/datatable', 'ContentController@datatable')->name('contents.datatable');
    Route::post('contents/publish', 'ContentController@publish')->name('contents.publish');
    Route::post('contents/sections', 'ContentController@sections')->name('contents.sections');
    Route::resource('contents', 'ContentController');
    /* END OF CONTENTS ROUTES */

    /* CONTACTS ROUTES */
    Route::get('contacts/datatable', 'ContactController@datatable')->name('contacts.datatable');
    Route::resource('contacts', 'ContactController');
    /* END OF CONTACTS ROUTES */

    /* LINKS ROUTES */
    Route::get('links/datatable', 'LinkController@datatable')->name('links.datatable');
    Route::resource('links', 'LinkController');
    /* END OF LINKS ROUTES */

    /* PARTNERS ROUTES */
    Route::get('partners/datatable', 'PartnerController@datatable')->name('partners.datatable');
    Route::resource('partners', 'PartnerController');
    /* END OF PARTNERS ROUTES */

    /* SLIDERS ROUTES */
    Route::get('sliders/datatable', 'SliderController@datatable')->name('sliders.datatable');
    Route::resource('sliders', 'SliderController');
    /* END OF SLIDERS ROUTES */

    /* AUTO RESPONSE ROUTES */
    Route::get('auto_response/datatable', 'AutoResponseController@datatable')->name('auto_response.datatable');
    Route::post('auto_response/enable', 'AutoResponseController@enable')->name('auto_response.enable');
    Route::resource('auto_response', 'AutoResponseController');
    /* END OF AUTO RESPONSE ROUTES */

    /* RECEIVERS ROUTES */
    Route::get('receivers/datatable', 'ReceiverController@datatable')->name('receivers.datatable');
    Route::get('receivers/users', 'ReceiverController@users')->name('receivers.users');
    Route::resource('receivers', 'ReceiverController');
    /* END OF RECEIVERS ROUTES */

    /* INQUIRIES ROUTES */
    Route::get('inquiries/datatable', 'InquiryController@datatable')->name('inquiries.datatable');
    Route::get('inquiries', 'InquiryController@index')->name('inquiries.index');
    Route::get('inquiries/{inquiry}', 'InquiryController@show')->name('inquiries.show');
    Route::get('inquiries/create_response/{inquiry}', 'InquiryController@create_response')->name('inquiries.create_response');
    Route::post('inquiries/send_response', 'InquiryController@send_response')->name('inquiries.send_response');
    /* END OF INQUIRIES ROUTES */

    /* DOWNLOADABLE CATEGORIES ROUTES */
    Route::get('downloadable_categories/datatable', ['as' => 'downloadable_categories.datatable', 'uses' => 'DownloadableCategoriesController@datatable']);
    Route::post('downloadable_categories/publish', ['as' => 'downloadable_categories.publish', 'uses' => 'DownloadableCategoriesController@publish']);
    Route::resource('downloadable_categories', 'DownloadableCategoriesController');
    /* END OF DOWNLOADABLE CATEGORIES ROUTES */

    /* DOWNLOADABLE ROUTES */
    Route::get('downloadables/datatable', ['as' => 'downloadables.datatable', 'uses' => 'DownloadableController@datatable']);
    Route::post('downloadables/publish', ['as' => 'downloadables.publish', 'uses' => 'DownloadableController@publish']);
    Route::post('downloadables/unpublish', ['as' => 'downloadables.unpublish', 'uses' => 'DownloadableController@unpublish']);
    Route::resource('downloadables', 'DownloadableController');
    /* END OF DOWNLOADABLE ROUTES */

    /* LOCKSCREEN */
    Route::post('unlock', 'LockController@unlock')->name('unlock');

    /*Monitoring Module Routes*/
    Route::post('monitoring/datatable','MonitoringController@datatable')->name('monitoring.datatable');
    Route::resource('/monitoring','MonitoringController');

    Route::get('/api-dashboard', 'ApiController@index')->name('api.index');
    Route::get('/api-dashboard/send_summary','ApiController@send_summary');
    Route::get('/api-dashboard/recipientDatatable','ApiController@recipientDatatable');
    Route::post('api-dashboard/datatable','ApiController@datatable')->name('api.datatable');
    Route::post('api-dashboard/getActiveSg','ApiController@getActiveSeedGrower')->name('api.getActiveSg');
    Route::post('/api-dashboard/lineChartData','ApiController@lineChartData')->name('api.lineChartData');
    //Route::post('api-dashboard/','ApiController@store')->name('api.store');
    Route::post('api-dashboard/viewApiDetail','ApiController@viewApiDetail')->name('api.viewApiDetail');

    // User Data Monitoring
    Route::get('/user_data_monitoring', ['as' => 'user_data_monitoring.index', 'uses' => 'UserDataMonitoringController@index']);
    Route::get('/user_data_monitoring/show_registration_data', ['as' => 'user_data_monitoring.show_registration_data', 'uses' => 'UserDataMonitoringController@show_registration_data']);
    Route::post('/user_data_monitoring/all_data_datatable', ['as' => 'user_data_monitoring.allDataDatatable', 'uses' => 'UserDataMonitoringController@all_data_datatable']);

    Route::post('api-dashboard/','ApiController@storeEmailRecipient')->name('email_recipient.store');
    
    Route::get('api-dashboard/test/','ApiController@send_summary');

    /* REGISTRATION NOTIFICATION RECEIVERS ROUTES */
    Route::get('reg_notif_receivers/datatable', 'RegNotifReceiverController@datatable')->name('reg_notif_receivers.datatable');
    Route::get('reg_notif_receivers/users', 'RegNotifReceiverController@users')->name('reg_notif_receivers.users');
    Route::resource('reg_notif_receivers', 'RegNotifReceiverController');
    /* END OF REGISTRATION NOTIFICATION RECEIVERS ROUTES */

    /* DATA COMPLIANCE RECEIVERS ROUTES */
    Route::get('data_compliance_receivers/datatable', 'DataComplianceReceiverController@datatable')->name('data_compliance_receivers.datatable');
    Route::resource('data_compliance_receivers', 'DataComplianceReceiverController');
    /* END OF DATA COMPLIANCE RECEIVERS ROUTES */

    /* SEED INVENTORY RECEIVERS ROUTES */
    Route::get('seed_inventory_receivers/datatable', 'SeedInventoryReceiverController@datatable')->name('seed_inventory_receivers.datatable');
    Route::resource('seed_inventory_receivers', 'SeedInventoryReceiverController');
    /* END OF SEED INVENTORY RECEIVERS ROUTES */

    /* USER MONITORING ROUTES */
    Route::get('user_monitor/last_login', 'UserMonitorController@users_last_login')->name('user_monitor.last_login');
    /* END OF USER MONITORING ROUTES */

});
