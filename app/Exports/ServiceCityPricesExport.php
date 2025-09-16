<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceCityPricesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return DB::table('service_city_prices as scp')
            ->leftJoin('services as s', 's.id', '=', 'scp.service_id')
            ->leftJoin('cities as c', 'c.id', '=', 'scp.city_id')
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'scp.category_id')
            ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'scp.sub_category_id')
            ->select(
                'scp.id',
                'c.name as city_name',
                'sc.name as category_name',
                'ssc.name as sub_category_name',
                's.name as service_name',
                'scp.price',
                'scp.discount_price',
                'scp.status',
                'scp.created_at'
            )
            ->get()
            ->map(function ($row) {
                // $price = (float) $row->price;
                // $discount = (float) $row->discount_price;
                // $finalPrice = $discount > 0 ? $price - $discount : $price;
                // $percent = ($price > 0 && $discount > 0) ? round(($discount / $price) * 100, 2) : 0;

                return [
                    'ID'             => $row->id,
                    'City'           => $row->city_name,
                    'Category'       => $row->category_name,
                    'Sub Category'       => $row->sub_category_name,
                    'Service Name'   => $row->service_name,
                    'Price'          => $row->price ?? 0,
                    'Discount Price' => $row->discount_price ?? 0,
                    // 'Final Price'    => number_format($finalPrice, 2),
                    // 'Discount %'     => $percent . '%',
                    'Status'         => $row->status ? 'Active' : 'Inactive',
                    'Created At'     => $row->created_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'City',
            'Category',
            'Sub Category',
            'Service Name',
            'Price',
            'Discount Price',
            // 'Final Price',
            // 'Discount %',
            'Status',
            'Created At',
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
