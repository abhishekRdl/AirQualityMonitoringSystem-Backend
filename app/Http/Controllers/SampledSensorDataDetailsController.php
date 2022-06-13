<?php

namespace App\Http\Controllers;

use App\Models\SampledSensorDataDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Models\AlertCron;
use App\Http\Controllers\UTILITY\DataUtilityController;

class SampledSensorDataDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode(); 
    }
    
    
    public function index()
    {
        $query = SampledSensorDataDetails::select('*');
        $response = $query->get();
        $status = 200;
        return response($response,$status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SampledSensorDataDetails  $sampledSensorDataDetails
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        
        $sensorData = array();
        $deviceData = array();
         
        $sensorTagNames = array("O3","NH3","PM10","PM2.5","SO2","NO2"); //get the tag names in array based on deveiceid
        $arrlength = count($sensorTagNames);
        
        for($x = 0; $x<$arrlength; $x++){
              $sensorValues = DB::table('sampled_sensor_data_details')
                ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                ->select('sampled_sensor_data_details.*', 'sensors.deviceId','sensors.deviceName','sensors.sensorTag')
                ->where('parameterName','=',$sensorTagNames[$x])
                ->whereBetween('sample_date_time', ['2022-05-14','2022-05-15'])
                ->orderBy('id','desc')
                ->skip(0)->take(10)
                ->get()->toArray();
                $sensorData["id"] =$sensorTagNames[$x];  
                foreach($sensorValues as $sensor){
                  	$sensorData["data"][] = [ 
          	            "y"=>$sensor->last_val,
          	            "x"=>$sensor->sample_date_time
                  	];
                }
                $deviceData[] = $sensorData;
                $sensorData["data"] = [];
        }
        return response($deviceData,200);
    }
    
    public function deviceSensorShow(Request $request){
        $sensorValues = DB::table('sampled_sensor_data_details')
                ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                ->select('sampled_sensor_data_details.*', 'sensors.deviceId','sensors.deviceName','sensors.sensorTag')
                ->where('parameterName','=',"PM10")
                ->whereBetween('sample_date_time', ['2022-05-14','2022-05-15'])
                ->orderBy('id','desc')
                ->get()->toArray();
                
        
        $deviceData = [];
        $deviceData['id'] = "PM10";
        $i = 0;
        foreach($sensorValues as $sensor){
            if($i<10){
          	$deviceData["data"][] = [ 
          	            "x"=>$sensor->last_val,
          	            "y"=>$sensor->sample_date_time
          	];
            }
            $i++;
        }
        
        return response($deviceData,200);
    }
    
    
    public function lastSampledData(Request $request){
        
        //detail of sending data to frontend
        // sensorID,
        // Segregation Interval - 30min/1hr/3hrs/6hr/12hr/24hrs
        // Range Interval - 6hr/12hrs/1day/1week/1month (edited) 
      
      
        $deviceId = $request->deviceId;
        $segregationInterval = $request->segretionInterval; //in mins   $sampling_Interval_min=60;
        $rangeInterval = $request->rangeInterval; //  $backInterval_min=24*60;
        
        $deviceData = array();
        
        $sampling_Interval_min=60;
        $cur_date_time=date("Y-m-d H:i:s");
        $backInterval_min=24*60;
        $date_from=date("Y-m-d H:i:s",strtotime($cur_date_time)-$backInterval_min*60);
       
        $sensorTagIds = DB::table('sensors')
                        ->select('id')
                        ->where('deviceId','=',$deviceId)
                        ->get();
                        
        $length = count($sensorTagIds);
        
        for($x = 0; $x<$length; $x++){
            
                $otherDataValues = DB::table('sampled_sensor_data_details')
                        ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                           ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,sampled_sensor_data_details.sample_date_time AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                           ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
                           ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagIds[$x]->id)
                           ->get();
                           
                $minVal = $otherDataValues[0]->par_min;
                $maxVal = $otherDataValues[0]->par_max;
                $avgVal = $otherDataValues[0]->par_min;
                $sensorTagName = $otherDataValues[0]->sensorTag;
                
                if($sensorTagName != ""){
                    $sensorValues = DB::table('sampled_sensor_data_details')
                        ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                        ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sampled_sensor_data_details.sample_date_time)/("'. $segregationInterval.'" * 60)) AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                        ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
                        ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagIds[$x]->id)
                        ->groupBy('timekey')
                        ->get()->toArray();
                                
                    $sensorData["id"] =$sensorTagIds[$x]->id; 
                    if($sensorTagIds[$x]->id != 43){
                        $sensorData["sensorTag"] = $sensorTagName;
                        $sensorData["min"] = $minVal;
                        $sensorData["max"] = $maxVal;
                        $sensorData["avg"] = $avgVal;
                            foreach($sensorValues as $sensor){
                              	$sensorData["data"][] = [ 
                      	            "y"=>$sensor->par_last,
                      	            "x"=>$sensor->DATE_TIME
                              	];
                            }
                            $deviceData[] = $sensorData;
                            $sensorData["data"] = [];
                            $sensorData["min"] = "";
                            $sensorData["max"] = "";
                            $sensorData["avg"] = "";
                        }
                    }    
                    
                    
        }
       
        return response($deviceData,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SampledSensorDataDetails  $sampledSensorDataDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(SampledSensorDataDetails $sampledSensorDataDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SampledSensorDataDetails  $sampledSensorDataDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SampledSensorDataDetails $sampledSensorDataDetails)
    {
       
    }
    
    
    
    public function getLastSampledDataOfSensorTagId(Request $request){
        
        if($request->sensorTagId == ""){
            $response = [
                  "data"=>"Sensor Tag Id is required"  
                ];
            $status = 401;
        }
        
        if($request->segretionInterval == ""){
            $response = [
                  "data"=>"Segregation Interval is required"  
                ];
                $status = 401;
        }
        
        if($request->rangeInterval == ""){
            $response = [
                  "data"=>"Range Interval is required"  
                ];
                $status = 401;
            
        }else{
            $sensorTagId = $request->sensorTagId;
            $segregationInterval = $request->segretionInterval; //in mins   $sampling_Interval_min=60;
            $rangeInterval = $request->rangeInterval; //  $backInterval_min=24*60;
            
            
            $sampling_Interval_min=60;
            $cur_date_time=date("Y-m-d H:i:s");
            $backInterval_min=24*60;
            $date_from=date("Y-m-d H:i:s",strtotime($cur_date_time)-$backInterval_min*60);
            
           
            //single sensortag data
            
            $sensorData = array();
            $deviceData = array();
            
            $otherDataValues = DB::table('sampled_sensor_data_details')
                                ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                               ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,sampled_sensor_data_details.sample_date_time AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                               ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
                               ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagId)
                               ->get();
                               
            $minVal = $otherDataValues[0]->par_min;
            $maxVal = $otherDataValues[0]->par_max;
            $avgVal = $otherDataValues[0]->par_min;
            $sensorTag = $otherDataValues[0]->sensorTag;
             
          
            $sensorValues = DB::table('sampled_sensor_data_details')
                            ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                            ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sampled_sensor_data_details.sample_date_time)/("'. $segregationInterval.'" * 60)) AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                            ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
                            ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagId)
                            ->groupBy('timekey')
                            ->get()->toArray();
                            
            $sensorData["id"] = $sensorTag;
            $sensorData["min"] = $minVal;
            $sensorData["max"] = $maxVal;
            $sensorData["avg"] = $avgVal;
            foreach($sensorValues as $sensor){
              	$sensorData["data"][] = [ 
      	            "y"=>$sensor->par_last,
      	            "x"=>$sensor->DATE_TIME
              	];
            }               
                            
            $response = $sensorData;
            $status = 200;
        }
        
        
        return response($response,$status);
    }
    
    public function liveDataDeviceId(Request $request){
         
        $deviceId = $request->device_id;

        //getting Count of alerts of particular device
        $query = AlertCron::select('*')
                 ->where('deviceId','=','3')
                 ->where('status','=','0');

        $getData = new DataUtilityController($request,$query);
        $alertCount = $getData->getData()['totalData'];

        
        $sensorTagsOfDeviceId = DB::table('customers as c')
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
                ->select('c.customerId', 'l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.deviceName','s.sensorNameUnit','s.sensorOutput','s.id','s.sensorTag')
                ->WHERE('customerId','=',$this->companyCode)
                ->WHERE('deviceId','=',$deviceId)
                ->get();
                
        $length = count($sensorTagsOfDeviceId);        
        
        $deviceData = array();
        $output = array();
        $sensorData = array();
        
        for($x=0;$x<$length;$x++){
            $otherDataValues = DB::table('sampled_sensor_data_details')
                         ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                          ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,sampled_sensor_data_details.sample_date_time AS timekey,sampled_sensor_data_details.max_val as par_max,sampled_sensor_data_details.min_val as par_min,sampled_sensor_data_details.avg_val as par_avg,sampled_sensor_data_details.last_val as par_last'))
                          ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagsOfDeviceId[$x]->id)
                          ->orderBy('sampled_sensor_data_details.id','desc')
                          ->first();
                          
                if($otherDataValues != ""){
                    $minVal = $otherDataValues->par_min;
                    $maxVal = $otherDataValues->par_max;
                    $avgVal = $otherDataValues->par_avg;
                    $lastVal = $otherDataValues->par_last;
                    $sensorTagName = $otherDataValues->sensorTag; 
                    
                    if($sensorTagName != ""){
                        if($sensorTagsOfDeviceId[$x]->sensorOutput == "Analog"){
                            
                            $sensorData['customerId']  = $sensorTagsOfDeviceId[$x]->customerId;
                            $sensorData['stateName']   = $sensorTagsOfDeviceId[$x]->stateName;
                            $sensorData['branchName']  = $sensorTagsOfDeviceId[$x]->branchName;
                            $sensorData['facilityName'] = $sensorTagsOfDeviceId[$x]->facilityName;
                            $sensorData['buildingName'] = $sensorTagsOfDeviceId[$x]->buildingName;
                            $sensorData['floorName'] = $sensorTagsOfDeviceId[$x]->floorName;
                            $sensorData['labDepName'] = $sensorTagsOfDeviceId[$x]->labDepName;
                            $sensorData['deviceName'] = $sensorTagsOfDeviceId[$x]->deviceName;
                            $sensorData['sensorNameUnit'] = $sensorTagsOfDeviceId[$x]->sensorNameUnit;
                            $sensorData['sensorOutput'] = $sensorTagsOfDeviceId[$x]->sensorOutput;
                            $sensorData["sensorTag"] = $sensorTagName;
                            $sensorData["sensorTagId"] = $sensorTagsOfDeviceId[$x]->id;
                            $sensorData["min"] = $minVal;
                            $sensorData["max"] = $maxVal;
                            $sensorData["avg"] = $avgVal;
                            $sensorData["last"] = $avgVal;
                            $deviceData['Analog']['data'][] = $sensorData;
                        }
                        
                        
                        
                        if($sensorTagsOfDeviceId[$x]->sensorOutput == "Digital"){
                            $sensorData['customerId']  = $sensorTagsOfDeviceId[$x]->customerId;
                            $sensorData['stateName']   = $sensorTagsOfDeviceId[$x]->stateName;
                            $sensorData['branchName']  = $sensorTagsOfDeviceId[$x]->branchName;
                            $sensorData['facilityName'] = $sensorTagsOfDeviceId[$x]->facilityName;
                            $sensorData['buildingName'] = $sensorTagsOfDeviceId[$x]->buildingName;
                            $sensorData['floorName'] = $sensorTagsOfDeviceId[$x]->floorName;
                            $sensorData['labDepName'] = $sensorTagsOfDeviceId[$x]->labDepName;
                            $sensorData['deviceName'] = $sensorTagsOfDeviceId[$x]->deviceName;
                            $sensorData['sensorNameUnit'] = $sensorTagsOfDeviceId[$x]->sensorNameUnit;
                            $sensorData['sensorOutput'] = $sensorTagsOfDeviceId[$x]->sensorOutput;
                            $sensorData["sensorTag"] = $sensorTagName;
                            $sensorData["sensorTagId"] = $sensorTagsOfDeviceId[$x]->id;
                            $sensorData["min"] = $minVal;
                            $sensorData["max"] = $maxVal;
                            $sensorData["avg"] = $avgVal;
                            $sensorData["last"] = $avgVal;
                            $deviceData['Digital']['data'][] = $sensorData;
                        }
                        
                        
                        if($sensorTagsOfDeviceId[$x]->sensorOutput == "Modbus"){
                            $sensorData['customerId']  = $sensorTagsOfDeviceId[$x]->customerId;
                            $sensorData['stateName']   = $sensorTagsOfDeviceId[$x]->stateName;
                            $sensorData['branchName']  = $sensorTagsOfDeviceId[$x]->branchName;
                            $sensorData['facilityName'] = $sensorTagsOfDeviceId[$x]->facilityName;
                            $sensorData['buildingName'] = $sensorTagsOfDeviceId[$x]->buildingName;
                            $sensorData['floorName'] = $sensorTagsOfDeviceId[$x]->floorName;
                            $sensorData['labDepName'] = $sensorTagsOfDeviceId[$x]->labDepName;
                            $sensorData['deviceName'] = $sensorTagsOfDeviceId[$x]->deviceName;
                            $sensorData['sensorNameUnit'] = $sensorTagsOfDeviceId[$x]->sensorNameUnit;
                            $sensorData['sensorOutput'] = $sensorTagsOfDeviceId[$x]->sensorOutput;
                            $sensorData["sensorTag"] = $sensorTagName;
                            $sensorData["sensorTagId"] = $sensorTagsOfDeviceId[$x]->id;
                            $sensorData["min"] = $minVal;
                            $sensorData["max"] = $maxVal;
                            $sensorData["avg"] = $avgVal;
                            $sensorData["last"] = $avgVal;
                            $deviceData['Modbus']['data'][] = $sensorData;
                        }
                        
                    }
                }
        }       

        $deviceData['sensorCount'] = $sensorCount;  
        $deviceData['alertCount'] = $alertCount;              
        $response = $deviceData;
                
        return response($deviceData, 200);
                
    }
    


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SampledSensorDataDetails  $sampledSensorDataDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(SampledSensorDataDetails $sampledSensorDataDetails)
    {
        //
    }
}
