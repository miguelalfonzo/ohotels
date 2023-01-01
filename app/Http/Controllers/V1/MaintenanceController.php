<?php
namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Maintenance;


class MaintenanceController extends Controller
{
   

  


    protected function options(Request $request)
    {
        


        $data = $request->only('type');

        $validator = Validator::make($data, [
            'type' => 'required|numeric',
           
            
        ]);
        
        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','validator fails',$validator->messages());

            return response()->json($middleRpta, 400);
        }
      
    
        $list = Maintenance::options($request);

        $middleRpta = $this->setRpta('ok','success list',$list);

        return response()->json($middleRpta,Response::HTTP_OK);

    }
    
    
   
   
    
}