<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'AdminController@dashboard')->name('index');
Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
Route::get('/inventorydashboard', 'AdminController@inventorydashboard')->name('inventorydashboard');
Route::get('/viewmaps', 'AdminController@viewmaps')->name('viewmaps');
Route::get('/tickets1', 'AdminController@tickets')->name('tickets');
Route::get('/tickets', 'AdminController@tickets1')->name('tickets1');
Route::get('/tickets/create', 'AdminController@addNewTicket')->name('tickets.create');
Route::post('/tickets/store', 'AdminController@storeTicket')->name('tickets.store');
Route::get('tickets/{id}/edit', 'AdminController@editTicket')->name('tickets.edit'); 
Route::delete('/delticket/{id}', 'AdminController@DeleteTicketByAdmin')->name('delticket');

Route::PATCH('tickets/{id}', 'AdminController@updateTicket')->name('tickets.update');
Route::get('/deleteticket/{id}', 'AdminController@deleteticket')->name('deleteticket');
Route::get('/getSearchblocklist/{id}','AdminController@getSearchblocklist');
Route::get('/getSearchproviderlist/{id}','AdminController@getSearchproviderlist');
Route::get('/addtickets', 'AdminController@addtickets')->name('addtickets');
Route::get('/heatmap', 'AdminController@heatmap')->name('heatmap');
Route::get('/employee-pdf-report/{id}/{fromDate}/{toDate}', 'AdminController@generateEmployeePDFReport')->name('employee-pdf-report');
Route::get('/attendance_dashboard', 'AdminController@AttendanceDashboard')->name('attendance_dashboard');
Route::get('attendance_list', 'AdminController@PatrollerAttendanceList')->name('attendance_list');
Route::get('/attendance-export', 'AdminController@exportAttendance')->name('attendance-export');
Route::get('/staff_details/{id}','AdminController@StaffView')->name('staff_details'); 
Route::get('/staff_livetrack/{id}','AdminController@showLocationHistory')->name('staff_livetrack'); 
Route::get('/employee_tracking_data/{id}','AdminController@getTrackingData')->name('employee_tracking_data');
Route::get('/staff_track/{id}','AdminController@StaffTrack')->name('staff_track'); 

Route::get('/currentlocation/{id}', 'AdminController@currentlocation')->name('currentlocation');
Route::get('/attendance', 'AdminController@attendance')->name('attendance');
Route::get('/userattendance', 'AdminController@userattendance')->name('userattendance');

Route::get('/trackattendance', 'AdminController@trackattendance')->name('trackattendance');
Route::get('/tracklocations', 'AdminController@tracklocations')->name('tracklocations');

Route::get('/reports', 'Resource\GPResource@gpreports')->name('reports');
Route::get('/get_completion_trend', 'Resource\GPResource@getCompletionTrend')->name('get_completion_trend');


Route::get('/reportattendance', 'AdminController@attendancereport')->name('reportattendance');
Route::get('/todayattendancereport','AdminController@todayattendancereport')->name('todayattendancereport');
Route::get('/patrollertickets','AdminController@patrollertickets')->name('patrollertickets');


Route::get('/occ', 'AdminController@occ')->name('occ');
Route::get('/frt', 'AdminController@frt')->name('frt');
Route::get('/zonalincharge', 'AdminController@zonalincharge')->name('zonalincharge');
Route::get('/districtincharge', 'AdminController@districtincharge')->name('districtincharge');

Route::get('/translation',  'AdminController@translation')->name('translation');

Route::get('/download/{id}', 'AdminController@download')->name('download');

Route::group(['as' => 'dispatcher.', 'prefix' => 'dispatcher'], function () {
	Route::get('/', 'DispatcherController@index')->name('index');
	Route::post('/', 'DispatcherController@store')->name('store');
    Route::get('/trips', 'DispatcherController@trips')->name('trips');
    Route::get('/cancelled', 'DispatcherController@cancelled')->name('cancelled');
	Route::get('/cancel', 'DispatcherController@cancel')->name('cancel');
	Route::get('/trips/{trip}/{provider}', 'DispatcherController@assign')->name('assign');
	Route::get('/users', 'DispatcherController@users')->name('users');
	Route::get('/providers', 'DispatcherController@providers')->name('providers');
    Route::get('/assignform/{id}', 'DispatcherController@assignform')->name('assignform');
    Route::post('/sendassignrequest', 'DispatcherController@sendassignrequest')->name('sendassignrequest');
    Route::get('/completeform/{id}', 'DispatcherController@completeform')->name('completeform');
    Route::post('/closerequest', 'DispatcherController@closerequest')->name('closerequest');
    Route::get('/onholdform/{id}', 'DispatcherController@onholdform')->name('onholdform');
    Route::post('/onholdrequest', 'DispatcherController@onholdrequest')->name('onholdrequest');



});

