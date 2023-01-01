<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;


class Booking extends Model
{
    use HasFactory;

    protected static function validateCoupon($coupon,$hotel){

    	$list  = DB::select('CALL sp_web_validate_coupon (?,?)', array($coupon,$hotel));

    	return $list;

    }


    protected static function update_temporary_book(){

    	DB::update('CALL sp_web_update_temporary_book', array());

    }
    protected static function confirmPayBooking($hotel,$id,$user,$type,$reference,$description,$applyIgv,$coupon,$amount){


    	DB::update('CALL sp_web_confirm_pay_booking (?,?,?,?,?,?,?,?,?)', array($hotel,$id,$user,$type,$reference,$description,$applyIgv,$coupon,$amount));


    }


   protected static function deleteBooking($hotel,$id){


   	DB::delete('CALL sp_web_delete_booking (?,?)', array($hotel,$id));

   }

   protected static function searchRooms($request){


   		
   		$hotel     = $request->hotel;
   		$checkIn   = Carbon::parse($request->checkIn)->format('Y-m-d');
   		$checkOut  = Carbon::parse($request->checkOut)->format('Y-m-d');
   		$lang      = $request->lang;


    	$list  = DB::select('CALL sp_web_available_typeroom (?,?,?,?)', array($hotel,$checkIn,$checkOut,$lang));

    	return $list;
    
    }


    protected static function searchBeds($request){


   		
   		$hotel    = $request->hotel;
   		$checkIn  = Carbon::parse($request->checkIn)->format('Y-m-d');
   		$checkOut = Carbon::parse($request->checkOut)->format('Y-m-d');
   		$lang 	  = $request->lang;


    	$list  = DB::select('CALL sp_web_available_beds (?,?,?,?)', array($hotel,$checkIn,$checkOut,$lang));

    	return $list;
    
    }

    protected static function createUserBooking($hotel,$country,$guestFirstName,$guestLastName,$guestEmail,$guestPhone,$user){

        $list  = DB::select('CALL sp_web_create_user (?,?,?,?,?,?,?)', array($hotel,$country,$guestFirstName,$guestLastName,$guestEmail,$guestPhone,$user));

      return $list;

    }

    protected static function create($request,$ids,$holder){

    	  $hotel 			= $request->hotel ;
        $agent 			= $request->agent ;
       
        $checkIn 		= Carbon::parse($request->checkIn)->format('Y-m-d');
        $checkOut 		= Carbon::parse($request->checkOut)->format('Y-m-d');
        $dateArrival 	= Carbon::parse($request->dateArrival)->format('Y-m-d');
        $arrivalTime 	= $request->arrivalTime ;
        $specialRequest = $request->specialRequest ;
        $origen 		= $request->origen ;
        $temporary 	= $request->temporary ;
        



        $booking  = DB::select('CALL sp_web_insert_booking (?,?,?,?,?,?,?,?,?,?,?)', array(
        $hotel,$agent,$checkIn,$checkOut,$dateArrival,$arrivalTime,$specialRequest,$origen ,$ids,$temporary,$holder  ));

    	return $booking;

    }

    protected static function validateKeysInputBooking($request){

    	
   		$hotel 	= $request->hotel ;
   		$agent 	= $request->agent ;
   	
   		$origen 		= $request->origen ;
   		

    	$rpta  = DB::select('CALL sp_web_before_insert_booking (?,?,?)', array($hotel,$agent,$origen));

    	return $rpta;


    }
    
}
