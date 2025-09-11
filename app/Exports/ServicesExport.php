<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Service::leftJoin('service_categories as sc', 'sc.id', '=', 'services.category_id')
            ->select(
                'services.id',
                'sc.name as category_name',
                'services.name',
                'services.price',
                'services.discount_price',
                'services.duration',
                'services.rating',
                'services.reviews',
                'services.description',
                'services.includes',
                'services.status',
                'services.is_popular'
            )
            ->get()
            ->map(function ($row) {
                $price = (float) $row->price;
                $discount = (float) $row->discount_price;
                $total = $discount > 0 ? $price - $discount : $price;
                $percent = ($price > 0 && $discount > 0) ? round(($discount / $price) * 100, 2) : 0;

                return [
                    'ID'            => $row->id,
                    'Category'      => $row->category_name,
                    'Name'          => $row->name,
                    'Price'         => number_format($price, 2),
                    'Discount Price' => $discount > 0 ? number_format($discount, 2) : '-',
                    'Total Price'   => number_format($total, 2),
                    'Discount %'    => $percent . '%',
                    'Duration'      => $row->duration,
                    'Rating'        => $row->rating,
                    'Reviews'       => $row->reviews,
                    'Description'   => $row->description,
                    'Includes'      => $row->includes ? implode(', ', json_decode($row->includes, true)) : '-',
                    'Status'        => $row->status ? 'Active' : 'Inactive',
                    'Is Popular'    => $row->is_popular ? 'Yes' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Name',
            'Price',
            'Discount Price',
            'Total Price',
            'Discount %',
            'Duration',
            'Rating',
            'Reviews',
            'Description',
            'Includes',
            'Status',
            'Is Popular',
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
