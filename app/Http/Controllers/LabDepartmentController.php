<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UtilityController;
use App\Http\Controllers\UTILITY\DataUtilityController;
use App\Models\labDepartment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;

class LabDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $companyCode = "";    
    
    public function encodeImageFormat($strimg){
    	$splittedData = explode("/",$strimg);
        $format = $splittedData[1];
        $imageFormat = explode(";",$format);
        return $imageFormat[0];
    }

    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode();
        
    }

    public function index(Request $request)
    {
        $query = labDepartment::query();
        
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
            $labDepartment = DB::table('lab_departments')
                ->where('companyCode', '=', $this->companyCode)  
                ->where('location_id', '=', $request->location_id)             
                ->where('branch_id', '=', $request->branch_id)             
                ->where('facility_id', '=', $request->facility_id)             
                ->where('building_id', '=', $request->building_id)             
                ->where('floor_id', '=', $request->floor_id)                
                ->where('labDepName', '=', $request->labDepName)                  
                ->first();                      
                
            if($labDepartment){
                throw new Exception("Lab department name is already Inserted");
            }

            $labDepartment = new labDepartment;
            $labDepartment->companyCode = $this->companyCode;
            $labDepartment->location_id = $request->location_id;   
            $labDepartment->branch_id = $request->branch_id;            
            $labDepartment->facility_id = $request->facility_id;
            $labDepartment->building_id = $request->building_id;
            $labDepartment->floor_id = $request->floor_id;
            $labDepartment->labDepName = $request->labDepName;    
            $image = $request->labDepMap;  // your base64 encodedyjdfty

            if($image){
                
                $format = $this->encodeImageFormat($image);
               
                if($format == "jpg"){
                   $image = str_replace('data:image/jpg;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".jpg";
                }
                if($format == "png"){
                   $image = str_replace('data:image/png;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".png";
                }
                if($format == "jpeg"){
                   $image = str_replace('data:image/png;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".jpeg";
                }
                
                
                //$image = str_replace('data:image/png;base64,', '', $request->labDepMap);
                $image = str_replace(' ', '+', $image);
                // $imageName =  $request->labDepName.".png";
                //$picture   = date('His').'-'.$filename;                
                $path = "Customers/".$this->companyCode."/Buildings/Floors/Department";     
                $imagePath = $path."/".$imageName;        
                Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));    
                $labDepartment->labDepMap = $imagePath;              
            }            
            
            $labDepartment->labCords = $request->labCords;                    
            $labDepartment->save();

            $response = [
                "message" => "Department name added successfully"
            ];
            $status = 201;           
       }catch(QueryException $e){
            $response = [
                "error" => $e->errorInfo
            ];
            $status = 406; 
       }catch(Exception $e){
           $response = [
               "error" => $e->getMessage()
           ];
           $status = 404;
       }  
       return response($response,$status);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\labDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(labDepartment $labDepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\labDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function edit(labDepartment $labDepartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\labDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $labDepartment = labDepartment::find($id);
                
            if(!$labDepartment){
                throw new Exception("Lab department not found");
            }

            $labDepartmentDataFound = DB::table('lab_departments')
                ->where('companyCode', '=', $this->companyCode)  
                ->where('location_id', '=', $request->location_id)             
                ->where('branch_id', '=', $request->branch_id)             
                ->where('facility_id', '=', $request->facility_id)             
                ->where('building_id', '=', $request->building_id)             
                ->where('floor_id', '=', $request->floor_id)                
                ->where('labDepName', '=', $request->labDepName)    
                ->where('id','<>',$id)              
                ->first();                      
           
            if($labDepartmentDataFound){
                throw new Exception("Duplicate entry For Department");
            }

            $labDepartment = labDepartment::find($id);
            $labDepartment->companyCode = $this->companyCode;
            $labDepartment->location_id = $request->location_id;   
            $labDepartment->branch_id = $request->branch_id;            
            $labDepartment->facility_id = $request->facility_id;
            $labDepartment->building_id = $request->building_id;
            $labDepartment->floor_id = $request->floor_id;
            $labDepartment->labDepName = $request->labDepName;    
            $image = $request->labDepMap;  // your base64 encoded

            if($image){
                
                $format = $this->encodeImageFormat($image);
               
                if($format == "jpg"){
                   $image = str_replace('data:image/jpg;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".jpg";
                }
                if($format == "png"){
                   $image = str_replace('data:image/png;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".png";
                }
                if($format == "jpeg"){
                   $image = str_replace('data:image/jpeg;base64,', '', $request->labDepMap);
                   $imageName =  $request->labDepName.".jpeg";
                }                
                
                //$image = str_replace('data:image/png;base64,', '', $request->labDepMap);
                $image = str_replace(' ', '+', $image);
                // $imageName =  $request->labDepName.".png";
                //$picture   = date('His').'-'.$filename;                
                $path = "Customers/".$this->companyCode."/Buildings/Floors/Department";     
                $imagePath = $path."/".$imageName;        
                Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));    
                $labDepartment->labDepMap = $imagePath;  

            }            
            
            $labDepartment->labCords = $request->labCords;                    
            $labDepartment->update();

            $response = [
                "message" => "Department name updated successfully"
            ];
            $status = 201;           
       }catch(Exception $e){
           $response = [
            "error" => true,
            "message" => $e->getMessage()
           ];
           $status = 404;
       }  

       return response($response,$status);
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\labDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {    
        $labDepartment = labDepartment::find($id);
        if(!$labDepartment){
            throw new CustomException("Department name not found");
        } 

        if($labDepartment){                 
            $labDepartment->delete();
            $response = [
                "message" => "Department name and related data deleted successfully"
            ];
            $status = 200;             
        }               
        return response($response,$status); 
    }
}
