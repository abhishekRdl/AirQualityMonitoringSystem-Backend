<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AlertCron;
use App\Models\Device;
use App\Models\Sensor;
use App\Http\Controllers\UtilityController;

class AlertController extends Controller
{

    protected $companyCode = "";   
    
    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode();       
        
    }
    
    public function getAlertData(Request $request){
        
        $count = 0;
        
        $notifications = array();
        
        $location_id = $request->location_id;
        $branch_id = $request->branch_id;
        $facility_id = $request->facility_id;
        $building_id = $request->building_id;
        $floor_id = $request->floor_id;
        $lab_id = $request->lab_id;
        $device_id = $request->device_id;
        $sensorId = $request->sensorId;
        
        try{
            $query = Sensor::select('*');
            
            $location_id == "" ? "" : $query->where('location_id', '=', $location_id);
            
            $branch_id == "" ? "" : $query->where('branch_id', '=', $branch_id);
            
            $facility_id == "" ? "" : $query->where('facility_id', '=', $facility_id);
            
            $building_id == "" ? "" : $query->where('building_id', '=', $building_id);
            
            $floor_id == "" ? "" : $query->where('floor_id', '=', $floor_id);
            
            $lab_id == "" ? "" : $query->where('lab_id','=',$lab_id);
        
            $device_id == "" ? "" : $query->where('deviceId','=',$device_id);
            
            $data = $query->get();
            
            $length = count($data);      
            $sensorCount = 0;
            
            //SELECT * FROM `sensors` where location_id = 4 and branch_id = 3 and facility_id = 4 and building_id = 2 and floor_id = 2 and lab_id = 3 and deviceId = 3 and id = 34
            
            for($x=0;$x<$length;$x++){
                $alertQuery = DB::table('alert_crons')
                                ->join('sensors', 'sensors.id', '=', 'alert_crons.sensorId')
                                ->select(DB::raw('sensors.location_id,sensors.branch_id,sensors.facility_id,sensors.building_id,sensors.floor_id,sensors.lab_id,alert_crons.*'))
                                ->where('alert_crons.sensorTag','=',$data[$x]->sensorTag)
                                ->where('status','=','0')
                                ->where('alert_crons.companyCode','=', 'A-TEST')
                                ->first();
                
                
                if($alertQuery != null){
                    $notifications[] = $alertQuery;    
                }
                
                
            }
            
            
            $response = [
                "data"=>$notifications
            ];
            $status = 200;
        }catch(Exception $e){
            $response = [
                "error" => $e->getMessage()
            ];
            $status = 404;
        }
        
        return response($response,$status);
        
    }
    
    
    public function getAlertDatas(Request $request){
        $alertQuery = AlertCron::select('*');
        $alertQuery->where('sensorTag','=','pm2.5_gas1');
        $alertQuery->where('status','=',0);
        $data = $alertQuery->first();
        $response = [
                "data"=>$data
            ];
            $status = 200;
            return response($response,$status);
    }
}

?>