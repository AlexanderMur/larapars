<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\ParsedCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ModelExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    public $ids;

    public function __construct($ids = [])
    {
        $this->ids = $ids;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->ids){
            return ParsedCompany::whereIn('id',$this->ids)->get();
        }
        return ParsedCompany::all();

    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Название',
            'Телефон',
            'Страница',
            'Сайт',
            'Город',
            'Адрес',
        ];
    }

    /**
     * @param Company $company
     * @return array
     */
    public function map($company): array
    {

        return [
            $company->title,
            $company->phone,
            $company->single_page_link,
            $company->site,
            $company->city,
            $company->address,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14)->setBold(700);
            },
        ];
    }
}
