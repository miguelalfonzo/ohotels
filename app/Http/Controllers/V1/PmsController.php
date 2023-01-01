<?php
namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Pms;
use Carbon\Carbon;
use DB;
class PmsController extends Controller
{
   

  

    

    protected function dashboardToday(Request $request)
    {
        


        $data = $request->only('hotel','type');

        $validator = Validator::make($data, [
            'hotel' => 'required|numeric',
            'type' => 'required|numeric',
           
            
        ]);
        
        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','validator fails',$validator->messages());

            return response()->json($middleRpta, 400);
        }
      
    
        $list = Pms::dashboardToday($request);

        $middleRpta = $this->setRpta('ok','success list',$list);

        return response()->json($middleRpta,Response::HTTP_OK);

    }
    
    
   protected function dashboardIndicators(Request $request)
    {
        


        $data = $request->only('hotel');

        $validator = Validator::make($data, [
            'hotel' => 'required|numeric',
           
           
            
        ]);
        
        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','validator fails',$validator->messages());

            return response()->json($middleRpta, 400);
        }
      
    
        $list = Pms::dashboardIndicators($request);

        $middleRpta = $this->setRpta('ok','success list',$list);

        return response()->json($middleRpta,Response::HTTP_OK);

    }

    protected function editDates(Request $request){

        $data = $request->only('hotel','checkIn','checkOut','type','idRoomOrIdBed','bookingId');

        $validator = Validator::make($data, [
            'hotel' => 'required|numeric',
            'checkIn'=> 'required|date',
            'checkOut'=> 'required|date|after:checkIn',
            'bookingId'=>'required|numeric',
            'type'=> 'required|string',
            'idRoomOrIdBed'=> 'required|numeric'
           
            
        ]);

        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','validator fails',$validator->messages());

            return response()->json($middleRpta, 400);
        }


        $list = $this->setTemporalListRatesDates($request);

        $rpta = $this->setRpta('ok','success response',$list);

        return response()->json($rpta,Response::HTTP_OK); 

    }

    protected function setRangoDates($request){

        $from = Carbon::parse($request->checkIn);

        $to   = Carbon::parse($request->checkOut);

        $diff = $from->diffInDays($to);

        $dates = [];

        $ini  = $from ;

        $ini = $ini->subDays(1);

        for($i = 0; $i < $diff+1; ++$i) {
       
            $ini = $ini->addDays(1);

            $dates[] = $ini->format('Y-m-d');
        }

        return $dates;

    }


    protected function setTemporalListRatesDates($request){

        
         
         $oldList = [];

         $temporal =[];

         $listOld = Pms::getListRatesHistoryOld($request);

         foreach($listOld as $list){

            $oldList[$list->Date]=$list->Price;

         }

         //partimos de 10 en 10 todo el rango de fechas , el mysql no puede retonar una cadena muy grande para la funcion fn_pms_temp_hist_book_dates, se enviara por lotes

         $datesAll = $this->setRangoDates($request);

         $datesAll = array_chunk($datesAll,10);

         
         foreach($datesAll as $values){

            $fromDate = current($values); //primer valor a enviar

            $toDate = end($values); //ultimo valor a enviar


            $listNew = Pms::getListRatesHistoryNew($request,$fromDate,$toDate);

         


            if(!empty($listNew)){

                $split = explode("&", $listNew);

                foreach($split as $list){

                    $subString = explode("|", $list);

                    $date = $subString[0];

                    $price_new = $subString[1];

                    //si existe un antiguo precio 

                    $price = (isset($oldList[$date]))?$oldList[$date]:$price_new;

                    $temporal[$date] = $price;

                }

            }
            

         }

        
         
         
         return $temporal;


    }
   
    protected function saveEditDates(Request $request){

        try {

            DB::beginTransaction();
            
            $data = $request->only('hotel','user','type','idRoomOrIdBed','bookingId','list');

            $validator = Validator::make($data, [
                'hotel' => 'required|numeric',
                'user'=> 'required|numeric',
                'bookingId'=>'required|numeric',
                'type'=> 'required|string',
                'idRoomOrIdBed'=> 'required|numeric',
                'list'=>'required'
               
                
            ]);

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','validator fails',$validator->messages());

                return response()->json($middleRpta, 400);
            }

           


            $hotel         = $request->hotel;
            $user          = $request->user;
            $bookingId     = $request->bookingId;
            $type          = trim($request->type);
            $idRoomOrIdBed = $request->idRoomOrIdBed;

            $now = Carbon::now()->format('Y-m-d H:i:s');

           
            DB::delete("DELETE FROM tblbookinghistoryprices WHERE HotelId=? AND BookingId=? AND RoomIdOrBedId=? AND Type=?",array($hotel ,$bookingId ,$idRoomOrIdBed ,$type ));

            foreach($request->list  as $values ){

                $date = $values["date"];
                
                $price = $values["price"];

                DB::insert("INSERT INTO tblbookinghistoryprices(HotelId,BookingId,RoomIdOrBedId,Type,Date,Price,CreatedAt,CreatedBy) VALUES(?,?,?,?,?,?,?,?)",array($hotel,$bookingId,$idRoomOrIdBed,$type,$date,$price,$now,$user));
            }

          
             DB::commit();

             return response()->json($this->setRpta('ok','success created history ',[]),Response::HTTP_OK);

        } catch (\Exception $e) {
            
            DB::rollBack();

           
            return response()->json($this->setRpta('error','transact : '.$e->getMessage(),[]), 400);
        }



        

        

    }
}