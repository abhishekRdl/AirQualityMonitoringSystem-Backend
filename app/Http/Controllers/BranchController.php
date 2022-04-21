<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UtilityController;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Exceptions\CustomException;
use App\Http\Controllers\UTILITY\DataUtilityController;

class BranchController extends Controller
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
       
        $query = Branch::query();             

        if($companyCode = $this->companyCode){
            $query->where('companyCode','=',$companyCode);             
        }

        if($branchName = $request->branchName){
            $query->where('branchName','=',$branchName);         
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
        
        $branch = Branch::where('branchName', $request->branchName)->first();  
        
        try{
            $branch = new Branch;
            $branch->companyCode = $this->companyCode;
            $branch->location_id = $request->location_id;   
            $branch->branchName = $request->branchName;          
            $branch->coordinates = $request->coordinates;       
            $branch->save();
            $response = [
                "message" => "Branch name added successfully"
            ];
            $status = 201;  

        }catch (QueryException $e) {
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
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {       
        try{
            $branch = Branch::find($id);
            if(!$branch){
                throw new CustomException("Branch name not found");
            }  
            $branch->companyCode = $this->companyCode;
            $branch->location_id = $request->location_id;   
            $branch->branchName = $request->branchName;          
            $branch->coordinates = $request->coordinates;
            $branch->update();
            $response = [
                "message" => "Branch name  updated successfully"
            ];
            $status = 200;    
           
        }catch (QueryException $e) {
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
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {      
        $branch = Branch::find($id);
        if(!$branch){
            throw new CustomException("Branch name not found");
        }

        if($branch){                 
            $branch->delete();
            $response = [
                "message" => "Branchname and related data deleted successfully"
            ];
            $status = 200;             
        }       
        
        return response($response,$status);   
    }
}
