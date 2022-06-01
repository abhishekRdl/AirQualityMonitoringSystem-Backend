<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use Illuminate\Support\Facades\DB;
use App\Models\BumpTestResult;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;

class BumpTestResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $companyCode = ""; 
    
    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode();        
    }


    public function index(Request $request) 
    {
        try{ 
            if($request->sensorTagName == ""){
                throw new Exception("Please Select the sensorTag name");
            }
            
             $nextDueDate = DB::table('bump_test_results')
                            ->select('nextDueDate')
                            ->where('sensorTagName','=',$request->sensorTagName)
                            ->orderBy('id', 'DESC')->first();
            $date = "";
            if($nextDueDate){
                $date = $nextDueDate->nextDueDate;
            }
           
            $query = DB::table('bump_test_results')
            ->select('*')
            ->where('sensorTagName','=',$request->sensorTagName);            
            
            $getData = new DataUtilityController($request,$query);
            
            $response = [
                "nextDueDate"=>$date,
                 "data"=>$getData->getData()['data']
            ];
            
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
        $bumptestresult->companyCode = $this->companyCode;
        $bumptestresult->sensorTagName = $request->sensorTagName;
        $bumptestresult->lastDueDate = $request->lastDueDate;
        $bumptestresult->typeCheck = $request->typeCheck;
        $bumptestresult->percentageConcentrationGas = $request->percentageConcentrationGas;
        $bumptestresult->durationPeriod = $request->durationPeriod;
        $bumptestresult->displayedValue = $request->displayedValue;
        $bumptestresult->percentageDeviation = $request->percentageDeviation;
        $bumptestresult->calibrationDate = $current_time;
        $bumptestresult->nextDueDate = $request->nextDueDate;
        if($request->percentageDeviation >= 0 && $request->percentageDeviation <= 10){
            $bumptestresult->result = "Pass";

        }
        else{
            $bumptestresult->result = "Fail";    

            // $data = [
            //     'userid'=>$user->name,
            //     'subject' => 'Application OTP',
            //     'body' => 'Your OTP is : '. $otp
            // ];
        
            // Mail::send('mail',$data, function($messages) use ($user){
            //     $messages->to($user->email);
            //     $messages->subject('OTP verification');        
            // });  

        }
             
        $bumptestresult->save();
        $response = [
            "message" => "BumpTest Result added successfully"
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


    public function sql(){
        $sensorValues = DB::table('sampled_sensor_data_details')
        ->join('sensors', 'sensors.id', '=', 'sampled_sensor_data_details.sensor_id')
        ->select(DB::raw('sensors.deviceId,sensors.deviceName,sensors.sensorTag,sampled_sensor_data_details.sample_date_time as DATE_TIME,sampled_sensor_data_details.sensor_id,sampled_sensor_data_details.parameterName as parameter,FLOOR(UNIX_TIMESTAMP(sampled_sensor_data_details.sample_date_time)/("'. $segregationInterval.'" * 60)) AS timekey,MAX(sampled_sensor_data_details.max_val) as par_max,MIN(sampled_sensor_data_details.min_val) as par_min,AVG(sampled_sensor_data_details.avg_val)  as par_avg,sampled_sensor_data_details.last_val as par_last'))
        ->whereRaw('sampled_sensor_data_details.sample_date_time >(NOW() - INTERVAL '.$rangeInterval.' MINUTE)')
        ->where('sampled_sensor_data_details.sensor_id','=',$sensorTagIds[$x]->id)
        ->groupBy('timekey')
        ->get()->toArray();
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



//bumptest query
// SELECT customers.customerId, locations.stateName, branches.branchName, facilities.facilityName, buildings.buildingName, floors.floorName, lab_departments.labDepName, devices.deviceName, sensors.sensorNameUnit FROM customers 
// INNER JOIN locations ON customers.customerId = locations.companyCode 
// INNER JOIN branches ON customers.customerId = branches.companyCode AND locations.id = branches.location_id 
// INNER JOIN facilities ON customers.customerId = facilities.companyCode AND locations.id = facilities.location_id AND branches.id = facilities.branch_id 
// INNER JOIN buildings ON customers.customerId = buildings.companyCode AND  locations.id = buildings.location_id  AND branches.id = buildings.branch_id AND facilities.id = buildings.facility_id
// INNER JOIN floors ON customers.customerId = floors.companyCode AND  locations.id = floors.location_id  AND  branches.id = floors.branch_id AND  facilities.id = floors.facility_id AND buildings.id = floors.building_id
// INNER JOIN lab_departments ON customers.customerId = lab_departments.companyCode AND  locations.id = lab_departments.location_id  AND  branches.id = lab_departments.branch_id AND  facilities.id = lab_departments.facility_id AND buildings.id = lab_departments.building_id AND floors.id = lab_departments.floor_id 
// INNER JOIN devices ON customers.customerId = devices.companyCode AND  locations.id = devices.location_id  AND  branches.id = devices.branch_id AND  facilities.id = devices.facility_id AND buildings.id = devices.building_id AND floors.id = devices.floor_id AND lab_departments.id = devices.lab_id
// INNER JOIN sensors ON customers.customerId = sensors.companyCode AND  locations.id = sensors.location_id  AND  branches.id = sensors.branch_id AND  facilities.id = sensors.facility_id AND buildings.id = sensors.building_id AND floors.id = sensors.floor_id AND lab_departments.id = sensors.lab_id AND devices.id = sensors.deviceid
// where customers.customerId = "A-TEST" AND sensors.sensorTag = "NH3_gas1"