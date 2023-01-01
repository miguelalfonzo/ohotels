<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;


class RoomType extends Model
{
    use HasFactory;

   
  protected static function viewAll($hotel,$lang){

   

     $list  = DB::select('CALL sp_web_get_top_rtypes (?,?)', array($hotel,$lang));

      return $list;

  }


  protected static function getPriceBaseTypeRoom($hotel,$id){



      $list  = DB::select('CALL sp_web_get_pbase_room (?,?)', array($hotel,$id));

      return (isset($list[0]->Rate)?$list[0]->Rate:'');
    
    }

    protected static function getNameTypeRoom($hotel,$id){



      $list  = DB::select('CALL sp_web_get_type_room (?,?)', array($hotel,$id));

      return (isset($list[0]->Name)?$list[0]->Name:'');
    
    }
    
}
