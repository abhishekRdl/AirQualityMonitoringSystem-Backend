<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use App\Models\CalibrationTestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CalibrationTestResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        try{ 
            $query = DB::table('calibration_test_results')
            ->select('*')
            ->where('sensorTag','=',$request->sensorTagName);            
            
            $getData = new DataUtilityController($request,$query);
            
            $response = $getData->getData();           
            $status = 200;

        }catch(Exception $e){
            $response = [
                "error" =>  $e->getMessage()
            ];    
            $status = 404;       
        }        
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
        $current_time = date('Y-m-d H:i:s');         
        $calibrationtestresult = new CalibrationTestResult;
        $calibrationtestresult->sensorTag = $request->sensorTag;
        $calibrationtestresult->name = $request->name;
        $calibrationtestresult->model = $request->model;
        $calibrationtestresult->testResult = $request->testResult;
        $calibrationtestresult->calibrationDate = $current_time;;
        $calibrationtestresult->nextDueDate = $request->nextDueDate;      
        $calibrationtestresult->save();
        $response = [
            "message" => "Calibration test Result added successfully"
        ];
        $status = 201;       

        return response($response,$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CalibrationTestResult  $calibrationTestResult
     * @return \Illuminate\Http\Response
     */
    public function show(CalibrationTestResult $calibrationTestResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CalibrationTestResult  $calibrationTestResult
     * @return \Illuminate\Http\Response
     */
    public function edit(CalibrationTestResult $calibrationTestResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CalibrationTestResult  $calibrationTestResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CalibrationTestResult $calibrationTestResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CalibrationTestResult  $calibrationTestResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalibrationTestResult $calibrationTestResult)
    {
        //
    }
}


//request
// {
//     "sensorTag":"pm10",
//     "name":"pm",   
//     "model":"25",   
//     "testResult":"pass",   
//     "calibrationDate":"22/08/2022",
//     "nextDueDate":"23/08/2022",  
    
// }