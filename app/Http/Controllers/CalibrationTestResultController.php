<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use App\Models\CalibrationTestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;
use App\Models\Sensor;
use Illuminate\Support\Facades\Mail;

class CalibrationTestResultController extends Controller
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
           
            if($request->sensorTag == ""){
                throw new Exception("Please Select the sensorTag name");
            }
            
            $nextDueDate = DB::table('calibration_test_results')
                            ->select('nextDueDate')
                            ->where('sensorTag','=',$request->sensorTag)
                            ->where('companyCode','=',$this->companyCode)
                            ->orderBy('id', 'DESC')->first();
            $date = "";
            if($nextDueDate){
                $date = $nextDueDate->nextDueDate;
            }
            
            $sensorUnitName =  DB::table('sensors')
                            ->select('sensorNameUnit')
                            ->where('sensorTag','=',$request->sensorTag)
                            ->where('companyCode','=',$this->companyCode)
                              ->orderBy('id', 'DESC')->first();
            
           
            $query = DB::table('calibration_test_results')
            ->select('*')
            ->where('sensorTag','=',$request->sensorTag)
            ->where('companyCode','=',$this->companyCode);   
                     
            
            $getData = new DataUtilityController($request,$query);
            
            $response = [
                "lastDueDate"=>$date,
                "sensorNameUnit"=>$sensorUnitName->sensorNameUnit,
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
                ->select('c.customerId', 'l.stateName', 'b.branchName','f.facilityName','bl.buildingName','fl.floorName','lb.labDepName','d.deviceName','s.sensorNameUnit','s.sensorTag')
                ->WHERE('customerId','=','A-TEST')
                ->WHERE('sensorTag','=',$request->sensorTag)
                ->first();
                
                
        $userNotificationEnabledData = DB::table('users')
               // ->where('companyCode','=',$this->companyCode)
                ->where('empNotification','=','1')
                ->get();


        
        $current_time = date('Y-m-d H:i:s');         
        $calibrationtestresult = new CalibrationTestResult;
        $calibrationtestresult->companyCode = $this->companyCode;
        $calibrationtestresult->sensorTag = $request->sensorTag;
        $calibrationtestresult->name = $request->name;
        $calibrationtestresult->model = $request->model;
        $calibrationtestresult->testResult = $request->testResult;
        $calibrationtestresult->calibrationDate = $current_time;
        $calibrationtestresult->nextDueDate = $request->nextDueDate;      
        $calibrationtestresult->save();
        $response = [
            "message" => "Calibration test Result added successfully"
        ];
        $status = 201;   
        
        if($request->testResult == "Fail"){
            foreach($userNotificationEnabledData as $user){
                $this->sendCalbrationResultMailToUsers($query, $user->email, $calibrationtestresult->testResult);
            }              
        }

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


    public function sendCalbrationResultMailToUsers($query, $userEmail, $result)
    {   
          $data = [
                    'userid'=>$userEmail,
                    'subject' => 'BumpTest Result Information',
                    'customerName'=>$query->customerId,
                    'stateName'=>$query->stateName,
                    'branchName'=>$query->branchName,
                    'facilityName'=>$query->facilityName,
                    'buildingName'=>$query->buildingName,
                    'floorName'=>$query->floorName,
                    'labDepName'=>$query->labDepName,
                    'deviceName'=>$query->deviceName,
                    'sensorNameUnit'=>$query->sensorNameUnit,
                    'sensorTagName'=>$query->sensorTag,
                    'Result'=>$result
                ];
        
        Mail::send('bumpTestMail',$data, function($messages) use ($userEmail){
            $messages->to($userEmail);
            $messages->subject('Calibration Result Information');        
        });
    }
}

// https://varmatrix.com/Aqms/api/calibrationTestResult/add
// Method:POST
// request
// {
//     "sensorTag":"pm10",
//     "name":"pm",   
//     "model":"25",   
//     "testResult":"pass",   
//     "nextDueDate":"23/08/2022",      
// }