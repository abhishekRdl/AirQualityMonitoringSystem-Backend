<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use App\Models\AlertCron;
use Illuminate\Http\Request;


class AlertCronController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\AlertCron  $alertCron
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {   
       $deviceId  = $request->device_id;

        $query = AlertCron::select('*')
                 ->where('deviceId','=',$deviceId);
                
        $getData = new DataUtilityController($request,$query);
        $response = $getData->getData();
        $status = 200;

        return response($response,$status);         
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlertCron  $alertCron
     * @return \Illuminate\Http\Response
     */
    public function edit(AlertCron $alertCron)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlertCron  $alertCron
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $sensorId = $request->sensor_id;
        $reason = $request->clearAlertReason;
        $status = 1;
        $statusMessage = "Cleared";
                 
        $query = AlertCron::select('*')
                    ->where('sensorId','=',$sensorId)
                    ->where('status','=','0')
                     ->update([
                         'Reason' => $reason,
                         'status' => $status,
                         'statusMessage'=>$statusMessage
                    ]);

        //hooter relay status to be enabled once it is acknowledge by the user


        
        if($query){
            $response = [
                "message" => "Alarms Cleared successfully" 
            ];    
            $status = 200; 
        }
        
        return response($response,$status);       
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlertCron  $alertCron
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlertCron $alertCron)
    {
        //
    }
}
