<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection,WithHeadings
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
            'a_date',
            'a_time',
            'deviceName',
            'labDepName',
            'sensorTag',
            'alertType',
            'Reason'
        ];
    }
}
