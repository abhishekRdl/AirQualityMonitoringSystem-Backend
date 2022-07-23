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
use App\Http\Middleware\CheckReportHeaders;
use App\Http\Controllers\DeviceConfigController;





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

Route::post('configDevice', [DeviceConfigController::class, 'configDevice']);


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

Route::post('/updateCustomerSettings', [CustomerController::class, 'updateCustomerSettings']);

Route::get('AqiChart/add', [AqiChartConfigValuesController::class, 'store']);
Route::get('AqiChart', [AqiChartConfigValuesController::class, 'index']);
Route::get('aqmi', [AqmiJsonDataController::class, 'index']); 


//NOT USED APIS
Route::get('sensorTag', [SensorController::class, 'getSensorTagData']); 
Route::post('/uploadFile', [CustomerController::class, 'uploadImageFile']); 

Route::get('export', [RoleController::class, 'export']);







/** Prajwal Reports api begin startDate 16-06-2022 */

//CURRENTLY FOR TESTING VALUE IT IS KEPT OUTSIDE AUTHENTICATION, AFTER COMPLETION NEED TO SHIFT TO AUTHENTICATION IN THE TOP
Route::get('reportBumpTest', [ReportController::class, 'reportBumpTest']); 
Route::get('alarmReport', [ReportController::class, 'alarmReport']); 
Route::get('exportAlarm', [ReportController::class, 'exportAlarm'])->middleware(CheckReportHeaders::class);
Route::get('exportBumpTestCsv', [ReportController::class, 'exportBumpTest'])->middleware(CheckReportHeaders::class);

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



/** Below commnets are of Git commands  */
#git remote add upstream  git@github.com:teamlead-rdl/AirQualityMonitoringSystem.git
#git remote
#git pull
#git pull upstream develop
#git branch

/** Accessing file in laravel */
#http://varmatrix.com/Aqms/blog/public/Customers/A-TEST/Buildings/devices/ConfigSettingFile/dataPush/AQISENSOR_DataPush.json

/** Below command creating model, controller and model */
#php artisan make:model Facilities -c -m -r

/** Below command is to create excel file download in laravel based in model that is sql table */
#composer require maatwebsite/excel --ignore-platform-reqs installing excel
#php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
#php artisan make:export RoleExport --model=Role creating excel file



#below code can be used for periodic backup which make data receiver sooner
#to fetch count data in aqmi_json_table
#SELECT count(id) FROM `aqmi_json_data` 

#Delete FROM `aqmi_json_data` order by id ASC limit 42000


//Testing done for hooter relay status
#SELECT * FROM `sampled_sensor_data_details` where sensor_id = 52 order by id desc


// $sql = "SELECT customers.customerId, locations.stateName, branches.branchName, facilities.facilityName, buildings.buildingName, floors.floorName, lab_departments.labDepName, devices.deviceName, sensors.sensorTag , bump_test_results.nextDueDate FROM customers 
// INNER JOIN locations ON customers.customerId = locations.companyCode 
// INNER JOIN branches ON customers.customerId = branches.companyCode AND locations.id = branches.location_id 
// INNER JOIN facilities ON customers.customerId = facilities.companyCode AND locations.id = facilities.location_id AND branches.id = facilities.branch_id 
// INNER JOIN buildings ON customers.customerId = buildings.companyCode AND  locations.id = buildings.location_id  AND branches.id = buildings.branch_id AND facilities.id = buildings.facility_id
// INNER JOIN floors ON customers.customerId = floors.companyCode AND  locations.id = floors.location_id  AND  branches.id = floors.branch_id AND  facilities.id = floors.facility_id AND buildings.id = floors.building_id
// INNER JOIN lab_departments ON customers.customerId = lab_departments.companyCode AND  locations.id = lab_departments.location_id  AND  branches.id = lab_departments.branch_id AND  facilities.id = lab_departments.facility_id AND buildings.id = lab_departments.building_id AND floors.id = lab_departments.floor_id 
// INNER JOIN devices ON customers.customerId = devices.companyCode AND  locations.id = devices.location_id  AND  branches.id = devices.branch_id AND  facilities.id = devices.facility_id AND buildings.id = devices.building_id AND floors.id = devices.floor_id AND lab_departments.id = devices.lab_id
// INNER JOIN sensors ON customers.customerId = sensors.companyCode AND  locations.id = sensors.location_id  AND  branches.id = sensors.branch_id AND  facilities.id = sensors.facility_id AND buildings.id = sensors.building_id AND floors.id = sensors.floor_id AND lab_departments.id = sensors.lab_id AND devices.id = sensors.deviceid
// INNER JOIN bump_test_results ON sensors.sensorTag = bump_test_results.sensorTagName
// where customers.customerId = 'A-TEST' AND sensors.sensorTag = 'O3_gas1' ORDER BY bump_test_results.id DESC LIMIT 1";

// SELECT customers.customerId, locations.stateName, branches.branchName, facilities.facilityName, buildings.buildingName, floors.floorName, lab_departments.labDepName, devices.deviceName, sensors.sensorTag , bump_test_results.nextDueDate FROM customers 
// INNER JOIN locations ON customers.customerId = locations.companyCode 
// INNER JOIN branches ON customers.customerId = branches.companyCode AND locations.id = branches.location_id 
// INNER JOIN facilities ON customers.customerId = facilities.companyCode AND locations.id = facilities.location_id AND branches.id = facilities.branch_id 
// INNER JOIN buildings ON customers.customerId = buildings.companyCode AND  locations.id = buildings.location_id  AND branches.id = buildings.branch_id AND facilities.id = buildings.facility_id
// INNER JOIN floors ON customers.customerId = floors.companyCode AND  locations.id = floors.location_id  AND  branches.id = floors.branch_id AND  facilities.id = floors.facility_id AND buildings.id = floors.building_id
// INNER JOIN lab_departments ON customers.customerId = lab_departments.companyCode AND  locations.id = lab_departments.location_id  AND  branches.id = lab_departments.branch_id AND  facilities.id = lab_departments.facility_id AND buildings.id = lab_departments.building_id AND floors.id = lab_departments.floor_id 
// INNER JOIN devices ON customers.customerId = devices.companyCode AND  locations.id = devices.location_id  AND  branches.id = devices.branch_id AND  facilities.id = devices.facility_id AND buildings.id = devices.building_id AND floors.id = devices.floor_id AND lab_departments.id = devices.lab_id
// INNER JOIN sensors ON customers.customerId = sensors.companyCode AND  locations.id = sensors.location_id  AND  branches.id = sensors.branch_id AND  facilities.id = sensors.facility_id AND buildings.id = sensors.building_id AND floors.id = sensors.floor_id AND lab_departments.id = sensors.lab_id AND devices.id = sensors.deviceid
// where customers.customerId = 'A-TEST' AND sensors.sensorTag = 'O3_gas1' ORDER BY bump_test_results.id DESC LIMIT 1








  









