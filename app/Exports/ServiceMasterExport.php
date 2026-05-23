<?php

namespace App\Exports;

use App\Models\ServiceMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceMasterExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return ServiceMaster::leftJoin('service_categories as sc', 'sc.id', '=', 'service_masters.category_id')
            ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'service_masters.sub_category_id')
            ->select(
                'service_masters.id',
                'sc.name as category_name',
                'ssc.name as sub_category_name',
                'service_masters.name',
                'service_masters.skin_type',
                'service_masters.price',
                'service_masters.discount_price',
                'service_masters.duration',
                'service_masters.rating',
                'service_masters.reviews',
                'service_masters.description',
                'service_masters.is_popular',
                'service_masters.has_variants',
                'service_masters.status'
            )
            ->get()
            ->map(function ($row) {
                return [
                    'ID'                => $row->id,
                    'Category'          => $row->category_name,
                    'Sub Category'      => $row->sub_category_name,
                    'Name'              => $row->name,
                    'Skin Type'         => $row->skin_type,
                    'Price'             => $row->price ?? 0,
                    'Discount Price'    => $row->discount_price ?? 0,
                    'Duration'          => $row->duration,
                    'Rating'            => $row->rating,
                    'Reviews'           => $row->reviews,
                    'Description'       => $row->description,
                    'Is Popular'        => $row->is_popular ? 'Yes' : 'No',
                    'Has Variants'      => $row->has_variants ? 'Yes' : 'No',
                    'Status'            => $row->status ? 'Active' : 'Inactive',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Sub Category',
            'Name',
            'Skin Type',
            'Price',
            'Discount Price',
            'Duration',
            'Rating',
            'Reviews',
            'Description',
            'Is Popular',
            'Has Variants',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
        ];
    }
}
