<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/verify' , 'UserApiController@verify');
Route::post('/checkemail' , 'UserApiController@checkUserEmail');

Route::post('/oauth/token' , 'UserApiController@login');
Route::post('/signup' , 'UserApiController@signup');
Route::post('/logout' , 'UserApiController@logout');
Route::get('/checkapi' , 'UserApiController@checkapi');
Route::post('/checkversion' , 'UserApiController@CheckVersion');


Route::post('/auth/facebook', 		'Auth\SocialLoginController@facebookViaAPI');
Route::post('/auth/google', 		'Auth\SocialLoginController@googleViaAPI');
Route::post('/forgot/password',     'UserApiController@forgot_password');
Route::post('/reset/password',      'UserApiController@reset_password');
Route::get('/districts' , 'UserApiController@districts');
Route::get('/receivedTicketList' , 'UserApiController@receivedTicketList');
Route::get('/completeTicketList' , 'UserApiController@completeTicketList');
Route::post('/userAssignedTicketList' , 'UserApiController@userAssignedTicketList');
Route::post('/mergeTickets' , 'UserApiController@merge_tickets');
Route::post('/autoSubmitTickets' , 'UserApiController@autoSubmit');
Route::post('/ProviderRequestStatus' , 'UserApiController@ProviderRequestStatus');
Route::post('/PilotAcceptedRejected' , 'UserApiController@PilotAcceptedRejected');
Route::post('/ProviderWorkStatus' , 'UserApiController@ProviderWorkStatus');
Route::post('/savedocuments' , 	'UserApiController@savedocuments');
Route::post('/submit-files/{id}/update-joint-images', 'UserApiController@updateJointImages');
Route::get('/export_non_geotagged_images', 'UserApiController@exportNonGeotaggedImages');

Route::post('/consume_material' , 	'UserApiController@consumeMaterials');

Route::get('/assigned-materials','UserApiController@getEmployeeMaterials');
Route::post('/multiupload' , 	'UserApiController@multiupload');

Route::post('/savehistory' , 	'UserApiController@savehistory');
Route::post('/providerhistory' , 	'UserApiController@providerhistory');
Route::post('/reassign' , 	'UserApiController@Reassign');
Route::post('/dhqhistory' , 	'UserApiController@dhqhistory');

Route::group(['middleware' => ['auth:api']], function () {

	// user profile
	Route::post('/change/password' , 	'UserApiController@change_password');
	Route::post('/update/location' , 	'UserApiController@update_location');
	Route::post('/update/language' , 	'UserApiController@update_language');
	Route::get('/details' , 			'UserApiController@details');
	Route::post('/update/profile' , 	'UserApiController@update_profile');
	// services
	Route::get('/services' , 'UserApiController@services');

	
	// provider
	Route::post('/rate/provider' , 'UserApiController@rate_provider');

	// request
	Route::post('/send/request' , 	'UserApiController@send_request');
	Route::post('/cancel/request' , 'UserApiController@cancel_request');
	Route::get('/request/check' , 	'UserApiController@request_status_check');
	Route::get('/show/providers' , 	'UserApiController@show_providers');
	Route::post('/update/request' , 'UserApiController@modifiy_request');
	
	
	// history
	Route::get('/trips' , 				'UserApiController@trips');
	Route::get('upcoming/trips' , 		'UserApiController@upcoming_trips');
	Route::get('/trip/details' , 		'UserApiController@trip_details');
	Route::get('upcoming/trip/details' ,'UserApiController@upcoming_trip_details');
	// payment
	Route::post('/payment' , 	'PaymentController@payment');
	Route::post('/add/money' , 	'PaymentController@add_money');
	// estimated
	Route::get('/estimated/fare' , 'UserApiController@estimated_fare');
	// help
	Route::get('/help' , 'UserApiController@help_details');
	// promocode
	Route::get('/promocodes_list','UserApiController@list_promocode');
	Route::get('/promocodes' , 		'UserApiController@promocodes');
	Route::post('/promocode/add' , 	'UserApiController@add_promocode');
	// card payment
    Route::resource('card', 		'Resource\CardResource');
    // card payment
    Route::resource('location', 'Resource\FavouriteLocationResource');
    // passbook
	Route::get('/wallet/passbook' , 'UserApiController@wallet_passbook');
	Route::get('/promo/passbook' , 	'UserApiController@promo_passbook');

	Route::post('/test/push' , 	'UserApiController@test');

	Route::post('/chat' , 'UserApiController@chatPush');

});

Route::get('/auto_assign_tickets', 'UserApiController@auto_assign_tickets');
Route::post('/userperformance', 'UserApiController@userPerformance');
Route::post('/gpperformance', 'UserApiController@gpperformance');
Route::post('/userhistory' , 	'UserApiController@userhistory');
Route::post('/raise_ticket', 'UserApiController@raise_ticket');
Route::post('/patroller_checklist', 'UserApiController@patroller_checklist');
Route::get('/patroller_checklist/recent', 'UserApiController@patrollerChecklistRecent');
Route::post('/patroller_checklist_tracking', 'UserApiController@saveTracking');
Route::post('/createmastertickets' , 	'UserApiController@createmastertickets');
Route::post('/getticketstatus' , 	'UserApiController@getticketstatus');




Route::get('/olt-status', 'UserApiController@getOltStatus');
Route::get('/ont-status', 'UserApiController@getOntStatus');
Route::get('/ne-status', 'UserApiController@getBharatNetNeStatus');
Route::post('/ne-status', 'UserApiController@getBharatNetNeStatus');
Route::post('/ne-status/insert', 'UserApiController@insertBharatNetNeStatus');


Route::get('/get_employee_list', 'UserApiController@get_employee_list');

