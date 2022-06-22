<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\ReportsDataUtilityController;

use App\Http\Controllers\UtilityController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    protected $companyCode = ""; 
    
    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode();        
    }
    public function reportBumpTest(Request $request){
        $startDate = date("Y-m-d",strtotime($request->fromDate));
        $endDate = date("Y-m-d", strtotime($request->toDate));

        $query = DB::table('customers as c')
        ->join('locations as l', 'c.customerId', '=', 'l.companyCode')
        ->Join('branches as b', function($join){
            $join->on('l.id', '=', 'b.location_id')
                 ->on('c.customerId', '=', 'b.companyCode');
        })
        ->Join('facilities as f', function($join){
            $join->on('c.customerId', '=', 'f.companyCode')
                ->on('l.id', '=', 'f.location_id')
                ->on('b.id', '=', 'f.branch_id');
        })
        ->Join('buildings as bl', function($join){
            $join->on('c.customerId', '=', 'bl.companyCode')
                ->on('l.id', '=', 'bl.location_id')
                ->on('b.id', '=', 'bl.branch_id')
                ->on('f.id','=','bl.facility_id');
        })
        ->Join('floors as fl', function($join){
            $join->on('c.customerId', '=', 'fl.companyCode')
                ->on('l.id', '=', 'fl.location_id')
                ->on('b.id', '=', 'fl.branch_id')
                ->on('f.id','=','fl.facility_id')
                ->on('bl.id','=','fl.building_id');
        })
        ->Join('lab_departments as lb', function($join){
            $join->on('c.customerId', '=', 'lb.companyCode')
                ->on('l.id', '=', 'lb.location_id')
                ->on('b.id', '=', 'lb.branch_id')
                ->on('f.id','=','lb.facility_id')
                ->on('bl.id','=','lb.building_id')
                ->on('fl.id','=','lb.floor_id');
        })
        ->Join('devices as d', function($join){
            $join->on('c.customerId', '=', 'd.companyCode')
                ->on('l.id', '=', 'd.location_id')
                ->on('b.id', '=', 'd.branch_id')
                ->on('f.id','=','d.facility_id')
                ->on('bl.id','=','d.building_id')
                ->on('fl.id','=','d.floor_id')
                ->on('lb.id','=','d.lab_id');
        })
        ->Join('bump_test_results as btr', function($join){
            $join->on('c.customerId', '=', 'btr.companyCode')
                ->on('d.id', '=', 'btr.device_id');             
                
        })
        // ->select(DB::raw('*, DATE_FORMAT(btr.created_at,"%d-%m-%Y") as createdDate, TIME(btr.created_at) as createdTime'))
        ->select('c.customerId', 'l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.id','d.deviceName','btr.sensorTagName','btr.lastDueDate','btr.created_at')
        ->WHERE('customerId','=','A-TEST')
        ->WHERE('device_id','=',$request->device_id);
        //->WHERE('sensorTagName','=',$request->sensorTagName)



        if($startDate === $endDate){
            $query->whereDate('btr.created_at','=',$startDate); 
        }
        else{
            $query->whereBetween('btr.created_at', [$startDate, $endDate]);    
        }

        $getData = new ReportsDataUtilityController($request,$query);
            
        $response = [
             "data"=>$getData->getData()
        ];
        
        $status = 200;
        return response($response,$status);

    }


    public function alarmReport(Request $request){
        $startDate = date("Y-m-d",strtotime($request->fromDate));
        $endDate = date("Y-m-d", strtotime($request->toDate));

        $query = DB::table('customers as c')
        ->join('locations as l', 'c.customerId', '=', 'l.companyCode')
        ->Join('branches as b', function($join){
            $join->on('l.id', '=', 'b.location_id')
                 ->on('c.customerId', '=', 'b.companyCode');
        })
        ->Join('facilities as f', function($join){
            $join->on('c.customerId', '=', 'f.companyCode')
                ->on('l.id', '=', 'f.location_id')
                ->on('b.id', '=', 'f.branch_id');
        })
        ->Join('buildings as bl', function($join){
            $join->on('c.customerId', '=', 'bl.companyCode')
                ->on('l.id', '=', 'bl.location_id')
                ->on('b.id', '=', 'bl.branch_id')
                ->on('f.id','=','bl.facility_id');
        })
        ->Join('floors as fl', function($join){
            $join->on('c.customerId', '=', 'fl.companyCode')
                ->on('l.id', '=', 'fl.location_id')
                ->on('b.id', '=', 'fl.branch_id')
                ->on('f.id','=','fl.facility_id')
                ->on('bl.id','=','fl.building_id');
        })
        ->Join('lab_departments as lb', function($join){
            $join->on('c.customerId', '=', 'lb.companyCode')
                ->on('l.id', '=', 'lb.location_id')
                ->on('b.id', '=', 'lb.branch_id')
                ->on('f.id','=','lb.facility_id')
                ->on('bl.id','=','lb.building_id')
                ->on('fl.id','=','lb.floor_id');
        })
        ->Join('devices as d', function($join){
            $join->on('c.customerId', '=', 'd.companyCode')
                ->on('l.id', '=', 'd.location_id')
                ->on('b.id', '=', 'd.branch_id')
                ->on('f.id','=','d.facility_id')
                ->on('bl.id','=','d.building_id')
                ->on('fl.id','=','d.floor_id')
                ->on('lb.id','=','d.lab_id');
        })
        ->Join('alert_crons as alarm', function($join){
            $join->on('c.customerId', '=', 'alarm.companyCode')
                  ->on('d.id', '=', 'alarm.deviceId');
        })
        ->select('alarm.id','c.customerId','l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.deviceName','alarm.deviceId','alarm.sensorTag','alarm.Reason','alarm.alertType','alarm.sensorId','alarm.a_date','alarm.a_time')
                ->WHERE('customerId','=','A-TEST')
                ->WHERE('deviceId','=',$request->deviceId);
                // ->orderBy('id', 'DESC');
                if($startDate === $endDate){
                    $query->whereDate('alarm.a_date','=',$startDate); 
                }
                else{
                    $query->whereBetween('alarm.a_date', [$startDate, $endDate]);    
                }

                $getData = new ReportsDataUtilityController($request,$query);
            
                $response = [
                     "data"=>$getData->getData()["data"]
                ];
                
                $status = 200;

       return response($response,$status);
    }

    public function aqiReport(Request $request){

        $startDate = date("Y-m-d",strtotime($request->fromDate));
        $endDate = date("Y-m-d", strtotime($request->toDate));
        $query = DB::table('customers as c')
        ->join('locations as l', 'c.customerId', '=', 'l.companyCode')
        ->Join('branches as b', function($join){
            $join->on('l.id', '=', 'b.location_id')
                 ->on('c.customerId', '=', 'b.companyCode');
        })
        ->Join('facilities as f', function($join){
            $join->on('c.customerId', '=', 'f.companyCode')
                ->on('l.id', '=', 'f.location_id')
                ->on('b.id', '=', 'f.branch_id');
        })
        ->Join('buildings as bl', function($join){
            $join->on('c.customerId', '=', 'bl.companyCode')
                ->on('l.id', '=', 'bl.location_id')
                ->on('b.id', '=', 'bl.branch_id')
                ->on('f.id','=','bl.facility_id');
        })
        ->Join('floors as fl', function($join){
            $join->on('c.customerId', '=', 'fl.companyCode')
                ->on('l.id', '=', 'fl.location_id')
                ->on('b.id', '=', 'fl.branch_id')
                ->on('f.id','=','fl.facility_id')
                ->on('bl.id','=','fl.building_id');
        })
        ->Join('lab_departments as lb', function($join){
            $join->on('c.customerId', '=', 'lb.companyCode')
                ->on('l.id', '=', 'lb.location_id')
                ->on('b.id', '=', 'lb.branch_id')
                ->on('f.id','=','lb.facility_id')
                ->on('bl.id','=','lb.building_id')
                ->on('fl.id','=','lb.floor_id');
        })
        ->Join('devices as d', function($join){
            $join->on('c.customerId', '=', 'd.companyCode')
                ->on('l.id', '=', 'd.location_id')
                ->on('b.id', '=', 'd.branch_id')
                ->on('f.id','=','d.facility_id')
                ->on('bl.id','=','d.building_id')
                ->on('fl.id','=','d.floor_id')
                ->on('lb.id','=','d.lab_id');
        })
        ->select('alarm.id','c.customerId','l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.deviceName','alarm.deviceId','alarm.sensorTag','alarm.Reason','alarm.alertType','alarm.sensorId','alarm.a_date','alarm.a_time')
                ->WHERE('customerId','=','A-TEST')
                ->WHERE('deviceId','=',$request->deviceId);
                // ->orderBy('id', 'DESC');
                if($startDate === $endDate){
                    $query->whereDate('alarm.a_date','=',$startDate); 
                }
                else{
                    $query->whereBetween('alarm.a_date', [$startDate, $endDate]);    
                }

                $getData = new ReportsDataUtilityController($request,$query);
            
                $response = [
                     "data"=>$getData->getData()
                ];
                $status = 200;
       return response($response,$status);
    }

    public function AqiLogs(Request $request){
        $startDate = date("Y-m-d",strtotime($request->fromDate));
        $endDate = date("Y-m-d", strtotime($request->toDate));
        $query = DB::table('customers as c')
        ->join('locations as l', 'c.customerId', '=', 'l.companyCode')
        ->Join('branches as b', function($join){
            $join->on('l.id', '=', 'b.location_id')
                 ->on('c.customerId', '=', 'b.companyCode');
        })
        ->Join('facilities as f', function($join){
            $join->on('c.customerId', '=', 'f.companyCode')
                ->on('l.id', '=', 'f.location_id')
                ->on('b.id', '=', 'f.branch_id');
        })
        ->Join('buildings as bl', function($join){
            $join->on('c.customerId', '=', 'bl.companyCode')
                ->on('l.id', '=', 'bl.location_id')
                ->on('b.id', '=', 'bl.branch_id')
                ->on('f.id','=','bl.facility_id');
        })
        ->Join('floors as fl', function($join){
            $join->on('c.customerId', '=', 'fl.companyCode')
                ->on('l.id', '=', 'fl.location_id')
                ->on('b.id', '=', 'fl.branch_id')
                ->on('f.id','=','fl.facility_id')
                ->on('bl.id','=','fl.building_id');
        })
        ->Join('lab_departments as lb', function($join){
            $join->on('c.customerId', '=', 'lb.companyCode')
                ->on('l.id', '=', 'lb.location_id')
                ->on('b.id', '=', 'lb.branch_id')
                ->on('f.id','=','lb.facility_id')
                ->on('bl.id','=','lb.building_id')
                ->on('fl.id','=','lb.floor_id');
        })
        ->Join('devices as d', function($join){
            $join->on('c.customerId', '=', 'd.companyCode')
                ->on('l.id', '=', 'd.location_id')
                ->on('b.id', '=', 'd.branch_id')
                ->on('f.id','=','d.facility_id')
                ->on('bl.id','=','d.building_id')
                ->on('fl.id','=','d.floor_id')
                ->on('lb.id','=','d.lab_id');     
        })
        ->Join('sensors as s', function($join){
                    $join->on('c.customerId', '=', 'd.companyCode')
                        ->on('l.id', '=', 's.location_id')
                        ->on('b.id', '=', 's.branch_id')
                        ->on('f.id','=','s.facility_id')
                        ->on('bl.id','=','s.building_id')
                        ->on('fl.id','=','s.floor_id')
                        ->on('lb.id','=','s.lab_id')
                        ->on('d.id','=','s.deviceid');
                })
        ->select('alarm.id','c.customerId','l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.deviceName','alarm.deviceId','alarm.sensorTag','alarm.Reason','alarm.alertType','alarm.sensorId','alarm.a_date','alarm.a_time')
                ->WHERE('customerId','=','A-TEST')
                ->WHERE('deviceId','=',$request->deviceId);
    }
}

