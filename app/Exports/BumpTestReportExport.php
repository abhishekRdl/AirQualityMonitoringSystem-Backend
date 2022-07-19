<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BumpTestReportExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $query;
    public function __construct($query){
        $this->query = $query;
      
    } 



    public function collection()
    {
        return $this->query->get();
    }


    public function headings(): array
    {
        return [
            'created_at',
            'stateName',
            'branchName',
            'facilityName',
            'buildingName',
            'floorName',
            'labDepName',
            'deviceName',
            'sensorTagName',
            'result',
            'lastDueDate'
        ];
    }
}
