<?php

namespace App\Http\Controllers\UTILITY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlertCron;

class DataUtilityController extends Controller
{
    protected $total = "";
    protected $page = "";
    protected $perPage = "";
    protected $result = "";  
    protected $sort = "";   
    protected $column = ""; 
    protected $sensorCount = 0;
    protected $alertCount = 0;
    protected $returnedData = [];
   
    function __construct($request,$query) {
        if($query) {
            
            if($request->lab_id != ""){
                
                $devices = $query->get();
                $length = count($devices);      
                
                for($x=0;$x<$length;$x++){
                    $deviceId = $devices[$x]->id;
                    $companyCode = $devices[$x]->companyCode;
                    
                    $alertQuery = AlertCron::select('*')
                     ->where('deviceId','=',$deviceId)
                     ->where('companyCode','=',$companyCode)
                     ->where('status','=','0')
                     ->get();
                     
                    $alertCount = $alertQuery->count();
                     
                    $this->alertCount += $alertCount;
                }
            }           
            
            $this->perPage = $request->input(key:'perPageData') == "" ? 100 : $request->input(key:'perPageData');
            $this->sort = $request->input(key:'sort') == "" ? "ASC" : $request->input(key:'sort');
            $this->column = $request->input(key:'column') == "" ? "id" : $request->input(key:'sort');
            $query->orderBy($this->column,$this->sort);           
            
            
            $this->page = $request->input(key:'page', default:1);
            $this->total = $query->count();    
            $this->result = $query->offset(value:($this->page - 1) * $this->perPage)->limit($this->perPage)->get();             
        }
    }    

    function getData(){
       return $returnedData[] = array(
            "data"=>$this->result,
            "sortedType"=>$this->sort,
            "totalData"=>$this->total,
            "perPageData"=>$this->perPage,
            "page"=>$this->page,
            "lastPage"=>ceil(num:$this->total/ $this->perPage),
            "alertCount"=>$this->alertCount                
       );
    }
}
