<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;


class Maintenance extends Model
{
    use HasFactory;

   
   protected static function options($request){


   		
   		$type  = $request->type;
   		
    	$list  = DB::select('CALL sp_pms_get_select_maintenance (?)', array($type));

    	return $list;
    
    }

    
    
    
}
