<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;


class UtilityController extends Controller{
    
    protected $companyCode = "";  
    protected $userId = "";   
    protected $userRole = "";   
      
    
    
    function __construct(Request $request) {
        if($request->hasHeader('companyCode')) {
            $this->companyCode = $request->Header('companyCode');
        }

        if($request->hasHeader('userId')){
            $this->userId = $request->Header('userId');
        }

        if($request->hasHeader('userRole')){
            $this->userRole = $request->Header('userRole');
        }

    }

    function getCompanyCode(){
        return $this->companyCode;
    }

    function getUserId(){
        return $this->userId;
    }

    function getUserRole(){
        return $this->userRole;
    }
}





?>