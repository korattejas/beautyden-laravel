<?php

namespace App\Exports;

use App\Models\Appointment;
use App\Models\TeamMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    protected $month;
    protected $year;

    public function __construct($month = 'all', $year = 'all')
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $query = Appointment::query()
            ->leftJoin('cities as c', 'appointments.city_id', '=', 'c.id')
            ->select(
                'appointments.*',
                'c.name as city_name'
            );

        if ($this->month != 'all') {
            $query->whereMonth('appointments.appointment_date', $this->month);
        }

        if ($this->year != 'all') {
            $query->whereYear('appointments.appointment_date', $this->year);
        }

        return $query->orderBy('appointments.appointment_date', 'DESC')->get();
    }

    public function map($appointment): array
    {
        $status = '';
        switch ($appointment->status) {
            case '1': $status = 'Pending'; break;
            case '2': $status = 'Assigned'; break;
            case '3': $status = 'Completed'; break;
            case '4': $status = 'Rejected'; break;
            default: $status = 'Unknown';
        }

        $assignedTo = '';
        if (!empty($appointment->assigned_to)) {
            $memberIds = explode(',', $appointment->assigned_to);
            $assignedTo = TeamMember::whereIn('id', $memberIds)->pluck('name')->implode(', ');
        }

        $servicesData = $appointment->services_data;
        if (is_string($servicesData)) {
            $servicesData = json_decode($servicesData, true);
        }
        $grandTotal = $servicesData['summary']['grand_total'] ?? 0;

        return [
            $appointment->id,
            $appointment->order_number,
            $appointment->first_name . ' ' . $appointment->last_name,
            $appointment->phone,
            $appointment->email,
            $appointment->appointment_date,
            $appointment->appointment_time,
            $appointment->city_name,
            $appointment->service_address,
            $assignedTo,
            $grandTotal,
            $appointment->company_amount ?? 0,
            $status,
            $appointment->created_at->format('d-m-Y H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Order Number',
            'Client Name',
            'Phone',
            'Email',
            'Appointment Date',
            'Appointment Time',
            'City',
            'Address',
            'Assigned To',
            'Grand Total',
            'Company Amount',
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