Route::resource('user', 'Resource\UserResource');
Route::resource('dispatch-manager', 'Resource\DispatcherResource');
Route::resource('account-manager', 'Resource\AccountResource');
Route::resource('fleet', 'Resource\FleetResource');
Route::resource('provider', 'Resource\ProviderResource');
Route::resource('document', 'Resource\DocumentResource');
Route::resource('service', 'Resource\ServiceResource');
Route::resource('promocode', 'Resource\PromocodeResource');

Route::group(['as' => 'provider.'], function () {
    Route::get('review/provider', 'AdminController@provider_review')->name('review');
    Route::get('provider/{id}/approve', 'Resource\ProviderResource@approve')->name('approve');
    Route::get('provider/{id}/disapprove', 'Resource\ProviderResource@disapprove')->name('disapprove');
    Route::get('provider/{id}/request', 'Resource\ProviderResource@request')->name('request');
    Route::get('provider/{id}/statement', 'Resource\ProviderResource@statement')->name('statement');
    Route::resource('provider/{provider}/document', 'Resource\ProviderDocumentResource');  
    Route::delete('provider/{provider}/service/{document}', 'Resource\ProviderDocumentResource@service_destroy')->name('document.service');
    Route::post('provider/leave','Resource\ProviderResource@HandleLeaves')->name('leave');
    Route::delete('provider/del-leaves/{id}','Resource\ProviderResource@DeleteLeaves')->name('del-leaves');


});
Route::get('tracking/provider', 'Resource\ProviderResource@tracking_provider')->name('tracking.provider');

Route::get('review/user', 'AdminController@user_review')->name('user.review');
Route::get('user/{id}/request', 'Resource\UserResource@request')->name('user.request');

Route::get('map', 'AdminController@map_index')->name('map.index');
Route::get('map/ajax', 'AdminController@map_ajax')->name('map.ajax');
Route::get('map/fetch-gps-data', 'AdminController@fetchGPSData')->name('map.fetch-gps-data');
Route::get('/get-map-districts', 'AdminController@getMapDistricts')->name('get-map-districts');
Route::get('/get-map-blocks','AdminController@getMapBlocks')->name('get-map-blocks');
Route::get('trackmap/ajax', 'AdminController@trackmap_ajax')->name('trackmap.ajax');
Route::get('alltrackmap/ajax', 'AdminController@alltrackmap_ajax')->name('alltrackmap.ajax');


Route::get('site/settings', 'AdminController@settings')->name('settings');
Route::post('settings/store', 'AdminController@settings_store')->name('settings.store');
Route::get('settings/payment', 'AdminController@settings_payment')->name('settings.payment');
Route::post('settings/payment', 'AdminController@settings_payment_store')->name('settings.payment.store');

Route::get('profile', 'AdminController@profile')->name('profile');
Route::post('profile', 'AdminController@profile_update')->name('profile.update');

Route::get('password', 'AdminController@password')->name('password');
Route::post('password', 'AdminController@password_update')->name('password.update');

Route::get('payment', 'AdminController@payment')->name('payment');

Route::get('request/ongoing/{id}', 'AdminController@ongoing')->name('ongoing');

// statements

Route::get('/statement', 'AdminController@statement')->name('ride.statement');
Route::get('/statement/provider', 'AdminController@statement_provider')->name('ride.statement.provider');
Route::get('/statement/range', 'AdminController@statement_range')->name('ride.statement.range');
Route::get('/statement/today', 'AdminController@statement_today')->name('ride.statement.today');
Route::get('/statement/monthly', 'AdminController@statement_monthly')->name('ride.statement.monthly');
Route::get('/statement/yearly', 'AdminController@statement_yearly')->name('ride.statement.yearly');

