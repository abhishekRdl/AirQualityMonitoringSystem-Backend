<?php

namespace App\Http\Controllers;

use App\Models\SampledSensorDataDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampledSensorDataDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
      
         $sensorValues = DB::table('sampled_sensor_data_details')
                        ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                        ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sampled_sensor_data_details.sample_date_time)/("'. $segregationInterval.'" * 60)) AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                        ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
                        ->where('parameterName','=',$sensorTagId)
                        ->groupBy('timekey')
                        ->get()->toArray();
                        
                        
                        
        $sensorData["id"] = $sensorTagId;  
        foreach($sensorValues as $sensor){
          	$sensorData["data"][] = [ 
  	            "y"=>$sensor->par_last,
  	            "x"=>$sensor->DATE_TIME
          	];
        }                
                        
        $response = [
              "data"=>$sensorData,
              "status"=>200
        ];
        
        
    
      /*     
        
        //$aqmi_par_id="PM10";
        $sampling_Interval_min=60;
        $cur_date_time=date("Y-m-d H:i:s");
        $backInterval_min=24*60;
        $date_from=date("Y-m-d H:i:s",strtotime($cur_date_time)-$backInterval_min*60);
        
        
        
        
        
        $sensorTagNames = array("O3","NH3","PM10","PM2.5","SO2","NO2"); //get the tag names in array based on deveiceid
        $arrlength = count($sensorTagNames);
        
        for($x = 0; $x<$arrlength; $x++){
                $sensorValues = DB::table('sampled_sensor_data_details')
                                 ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
                                ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sampled_sensor_data_details.sample_date_time)/("'.$sampling_Interval_min.'" * 60)) AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
                                ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$backInterval_min.' MINUTE)')
                                ->where('parameterName','=',"PM10")
                                ->groupBy('timekey')
                                ->get()->toArray();
                                
                $sensorData["id"] =$sensorTagNames[$x];  
                foreach($sensorValues as $sensor){
                  	$sensorData["data"][] = [ 
          	            "y"=>$sensor->par_last,
          	            "x"=>$sensor->DATE_TIME
                  	];
                }
                $deviceData[] = $sensorData;
                $sensorData["data"] = [];
        }
       */
        return response($response,200);
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
        //  $sensorValues = DB::table('sampled_sensor_data_details')
        //         // ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
        //         ->select(DB::raw('sample_date_time as DATE_TIME,sensor_id,parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sample_date_time)/('.$sampling_Interval_min.' * 60)) AS timekey,MAX(max_val) as par_max,MIN(min_val) as par_min,AVG(avg_val)  as par_avg,last_val as par_last FROM sampled_sensor_data_details  WHERE sensor_id=$aqmi_par_id and sample_date_time >(NOW() - INTERVAL '.$backInterval_min.' MINUTE) GROUP BY timekey'))
        //         // ->select('sampled_sensor_data_details.*','sensors.deviceId','sensors.deviceName','sensors.sensorTag')
        //         // ->where('parameterName','=',PM10")
        //         // ->where('sampled_sensor_data_details.sensor_id','=',$aqmi_par_id)
        //         // ->where('sampled_sensor_data_details.sample_date_time','>','(NOW() - INTERVAL "'.$backInterval_min.'" MINUTE)')
        //         // // ->groupBy('timekey')
        //         // ->orderBy('sampled_sensor_data_details.id','desc')
        //         ->get()->toArray();
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
