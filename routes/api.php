<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\EmpUserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\LabDepartmentController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorCategoryController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SensorUnitController;
use App\Http\Controllers\ConfigSetupController;
use App\Http\Controllers\DeviceConfigSetupController;
use App\Http\Controllers\AqmiJsonDataController;
use App\Http\Controllers\AqiChartConfigValuesController;
use App\Http\Controllers\BumpTestResultController;
use App\Http\Controllers\CalibrationTestResultController;
use App\Http\Controllers\SampledSensorDataDetailsController;
use App\Http\Controllers\AlertCronController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AlertController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

#http://varmatrix.com/Aqms/blog/public/Customers/A-TEST/Buildings/devices/ConfigSettingFile/dataPush/AQISENSOR_DataPush.json
#http://127.0.0.1:8000/blog/app/Http/Controllers/CronJobController.php
#http://varmatrix.com/Aqms/blog/public/app/Http/Controllers/CronJobController.php


Route::group(['middleware' => ['web']], function () {    
    
});

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('block',function(){
    $response=[
         "message" => "Unable to access the page, Token Expired"
    ];
    return response($response, 401);
})->name('block');

Route::middleware(['auth:sanctum'])->group(function () {       

    //Authentication routes
    Route::post('sendOtp', [Authcontroller::class, 'sendOtp']);
    Route::post('requestToken', [AuthController::class, 'requestToken']);
    Route::post('resetUserPassword', [AuthController::class, 'resetUserPassword']);
    Route::post('blockedUserPasswordAutogenerate', [AuthController::class, 'blockedUserPasswordAutogenerate']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    //Company employee users
    Route::post('empuser/add', [EmpUserController::class, 'store']);
    Route::get('empuser', [EmpUserController::class, 'index']);
    Route::get('empuser/{id}/show', [EmpUserController::class, 'show']);//work in progress
    Route::post('empuser/{id}/update', [EmpUserController::class, 'update']);
    Route::post('empuser/{id}/delete', [EmpUserController::class, 'destroy']);  

    //Roles
    Route::post('role/add', [RoleController::class, 'store']);
    // Route::get('role', [RoleController::class, 'index']);
    Route::get('role/{id}/show', [RoleController::class, 'show']);
    Route::post('role/{id}/update', [RoleController::class, 'update']);
    Route::delete('role/{id}/delete', [RoleController::class, 'destroy']);

    //Customers
    Route::post('customer/add', [CustomerController::class, 'store']);
    Route::post('customer/{id}/update', [CustomerController::class, 'update']);
    Route::post('customer/{id}/delete', [CustomerController::class, 'destroy']);
    Route::get('customers', [CustomerController::class, 'customerCustomData']); 

    //locations   
    Route::post('location/add', [LocationController::class, 'store']);
    Route::get('location', [LocationController::class, 'index']);
    Route::post('location/{id}/update', [LocationController::class, 'update']);
    Route::delete('location/{id}/delete', [LocationController::class, 'destroy']);    

    //branches
    Route::post('branch/add', [BranchController::class, 'store']);
    Route::get('branch', [BranchController::class, 'index']);
    Route::post('branch/{id}/update', [BranchController::class, 'update']);
    Route::delete('branch/{id}/delete', [BranchController::class, 'destroy']);

    //facility
    Route::post('facility/add', [FacilitiesController::class, 'store']);
    Route::get('facility', [FacilitiesController::class, 'index']);
    Route::post('facility/{id}/update', [FacilitiesController::class, 'update']);
    Route::delete('facility/{id}/delete', [FacilitiesController::class, 'destroy']);

    //buildings
    Route::post('building/add', [BuildingController::class, 'store']);
    Route::get('building', [BuildingController::class, 'index']);
    Route::post('building/{id}/update', [BuildingController::class, 'update']);
    Route::delete('building/{id}/delete', [BuildingController::class, 'destroy']);

    //floors
    Route::post('floor/add', [FloorController::class, 'store']);
    Route::get('floor', [FloorController::class, 'index']);
    Route::post('floor/{id}/update', [FloorController::class, 'update']);
    Route::delete('floor/{id}/delete', [FloorController::class, 'destroy']);   
    
    
    //department
    Route::post('labDepartment/add', [LabDepartmentController::class, 'store']);
    Route::get('labDepartment', [LabDepartmentController::class, 'index']);
    Route::post('labDepartment/{id}/update', [LabDepartmentController::class, 'update']);
    Route::delete('labDepartment/{id}/delete', [LabDepartmentController::class, 'destroy']);    
    
    Route::post('search', [DataController::class, 'search']); //navigation api
    
    //vendor
    Route::post('vendor/add',[VendorController::class,'store']);
    Route::post('vendor/{id}/update',[VendorController::class,'update']);
    Route::delete('vendor/{id}/delete',[VendorController::class,'destroy']);
    Route::get('vendor', [VendorController::class, 'vendorCustomData']);   
   
    //devicecategory
    Route::post('category/add',[CategoriesController::class,'store']);
    Route::post('category/{id}/update',[CategoriesController::class,'update']);
    Route::delete('category/{id}/delete',[CategoriesController::class,'destroy']);
    Route::get('category', [CategoriesController::class, 'index']);
    
    //device
    Route::post('device/add',[DeviceController::class,'store']);
    Route::post('device/{id}/update',[DeviceController::class,'update']);
    Route::delete('device/{id}/delete',[DeviceController::class,'destroy']);
    Route::post('deviceMode/{id}/update',[DeviceController::class,'updateDeviceMode']);
    Route::get('device', [DeviceController::class, 'index']);
    
    //sensorCategory
    Route::post('sensorCategory/add',[SensorCategoryController::class,'store']);
    Route::post('sensorCategory/{id}/update',[SensorCategoryController::class,'update']);
    Route::delete('sensorCategory/{id}/delete',[SensorCategoryController::class,'destroy']);
    Route::get('sensorCategory', [SensorCategoryController::class, 'index']);  
    
    //sensor
    Route::POST('sensor', [SensorController::class, 'index']); 
    Route::post('sensor/add',[SensorController::class,'store']);
    Route::post('sensor/{id}/update',[SensorController::class,'update']);
    Route::delete('sensor/{id}/delete',[SensorController::class,'destroy']);
    Route::get('deviceDeployedSensors/{id}', [SensorController::class, 'deviceDeployedSensors']); 
    Route::get('sensorTag', [SensorController::class, 'getSensorTagData']);
    Route::post('sensorProperties/{id}/update', [SensorController::class, 'sensorPropertiesUpdate']);
    
    //sensorUnit
    Route::get('sensorUnit/{id}', [SensorUnitController::class, 'index']);
    Route::get('sensorUnit', [SensorUnitController::class, 'getData']);
    Route::post('sensorUnit/add',[SensorUnitController::class,'store']);
    Route::post('sensorUnit/{id}/update',[SensorUnitController::class,'update']);
    Route::delete('sensorUnit/{id}/delete',[SensorUnitController::class,'destroy']);
    
    //Config setup
    Route::get('configSetup', [ConfigSetupController::class, 'index']); 
    Route::post('configSetup/add',[ConfigSetupController::class,'store']);
    Route::post('configSetup/{id}/update',[ConfigSetupController::class,'update']);
    Route::delete('configSetup/{id}/delete',[ConfigSetupController::class,'destroy']);    

    //configSetup
    Route::post('DeviceConfigSetup/add',[DeviceConfigSetupController::class,'DeviceConfigAddOrUpdate']);
    Route::get('DeviceConfigSetup/{id}/getDeviceConfigData',[DeviceConfigSetupController::class,'getDeviceConfigData']);
    
    Route::post('stel/{id}/update',[SensorUnitController::class,'StelTwd']);

    Route::post('bumpTestResult/add',[BumpTestResultController::class,'store']);
    Route::get('bumpTestResult', [BumpTestResultController::class, 'index']); 

    Route::post('calibrationTestResult/add',[CalibrationTestResultController::class,'store']);
    Route::post('calibrationTestResult', [CalibrationTestResultController::class, 'index']);    
    
    Route::post('aqmiValues', [SampledSensorDataDetailsController::class, 'index']);

    Route::post('userListDetails', [AuthController::class, 'userListDetails']);      

    Route::post('sendMessage', [Authcontroller::class, 'sendMessage']);

    Route::post('userLog', [AuthController::class, 'UserLogDetails']);
   
    Route::post('alertData', [AlertCronController::class, 'show']);

    Route::post('alertDataUpdate', [AlertCronController::class, 'update']);   



});

//USED FOR QUERY WRITING
Route::post('query', [BumpTestResultController::class, 'sql']); 



//CURRENTLY FOR TESTING VALUE IT IS KEPT OUTSIDE AUTHENTICATION, AFTER COMPLETION NEED TO SHIFT TO AUTHENTICATION IN THE TOP
Route::post('aqmiSensorValues', [SampledSensorDataDetailsController::class, 'show']);
Route::post('aqmiDeviceSensorValues', [SampledSensorDataDetailsController::class, 'deviceSensorShow']);
Route::post('lastSampledValues', [SampledSensorDataDetailsController::class, 'lastSampledData']);
Route::post('sensorTagIdData', [SampledSensorDataDetailsController::class, 'getLastSampledDataOfSensorTagId']);
Route::post('lastUpdatedData', [SampledSensorDataDetailsController::class, 'liveDataDeviceId']);
//Route::post('sensorTagIdGraphData', [SampledSensorDataDetailsController::class, 'getLastSampledDataOfSensorTagIdBarLine']);

Route::post('/getAlerts', [AlertController::class, 'getAlertData']);

Route::get('AqiChart/add', [AqiChartConfigValuesController::class, 'store']);
Route::get('AqiChart', [AqiChartConfigValuesController::class, 'index']);
Route::get('aqmi', [AqmiJsonDataController::class, 'index']); 


//NOT USED APIS
Route::get('sensorTag', [SensorController::class, 'getSensorTagData']); 
Route::post('/uploadFile', [CustomerController::class, 'uploadImageFile']); 




/** Prajwal Reports api begin startDate 16-06-2022 */

//CURRENTLY FOR TESTING VALUE IT IS KEPT OUTSIDE AUTHENTICATION, AFTER COMPLETION NEED TO SHIFT TO AUTHENTICATION IN THE TOP
Route::get('reportBumpTest', [ReportController::class, 'reportBumpTest']); 
Route::get('alarmReport', [ReportController::class, 'alarmReport']); 



//local
// {
//     "email":"developer2@rdltech.in",
//     "password":"WgePhnShen"
// }

//server
// {
//     "email":"developer2@rdltech.in",
//     "password":"123456"
// }

//git remote add upstream  git@github.com:teamlead-rdl/AirQualityMonitoringSystem.git
//git remote
//git pull
//git pull upstream develop
//git branch










  