//transactions
Route::get('/transactions', 'AdminController@transactions')->name('transactions');
Route::get('transfer/provider', 'AdminController@transferlist')->name('providertransfer');
Route::get('transfer/fleet', 'AdminController@transferlist')->name('fleettransfer');
Route::get('/transfer/{id}/approve', 'AdminController@approve')->name('approve');
Route::get('/transfer/cancel', 'AdminController@requestcancel')->name('cancel');
Route::get('transfer/{id}/create', 'AdminController@transfercreate')->name('transfercreate');
Route::get('transfer/search', 'AdminController@search')->name('transfersearch');
Route::post('transfer/store', 'AdminController@transferstore')->name('transferstore');


// Static Pages - Post updates to pages.update when adding new static pages.

Route::get('/help', 'AdminController@help')->name('help');
Route::get('/send/push', 'AdminController@push')->name('push');
Route::post('/send/push', 'AdminController@send_push')->name('send.push');
Route::get('/pages', 'AdminController@cmspages')->name('cmspages');
Route::post('/pages', 'AdminController@pages')->name('pages.update');
Route::get('/pages/search/{types}','AdminController@pagesearch');
Route::resource('requests', 'Resource\TripResource');
Route::get('scheduled', 'Resource\TripResource@scheduled')->name('requests.scheduled');
Route::get('tickets-resolved', 'Resource\TripResource@resolved')->name('requests.resolved');
Route::get('tickets-pending', 'Resource\TripResource@pending')->name('requests.pending');
Route::get('tickets-onhold', 'Resource\TripResource@onhold')->name('requests.onhold');
Route::get('tickets-ongoing', 'Resource\TripResource@ongoing')->name('requests.ongoing');
Route::get('todayongoing-tickets', 'Resource\TripResource@todayongoing')->name('requests.todayongoing');
Route::get('yesterdayclosed-tickets', 'Resource\TripResource@yesterdayclosed')->name('requests.yesterdayclosed');
Route::get('todayclosed-tickets', 'Resource\TripResource@todayclosed')->name('requests.todayclosed');
Route::get('notstarted-tickets', 'Resource\TripResource@notstarted')->name('requests.notstarted');
Route::get('tickets-ups', 'Resource\TripResource@ups')->name('requests.ups');
Route::get('tickets-fiber', 'Resource\TripResource@fiber')->name('requests.fiber');
Route::get('tickets-poles', 'Resource\TripResource@poles')->name('requests.poles');
Route::get('tickets-electronics', 'Resource\TripResource@electronics')->name('requests.electronics');


Route::get('push', 'AdminController@push_index')->name('push.index');
Route::post('push', 'AdminController@push_store')->name('push.store');
Route::get('sendnotification', 'AdminController@sendpushnotifications')->name('sendnotification');

Route::get('/ajax-blocks/{id}','AdminController@getajaxblocks');
Route::get('/ajax-blocks-id/{id}','AdminController@getajaxblockids');
Route::get('/ajax-blocks-providers/{id}','AdminController@getajaxproviderblocks');
Route::get('/ajax-gps/{id}','AdminController@getajaxgps');

Route::post('searchproviders','AdminController@searchproviders')->name('searchproviders');


Route::get('/dispatch', function () {
    return view('admin.dispatch.index');
});

Route::get('/cancelled', function () {
    return view('admin.dispatch.cancelled');
});

Route::get('/ongoing', function () {
    return view('admin.dispatch.ongoing');
});

Route::get('/schedule', function () {
    return view('admin.dispatch.schedule');
});

Route::get('/add', function () {
    return view('admin.dispatch.add');
});

Route::get('/assign-provider', function () {
    return view('admin.dispatch.assign-provider');
});

Route::get('/dispute', function () {
    return view('admin.dispute.index');
});

Route::get('/dispute-create', function () {
    return view('admin.dispute.create');
});

Route::get('/dispute-edit', function () {
    return view('admin.dispute.edit');
});
Route::resource('parts', 'Resource\PartsResource');
Route::resource('inventory', 'Resource\InventoryResource');
Route::resource('return_note', 'Resource\ReturnNoteResource');
Route::resource('material_inward', 'Resource\MaterialInwardResource');
Route::resource('material_incident', 'Resource\MaterialIncidentResource');
Route::resource('material_issue', 'Resource\MaterialIssueResource');
Route::resource('material_consumption', 'Resource\MaterialConsumptionResource');


