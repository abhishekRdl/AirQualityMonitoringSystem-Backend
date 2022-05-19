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
        
        // $sensorValues = DB::table('sampled_sensor_data_details')
        //         ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
        //         ->select('sampled_sensor_data_details.*', 'sensors.deviceId','sensors.deviceName','sensors.sensorTag')
        //         ->where('parameterName','=',"PM10")
        //         ->whereBetween('sample_date_time', ['2022-05-14','2022-05-15'])
        //         ->orderBy('id','desc')
        //         ->get()->toArray();
                
        
        // $deviceData = [];
        // $deviceData['id'] = "PM10";
        // foreach($sensorValues as $sensor){
        //   	$deviceData["data"][] = [ 
        //   	            "x"=>$sensor->last_val,
        //   	            "y"=>$sensor->sample_date_time
        //   	];
        // }
        
        
        
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
        
        // $cars = array("Volvo", "Volva", "Toyota");
        // $arrlength = count($cars);
        
        // $data = array();
        
        // for($x = 0; $x < $arrlength; $x++) {
        //     //$data[$cars[$x]] =["date"=>"25-3-2022"];
        // //   	for($i=0;$i<3;$i++){
        // //   	    $data2 = [
        // //     	    "x"=>20,
        // //             "y"=>30
        // //         ];  
        // //         $data[$cars[$x]]["data"][] = $data2;
        // //     }
        //     $data[] = $cars[$x];
        //}
      
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
        //
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
