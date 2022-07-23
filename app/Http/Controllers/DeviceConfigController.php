<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilityController;
use App\Models\Device;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Database\QueryException;

class DeviceConfigController extends Controller
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

        $perPage = 10;
        $page = $request->input(key:'page', default:1);
        $total = $query->count();

        $result = $query->offset(value:($page - 1) * $perPage)->limit($perPage)->get();                
        $response =  [
            'data' => $result,
            'totalData'=>$total,            
            'page'=>$page,
            'lastPage'=>ceil(num:$total/ $perPage)
        ];
        $status = 200;
        return response($response,$status);   
    }

    public function configDevice(Request $request){
        
        $ID = $request->ID;
        $CONFIG = $request->CONFIG;
        $channel = $request->CH;
        $status = $request->STATUS;
        
        // $macAddress = $request->MACADDRESS;
        // $deviceId = $request->DEVICEID;


        /*****************************SSID**********************************/

        if($ID == 'RDL456' && $CONFIG == 'SSID' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=10=linksys";
        }
        else if($ID == 'RDL456' && $CONFIG == 'SSID' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /******************************PASSWORD******************************/
        elseif($ID == 'RDL456' && $CONFIG == 'PASS' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=30=2020@RDL";
        }
        else if($CONFIG == 'PASS' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*************************ID****************************************/
        elseif($ID == 'RDL456' && $CONFIG == 'ID' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=50=RDL456";
        }
        else if($ID == 'RDL456' && $CONFIG == 'ID' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /**************************UPLODING URL***************************************/

        elseif($ID == 'RDL456' && $CONFIG == 'URL' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=80=http://industrypi.com/rdl_rnd_test/uploaddata.php";
        }
        else if($ID == 'RDL456' && $CONFIG == 'URL' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*************************OTA****************************************/

        elseif($ID == 'RDL456' && $CONFIG == 'OTA' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=200=http://industrypi.com/rdl_rnd_test/uploaddata.php";
        }
        else if($ID == 'RDL456' && $CONFIG == 'OTA' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /************************  PIROITY  ***************************************/

        elseif($ID == 'RDL456' && $CONFIG == 'PRTY' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=300=10";
        }
        else if($CONFIG == 'PRTY' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*****************************NO  PIROITY*******************************************/
        elseif($ID == 'RDL456' && $CONFIG == 'NONPRTY' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=310=60";
        }
        else if($CONFIG == 'NONPRTY' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*****************************ADC*****************************************/  
        elseif($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=500=1,1,1,1,1";  //ch1,ch2,ch3,ch4,PRY
        }
        else if($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*
        elseif($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '2' && $status == '0'){
            echo "RDL456-AT-WRITE=520=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '2' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        elseif($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '3' && $status == '0'){
            echo "RDL456-AT-WRITE=540=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '3' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        elseif($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '4' && $status == '0'){
            echo "RDL456-AT-WRITE=560=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'ADC' && $channel == '4' && $status == '1'){
            echo "RDL456-OK-WR";
        }*/

        /*****************************DIGTAL*****************************************/  
        elseif($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=600=1,1,1,1,2";   //ch1,ch2,ch3,ch4,PRY
        }
        else if($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }
        /*
        elseif($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '2' && $status == '0'){
            echo "RDL456-AT-WRITE=620=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '2' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        elseif($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '3' && $status == '0'){
            echo "RDL456-AT-WRITE=640=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '3' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        elseif($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '4' && $status == '0'){
            echo "RDL456-AT-WRITE=660=1,1";
        }
        else if($ID == 'RDL456' && $CONFIG == 'DIGTAL' && $channel == '4' && $status == '1'){
            echo "RDL456-OK-WR";
        }*/
        /**************************************MODBUS *******************************************/
        else if($ID == 'RDL456' && $CONFIG == 'METER' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-METER=4";
        }
        else if($ID == 'RDL456' && $CONFIG == 'METER' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '1' && $status == '0'){
            echo "RDL456-AT-WRITE=1010=1,3,25,2,20";   //SLAVE ID ,FC ,ADDRESS,LENG,PRTY
        }
        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '1' && $status == '1'){
            echo "RDL456-OK-WR";
        }

        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '2' && $status == '0'){
            echo "RDL456-AT-WRITE=1060=1,3,45,2,10";   //SLAVE ID ,FC ,ADDRESS,LENG,PRTY
        }
        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '2' && $status == '1'){
            echo "RDL456-OK-WR";
        }


        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '3' && $status == '0'){
            echo "RDL456-AT-WRITE=1110=1,3,10,2,15";   //SLAVE ID ,FC ,ADDRESS,LENG,PRTY
        }
        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '3' && $status == '1'){
            echo "RDL456-OK-WR";
        }


        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '4' && $status == '0'){
            echo "RDL456-AT-WRITE=1160=1,3,55,2,30";   //SLAVE ID ,FC ,ADDRESS,LENG,PRTY
        }
        else if($ID == 'RDL456' && $CONFIG == 'MODBUS' && $channel == '4' && $status == '1'){
            echo "RDL456-OK-WR";
        }



        else
        {
            echo "kanwal"; 
        }
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
}
