<?php

namespace App\Http\Controllers\UTILITY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataUtilityController extends Controller
{

    /* code for paginating data */
    
    protected $total = 0;
    protected $page = "";
    protected $perPage = 10;
    protected $result = "";  
    protected $sort = "";  
    protected $column = ""; 
    protected $data = "";
    protected $returnedData = [];
    protected $searchedKey = "";
    
    function __construct($request,$query) {
        if($query) {
            
            $this->perPage = $request->input(key:'perPageData') == "" ? $this->perPage : $request->input(key:'perPageData');
            $this->sort = $request->input(key:'sort') == "" ? "DESC" : $request->input(key:'sort');
            $this->column = $request->input(key:'column') == "" ? "id" : $request->input(key:'column');           
            
            $query->orderBy($this->column,$this->sort);        
            
            
            $this->total = $query->count();    //gets the count of data
            //$this->result = $query->get();

            $this->page = $request->input(key:'page', default:1);  //gets the page number of pagination or by default parameter will be one          
            $this->result = $query->offset(value:($this->page - 1) * $this->perPage)->limit($this->perPage)->get();    //data will be stored in  results 
                  
            
        }
    }    

    function getData(){                
        return $returnedData[] = array(          
                "data"=>$this->result,
                "sortedType"=>$this->sort,
                "totalData"=>$this->total,
                "perPageData"=>$this->perPage,
                "page"=>$this->page,
                'lastPage'=>ceil(num:$this->total/ $this->perPage)                
        );
        
       
    }
}
