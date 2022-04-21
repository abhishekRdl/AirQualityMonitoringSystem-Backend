<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceConfig extends Model
{
    use HasFactory;
    protected $table = "device_configs";
    protected $fillable = [
        'companyCode', 
        'location_id',
        'branch_id',
        'facility_id',
        'building_id',
        'category_id',
        'categoryName',
        'device_id',
        'deviceName',
        'ssId',
        'ssIdPassword',
        'serverUrl',
        'deviceUrl',
        'firmwareUpdateUrl',
        'ftpServerUrl',
        'userName',
        'ftpPassword',
        'port',
        'apn',
        'jsonUrl'
    ];

}