Route::resource('gps', 'Resource\GPResource');// Import file 
Route::get('/import', function () {
    return view('admin.import.index');
})->name('import');
Route::post('/import/validate_data', 'AdminController@import_data')->name('import.data');
Route::post('/import/process', 'AdminController@process')->name('import.process');
Route::resource('location', 'Resource\LocationResource');
Route::group(['as' => 'location.', 'prefix' => 'location'], function () {
    Route::get('block/list', 'Resource\LocationResource@list_block')->name('block');
    Route::get('block/create', 'Resource\LocationResource@create_block')->name('block.create');
    Route::post('block/store', 'Resource\LocationResource@store_block')->name('block.store');
    Route::get('block/{id}/edit', 'Resource\LocationResource@edit_block')->name('block.edit');
    Route::patch('block/{id}', 'Resource\LocationResource@update_block')->name('block.update');
    Route::delete('block/{id}', 'Resource\LocationResource@destroy_block')->name('block.destroy');
});
Route::get('schedulers', 'AdminController@list_schedules')->name('schedulers');
Route::get('schedulers/{id}/edit', 'AdminController@edit_schedules')->name('schedulers.edit');
Route::PATCH('schedulers/{id}', 'AdminController@schedule_autoassign')->name('schedulers.update');
Route::get('tickets-ongoing-intervals', 'Resource\TripResource@ongoing_intervals')->name('requests.ongoing.intervals');
Route::get('tickets-ongoing-history', 'AdminController@ticketongoinghistory')->name('tickets-ongoing-history');
Route::get('tickets-completed-history', 'AdminController@ticketcompletedhistory')->name('tickets-completed-history');
Route::get('notstartedteams', 'AdminController@notstartedteams')->name('notstartedteams');
Route::get('totalteams', 'AdminController@totalteams')->name('totalteams');
Route::get('uniqueteams', 'AdminController@uniqueteams')->name('uniqueteams');
Route::get('completedteams', 'AdminController@completedteams')->name('completedteams');
Route::get('holdteams', 'AdminController@holdteams')->name('holdteams');

Route::get('todaynotstartedteams', 'AdminController@todaynotstartedteams')->name('todaynotstartedteams');

Route::get('/teams_status', 'Resource\ProviderFleetResource@teams_status')->name('teams_status');

//Route::get('/dashboard-test', 'Resource\ProviderFleetResource@dashboard')->name('dashboard-test');
//Route::get('/dashboard-test', 'Resource\ProviderResource@index_new')->name('dashboard-test');


//main dashboard
Route::get('get_dashboard_data', 'AdminController@getDashboardData')->name('get_dashboard_data');
Route::get('get_teams_data', 'AdminController@getTeamStatus')->name('get_teams_data');
Route::get('get_dashboard_map', 'AdminController@dashboardMap')->name('get_dashboard_map');

Route::get('/provider-export', 'AdminController@exportProviders')->name('provider-export');

//Reports
Route::get('workforce', 'AdminController@dashboard_workforce')->name('workforce');
Route::get('dailyrepots', 'Resource\GPResource@frtreports')->name('dailyrepots');
Route::get('get_todayfrtreport', 'Resource\GPResource@gettodayFrtReport')->name('get_todayfrtreport');
//Route::get('workforce_details', 'Resource\GPResource@getFrtReport')->name('workforce_details');
Route::get('get_frtreport', 'Resource\GPResource@getFrtReport')->name('get_frtreport');
Route::get('get_todayfrt_details', 'Resource\GPResource@getTodayFrtDetails')->name('get_todayfrt_details');
Route::get('workforce_details', 'Resource\GPResource@frtreports_details')->name('workforce_details');
Route::get('get_districts/{zone_id}', 'Resource\GPResource@get_districts')->name('get_districts');
Route::get('get_employees/{districtId}', 'Resource\GPResource@get_employees')->name('get_employees');
Route::get('get_blocks/{districtId}', 'Resource\GPResource@getBlocks')->name('get_blocks');

