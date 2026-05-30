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
        $services = ServiceMaster::select(
                'service_masters.id',
                'service_masters.category_id',
                'service_masters.sub_category_id',
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
                'service_masters.status',
                'sc.name as category_name',
                'ssc.name as sub_category_name'
            )
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_masters.category_id')
            ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'service_masters.sub_category_id')
            ->with('variants')
            ->orderBy('sc.name', 'asc')
            ->orderBy('service_masters.name', 'asc')
            ->get();

        $rows = [];

        foreach ($services as $service) {
            // Main Service Row
            $rows[] = [
                'ID'                => $service->id,
                'Type'              => 'Service',
                'Category'          => $service->category_name,
                'Sub Category'      => $service->sub_category_name,
                'Service Name'      => $service->name,
                'Variant Name'      => '-',
                'Skin Type'         => $service->skin_type,
                'Price'             => $service->price ?? 0,
                'Discount'          => $service->discount_price ?? 0,
                'Duration'          => $service->duration,
                'Rating'            => $service->rating,
                'Reviews'           => $service->reviews,
                'Description'       => $service->description,
                'Is Popular'        => $service->is_popular ? 'Yes' : 'No',
                'Status'            => $service->status ? 'Active' : 'Inactive',
            ];

            // Variant Rows
            if ($service->has_variants && $service->variants->count() > 0) {
                foreach ($service->variants as $variant) {
                    $rows[] = [
                        'ID'                => $variant->id,
                        'Type'              => 'Variant',
                        'Category'          => $service->category_name,
                        'Sub Category'      => $service->sub_category_name,
                        'Service Name'      => $service->name,
                        'Variant Name'      => $variant->name,
                        'Skin Type'         => '-',
                        'Price'             => $variant->price ?? 0,
                        'Discount'          => $variant->discount_percentage ? $variant->discount_percentage . '%' : '0',
                        'Duration'          => $variant->duration ?? '-',
                        'Rating'            => $variant->rating ?? 0,
                        'Reviews'           => $variant->reviews ?? 0,
                        'Description'       => $variant->description ?? '-',
                        'Is Popular'        => '-',
                        'Status'            => '-',
                    ];
                }
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Type',
            'Category',
            'Sub Category',
            'Service Name',
            'Variant Name',
            'Skin Type',
            'Price',
            'Discount',
            'Duration',
            'Rating',
            'Reviews',
            'Description',
            'Is Popular',
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
