<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;


class Pms extends Model
{
    use HasFactory;

   
   protected static function dashboardToday($request){


   		
   		$hotel  = $request->hotel;
   		
   		$type  = $request->type;

    	$list  = DB::select('CALL sp_pms_get_list_bookings_today (?,?)', array($hotel,$type));

    	return $list;
    
    }

    protected static function dashboardIndicators($request){


   		
   		$hotel  = $request->hotel;
   		
   		

    	$list  = DB::select('CALL sp_pms_get_indicators_dashboard (?)', array($hotel));

    	return $list;
    
    }

    protected static function getListRatesHistoryOld($request){


      
      $hotel  = $request->hotel;
      
      $type   = trim($request->type);

      $idRoomOrIdBed  = $request->idRoomOrIdBed;

      $booking = $request->bookingId;

      $list  = DB::select('CALL sp_pms_get_list_rates_booking (?,?,?,?)', array($hotel,$type,$idRoomOrIdBed,$booking));

      return $list;
    
    }

    protected static function getListRatesHistoryNew($request,$fromDate,$toDate){


      
      $hotel  = $request->hotel;
      
      $type   = trim($request->type);

      $idRoomOrIdBed  = $request->idRoomOrIdBed;

      $checkIn  = Carbon::parse($fromDate)->format('Y-m-d');

      $checkOut  = Carbon::parse($toDate)->format('Y-m-d');

      $text  = DB::select('SELECT fn_pms_temp_hist_book_dates (?,?,?,?,?) AS result', array($hotel,$type,$idRoomOrIdBed,$checkIn,$checkOut));

      return (isset($text[0]->result))?$text[0]->result:null;
    
    }
    
    

    
}
