<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\ProductsController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BookingController;
use App\Http\Controllers\V1\MaintenanceController;
use App\Http\Controllers\V1\PaymentsController;
use App\Http\Controllers\V1\PmsController;



Route::prefix('v1')->group(function () {

	

	  Route::group(['prefix' => 'payments'], function () {

       
	 	Route::get('culqui', [PaymentsController::class, 'culqui']);

		Route::post('culquiPayment', [PaymentsController::class, 'culquiPayment']);

		Route::get('paypal', [PaymentsController::class, 'paypal']);

		Route::get('paypalPayment', [PaymentsController::class, 'paypalPayment']);
    });
	

	 Route::group(['prefix' => 'booking'], function () {

	 	Route::get('search', [BookingController::class, 'search']);

	 	Route::get('viewAll', [BookingController::class, 'viewAll']);

	 	Route::get('coupon', [BookingController::class, 'coupon']);

	 	Route::post('create', [BookingController::class, 'create']);

	 	Route::put('confirm', [BookingController::class, 'confirm']);

	 	Route::delete('delete', [BookingController::class, 'delete']);
		
    });


	  Route::group(['prefix' => 'maintenance'], function () {

        
	 	Route::get('options', [MaintenanceController::class, 'options']);
		
    });



	   Route::group(['prefix' => 'pms'], function () {

	   		Route::prefix('dashboard')->group(function () {
	
	   			Route::get('today', [PmsController::class, 'dashboardToday']);

	 			Route::get('indicators', [PmsController::class, 'dashboardIndicators']);

	
			});


			Route::prefix('booking')->group(function () {
	
	   			Route::get('editDates', [PmsController::class, 'editDates']);

	   			Route::post('saveEditDates', [PmsController::class, 'saveEditDates']);
	 			

	
			});
       
	 	
		
    	});

	 

	



    Route::post('login', [AuthController::class, 'authenticate']);
    
    Route::post('register', [AuthController::class, 'register']);
    

    Route::group(['middleware' => ['jwt.verify']], function() {
      
      //verificación 
        
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('get-user', [AuthController::class, 'getUser']);
      
    });
});