<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\UTILITY\DataUtilityController;
use App\Models\Device;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Database\QueryException;

class DeviceController extends Controller
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
        $query = Device::query();
        
        if($companyCode = $this->companyCode){
            $query->where('companyCode','=',$companyCode);             
        }       

        $getData = new DataUtilityController($request,$query);
        $response = $getData->getData();
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
        
        try{
            $deviceDataFound = DB::table('devices')
                ->where('companyCode', '=', $this->companyCode)  
                // ->where('location_id', '=', $request->location_id)             
                // ->where('branch_id', '=', $request->branch_id)             
                // ->where('facility_id', '=', $request->facility_id)             
                // ->where('building_id', '=', $request->building_id) 
                // ->where('category_id', '=', $request->category_id)     
               // ->where('deviceName', '=', $request->deviceName)     
                ->where('deviceTag', '=', $request->deviceTag)                                   
                ->first();                      
                
            if($deviceDataFound){
                throw new Exception("Duplicate entry for device name ");
            }

            $device = new Device;
            
            $categories = Categories::where('id',$request->category_id)->first();
            $deviceCategory = $categories->categoryName;
                
            $device->companyCode = $this->companyCode;
            $device->location_id = $request->location_id;   
            $device->branch_id = $request->branch_id;            
            $device->facility_id = $request->facility_id;
            $device->building_id = $request->building_id;
            
            $device->floor_id=$request->floor_id;
            $device->floorCords=$request->floorCords;
            $device->lab_id=$request->lab_id;
            
            $device->deviceName = $request->deviceName;
            $device->deviceCategory = $deviceCategory;    
            $device->category_id = $request->category_id;   
            $device->firmwareVersion = $request->firmwareVersion;   
            $device->macAddress = $request->macAddress;  
            

            $image = $request->deviceImage;  // your base64 encoded

            if($image){
                $image = str_replace('data:image/png;base64,', '', $request->deviceImage);
                $image = str_replace(' ', '+', $image);
                $imageName =  $request->deviceName.".png";
                //$picture   = date('His').'-'.$filename;                
                $path = "Customers/".$this->companyCode."/Buildings/devices";     
                $imagePath = $path."/".$imageName;        
                Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));    
                $device->deviceImage = $imagePath;              
            }        
            
        
            $accessPath = "http://varmatrix.com/Aqms/blog/public/";
            
            //datapush file creation
            $dataPushFileName =  $request->deviceName."_DataPush.json";
            $dataPushdata = json_encode(['Element 1','Element 2','Element 3','Element 4','Element 5']);
            $dataPushUrlpath = "Customers/".$this->companyCode."/Buildings/devices/ConfigSettingFile";     
            Storage::disk('public_uploads')->put($dataPushUrlpath."/".$dataPushFileName, $dataPushdata); 

            
            //firmwarepush file creation
            // $firmwarePushFileName =  $request->deviceName."_firmwarePush.json";
            // $firmwarePushdata = json_encode(['Element 1','Element 2','Element 3','Element 4','Element 5']);
            $firmwarePushUrlpath = "Customers/".$this->companyCode."/Buildings/devices/ConfigSettingFile"; 

            //Storage::disk('public_uploads')->put($accessPath.$firmwarePushUrlpath."/".$firmwarePushFileName, $firmwarePushdata); 
            
            $device->deviceTag =  $request->deviceTag;  
            $device->nonPollingPriority =  $request->nonPollingPriority;  
            $device->pollingPriority =  $request->pollingPriority;  
            
            $device->dataPushUrl = $accessPath.$dataPushUrlpath."/".$dataPushFileName;
            $device->firmwarePushUrl = $accessPath.$firmwarePushUrlpath;
            
            $device->save();
            $response = [
                "message" => "Device name added successfully"
            ];
            $status = 201;   
        }catch(Exception $e){
            $response = [
                "message"=>$e->getMessage()
            ];            
            $status = 406;
        }catch(QueryException $e){
            $response = [
                "error" => $e->errorInfo
            ];
            $status = 406; 
       }
       return response($response,$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

            $deviceDataNotFound = Device::find($id);
                
            if(!$deviceDataNotFound){
                throw new Exception("Device data not found");
            }


            $deviceDataFound = DB::table('devices')
                ->where('companyCode', '=', $this->companyCode)  
                ->where('location_id', '=', $request->location_id)             
                ->where('branch_id', '=', $request->branch_id)             
                ->where('facility_id', '=', $request->facility_id)             
                ->where('building_id', '=', $request->building_id)   
                ->where('category_id', '=', $request->category_id)     
                ->where('deviceName', '=', $request->deviceName) 
                ->where('id','<>',$id)                                             
                ->first();                                  
                
            if($deviceDataFound){
                throw new Exception("Duplicate entry for device name ");
            }

            $device = Device::find($id);
            if($device){
                
                $categories = Categories::where('id',$request->category_id)->first();
                $deviceCategory = $categories->categoryName;
            
            
                $device->companyCode = $this->companyCode;
                $device->location_id = $request->location_id;   
                $device->branch_id = $request->branch_id;            
                $device->facility_id = $request->facility_id;
                $device->building_id = $request->building_id;
                
                $device->floor_id=$request->floor_id;
                $device->floorCords=$request->floorCords;
                $device->lab_id=$request->lab_id;
            
            
                $device->deviceName = $request->deviceName;
                $device->category_id = $request->category_id;   
                $device->deviceCategory = $deviceCategory;    
                $device->firmwareVersion = $request->firmwareVersion;   
                $device->macAddress = $request->macAddress;      
                
                $image = $request->deviceImage;  // your base64 encoded

                if($image){
                    $image = str_replace('data:image/png;base64,', '', $request->deviceImage);
                    $image = str_replace(' ', '+', $image);
                    $imageName = $request->deviceName.".png";
                    //$picture   = date('His').'-'.$filename;                
                    $path = "Customers/".$this->companyCode."/Buildings/devices";     
                    $imagePath = $path."/".$imageName;        
                    Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));    
                    $device->deviceImage = $imagePath;              
                }               
                
                // $device->deviceIcon =  $request->deviceIcon;
                
                $device->deviceTag =  $request->deviceTag;     
                
                $device->nonPollingPriority =  $request->nonPollingPriority;  
                $device->pollingPriority =  $request->pollingPriority;  
                
                $device->save();
                $response = [
                    "message" => "Device name updated successfully"
                ];
                $status = 201;   
            }    

        }catch(Exception $e){
            $response = [
                "message"=>$e->getMessage()
            ];            
            $status = 406;
        }catch(QueryException $e){
            $response = [
                "error" => $e->errorInfo
            ];
            $status = 406; 
       }
       return response($response,$status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $device = Device::find($id);
            if(!$device){
                throw new exception("Device name not found");
            }          

            if($device){                 
                $device->delete();
                $response = [
                    "message" => "Device name and related data deleted successfully"
                ];
                $status = 200;             
            }   
        }catch(Exception $e){
            $response = [
                "message"=>$e->getMessage()
            ];
            $status = 409;
        }
                    
        return response($response,$status); 
    }


    public function updateDeviceMode(Request $request,  $id)
    {
        try{
            $device = Device::find($id);
            if(!$device){
                throw new exception("Device name not found");
            }          

            if($device){        
                $device->deviceMode = $request->deviceMode;
                $device->save();
                $response = [
                    "message" => "Device mode updated successfully"
                ];
                $status = 200;             
            }   
        }catch(Exception $e){
            $response = [
                "message"=>$e->getMessage()
            ];
            $status = 409;
        }
                    
        return response($response,$status); 
    }

    
}
