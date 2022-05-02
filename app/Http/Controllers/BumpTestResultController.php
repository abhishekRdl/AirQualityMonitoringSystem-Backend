<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use Illuminate\Support\Facades\DB;
use App\Models\BumpTestResult;
use Illuminate\Http\Request;

class BumpTestResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
        try{ 
            $query = DB::table('bump_test_results')
            ->select('*')
            ->where('sensorTagName','=',$request->sensorTagName);            
            
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
        $bumptestresult = new BumpTestResult;
        $bumptestresult->sensorTagName = $request->sensorTagName;
        $bumptestresult->lastDueDate = $request->lastDueDate;
        $bumptestresult->typeCheck = $request->typeCheck;
        $bumptestresult->percentageConcentrationGas = $request->percentageConcentrationGas;
        $bumptestresult->durationPeriod = $request->durationPeriod;
        $bumptestresult->displayedValue = $request->displayedValue;
        $bumptestresult->percentageDeviation = $request->percentageDeviation;
        $bumptestresult->calibrationDate = $current_time;
        $bumptestresult->nextDueDate = $request->nextDueDate;
        $bumptestresult->result = $request->result;      
        $bumptestresult->save();
        $response = [
            "message" => "Bump test Result added successfully"
        ];
        $status = 201;       

        return response($response,$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BumpTestResult  $bumpTestResult
     * @return \Illuminate\Http\Response
     */
    public function show(BumpTestResult $bumpTestResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BumpTestResult  $bumpTestResult
     * @return \Illuminate\Http\Response
     */
    public function edit(BumpTestResult $bumpTestResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BumpTestResult  $bumpTestResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BumpTestResult $bumpTestResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BumpTestResult  $bumpTestResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(BumpTestResult $bumpTestResult)
    {
        //
    }
}


//Request input
// {
//     "sensorTagName":"HydroSen-02",
//     "lastDueDate":"22-08-2022",
//     "typeCheck":"span",
//     "percentageConcentrationGas":"23",
//     "durationPeriod":"5",
//     "displayedValue":"35",
//     "percentageDeviation":"65",
//     "calibrationDate":"22-08-2022",
//     "nextDueDate":"26-08-2022",
//     "result":"fail"
// }