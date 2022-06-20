<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use Illuminate\Support\Facades\DB;
use App\Models\BumpTestResult;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;
use App\Models\Sensor;
use Illuminate\Support\Facades\Mail;

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
                            ->where('companyCode','=',$this->companyCode)
                            ->orderBy('id', 'DESC')->first();
            $date = "";
            if($nextDueDate){
                $date = $nextDueDate->nextDueDate;
            }
           
            $query = DB::table('bump_test_results')
            ->select('*')
            ->where('sensorTagName','=',$request->sensorTagName)
            ->where('companyCode','=',$this->companyCode);            
            
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
                ->WHERE('sensorTag','=',$request->sensorTagName)
                ->first();
                
                
        $userNotificationEnabledData = DB::table('users')
                // ->where('companyCode','=',$this->companyCode)
                ->where('empNotification','=','1')
                ->get();
                
        $current_time = date('Y-m-d H:i:s');         
        $bumptestresult = new BumpTestResult;
        $bumptestresult->companyCode = $this->companyCode;
        $bumptestresult->device_id = $request->device_id;
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
            
            // foreach($userNotificationEnabledData as $user){
            //     $this->sendBumpTestDueDateMailToUsers($query, $user->email, $bumptestresult->result);
            // }//commented because
            
        }
        else{
            $bumptestresult->result = "Fail";
            
            foreach($userNotificationEnabledData as $user){
                $this->sendBumpTestDueDateMailToUsers($query, $user->email, $bumptestresult->result);
            }            
        }
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
    
    
    public function sendBumpTestDueDateMailToUsers($query, $userEmail, $result)
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
            $messages->subject('BumpTest Result Information');        
        });
    }
    
    public function sql(){
        
        $sensorTagName = 'pm2.5_gas1';       
        
        //sample join query
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
                ->WHERE('sensorTag','=',$sensorTagName)
                ->first();
                
                
                //sample mail with multiple content in data
                // $email = "abhishek@rdltech.in";
                // $data = [
                //     'userid'=>$email,
                //     'subject' => 'Application employee Credentials',
                //     'body' =>"123456",
                //     "content"=>"hello"
                // ];
        
                // Mail::send('credentialmail',$data, function($messages) use ($email){
                //     $messages->to($email);
                //     $messages->subject('Application login credentials');        
                // });
                
                
                
                $userNotificationEnabledData = DB::table('users')
                        ->where('empNotification','=','1')
                        ->get();
                        
                $userNames = array();
                foreach($userNotificationEnabledData as $user){
                    $this->sendBumpTestDueDateMailToUsers($query, $user->email);
                   
                }
                
                $response = [
                    "data"=>$userNames
                ];
                
        return response($response,200);
       
        
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