<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorUnit extends Model
{
    use HasFactory;
    protected $table = "sensor_units";
    protected $fillable = [
        'companyCode',
        'sensorCategoryId',
        'sensorCategoryName',
        'sensorName',
        'manufacturer',
        'partId',
        'sensorOutput',
        'sensorType',
        'units',
        'minRatedReading',
        'minRatedReadingChecked',
        'minRatedReadingScale',
        'maxRatedReading',
        'maxRatedReadingChecked',
        'maxRatedReadingScale',
        'slaveId',
        'registerId',
        'length',
        'registerType',
        'conversionType',
        'ipAddress',
        'subnetMask',  

        'isStel',
        'stelDuration',
        'stelType',
        'stelLimit',
        'stelAlert',
        
        'twaDuration',
        'twaType',
        'twaLimit',
        'twaAlert',

        'alarm',
        'unLatchDuration',

        'isAQI',
        'parmGoodMinScale',
        'parmGoodMaxScale',
        
        'parmSatisfactoryMinScale',
        'parmSatisfactoryMaxScale',

        'parmModerateMinScale',
        'parmModerateMaxScale',

        'parmPoorMinScale',
        'parmPoorMaxScale',

        'parmVeryPoorMinScale',
        'parmVeryPoorMaxScale',

        'parmSevereMinScale',
        'parmSevereMaxScale',                
        
        'relayOutput',
        
        
    ];
}