Route::get('get_sub_categories/{categoryId}', 'Resource\GPResource@getSubCategories')->name('get_sub_categories');
Route::get('gp_mapping', 'Resource\GPResource@gp_mapping')->name('gp_mapping');
Route::post('gp_mapping', 'Resource\GPResource@gp_mapping_update')->name('gp_mapping_update');
Route::get('track', 'Resource\GPResource@tracking')->name('track');
Route::get('track_data/{id}','Resource\GPResource@getTrackingData')->name('track_data');
Route::get('/ongoing_ticket_data', 'Resource\GPResource@ongoingTicketData')->name('ongoing_ticket_data');
Route::get('/ongoing_ticket_range', 'Resource\GPResource@getRangPickupTickets')->name('ongoing_ticket_range');
Route::get('ongoing_tt_details', 'Resource\GPResource@ongoing_details')->name('ongoing_tt_details');


Route::get('/dashboard-test', 'AdminController@dashboard_test')->name('dashboard-test');

Route::get('/velocity', 'AdminController@velocityTrend')->name('velocity');
Route::get('/district-heatmap', 'AdminController@districtHeatmap')->name('district-heatmap');



Route::resource('olt-locations', "OltLocationController");
Route::get('/uptime', "OltLocationController@UptimeMng")->name('uptime');
Route::get('/ont_data', "OltLocationController@OntData")->name('ont_data');
Route::get('/uptime_data', "OltLocationController@OntDataList")->name('uptime_data');
Route::get('/olt_dashboard', "OltLocationController@OltData")->name('olt_dashboard');
Route::get('/olt_performance', "OltLocationController@OltDataList")->name('olt_performance');
Route::get('/samriddh_dashboard', "OltLocationController@SamriddhData")->name('samriddh_dashboard');
Route::get('/samriddh_analytics', "OltLocationController@SamriddhDataList")->name('samriddh_analytics');
Route::post('upload-otdr-images', 'AdminController@uploadImages')->name('upload-otdr-images');



Route::post('/ont-upload', "OltLocationController@OntUpload")->name('ont-upload');
Route::post('/olt-upload', "OltLocationController@OltUpload")->name('olt-upload');


Route::get('/olt-export', 'OltLocationController@ExportOlt')->name('olt-export');


Route::get('/blocks', "OltLocationController@getBlocks")->name('blocks');

Route::get('/php-test', 'AdminController@php_test')->name('php-test');


Route::get('/joint_enclouser_reports', 'Resource\GPResource@joint_enclouser_reports')->name('joint_enclouser_reports');

Route::get('/joint_enclouser_tickets', 'Resource\GPResource@joint_enclouser_tickets')->name('joint_enclouser_tickets');

Route::get('/joint_enclosure_download', 'Resource\GPResource@joint_enclosure_download')->name('joint_enclosure_download');
Route::get('/joint_enclouser_img_download', 'Resource\GPResource@jointEnclosureDownload')->name('joint_enclouser_img_download');




Route::get('/unassigned_role', function () {
    return view('admin.unassigned_roles');
});

// -------------------Materials--------------------------------------
Route::get('stock-entry/get-material-details', 'InventoryMng\InventoryController@getMaterialDetails')->name('stock-entry.get-material-details');

Route::resource('materials', 'InventoryMng\MaterialController');
Route::resource('stock-entry', 'InventoryMng\InventoryController');
Route::resource('stock-issue', 'InventoryMng\StockIssueController');

Route::get('materials/{material}/get', 'InventoryMng\MaterialController@getMaterial')->name('materials.get');
Route::post('materials/bulk-delete', 'InventoryMng\MaterialController@bulkDelete')->name('materials.bulk-delete');
Route::get('get-employees', 'InventoryMng\InventoryController@getEmployees')->name('get-employees');
Route::get('emp-stock-blnc', 'InventoryMng\StockIssueController@getEmployeeBalance')->name('emp-stock-blnc');
Route::get('material-stock-blnc', 'InventoryMng\StockIssueController@getMatBalance')->name('material-stock-blnc');
Route::get('stock-report', 'InventoryMng\StockIssueController@employeeStockReport')->name('stock-report');

Route::get('/frequently_down_gps', 'InventoryMng\MaterialController@getFrequentlyDownGps')->name('frequently_down_gps');
Route::get('/get_recurring_gp_trends', 'InventoryMng\MaterialController@getRecurringGpTrends')->name('get_recurring_gp_trends');

Route::get('/frequently_down_gps_export', 'InventoryMng\MaterialController@exportFrequentlyDownGps')->name('frequently_down_gps_export');


