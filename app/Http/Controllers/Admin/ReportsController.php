<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;

class ReportsController extends Controller
{
    private const ROLE_CLIENT = 1;
    private const ROLE_BEAUTICIAN = 2;

    private const STATUS_PENDING = 1;
    private const STATUS_ASSIGNED = 2;
    private const STATUS_COMPLETED = 3;
    private const STATUS_REJECTED = 4;

    public function index()
    {
        $completedStatus = self::STATUS_COMPLETED;

        $totalRevenue = $this->sumAppointmentRevenue(
            Appointment::where('status', $completedStatus)->get()
        ) + (float) UserSubscription::sum('price_paid');

        $totalAppointments = Appointment::count();
        $totalCompletedAppointments = Appointment::where('status', $completedStatus)->count();

        $totalClients = User::where('role', self::ROLE_CLIENT)->count();
        $totalBeauticians = User::where('role', self::ROLE_BEAUTICIAN)->count();
        $totalSubscriptions = UserSubscription::count();

        $series = $this->buildAllTimeMonthSeries($completedStatus);

        return view('admin.reports.index', array_merge(compact(
            'totalRevenue',
            'totalAppointments',
            'totalCompletedAppointments',
            'totalClients',
            'totalBeauticians',
            'totalSubscriptions'
        ), $series));
    }

    private function buildAllTimeMonthSeries(int $completedStatus): array
    {
        $months = $this->initMonthBuckets();

        Appointment::whereNotNull('appointment_date')
            ->selectRaw('YEAR(appointment_date) as y, MONTH(appointment_date) as m, status, COUNT(*) as total')
            ->groupByRaw('YEAR(appointment_date), MONTH(appointment_date), status')
            ->get()
            ->each(function ($row) use (&$months) {
                $key = sprintf('%04d-%02d', $row->y, $row->m);
                if (! isset($months[$key])) {
                    return;
                }

                match ((int) $row->status) {
                    self::STATUS_PENDING => $months[$key]['pending'] = (int) $row->total,
                    self::STATUS_ASSIGNED => $months[$key]['assigned'] = (int) $row->total,
                    self::STATUS_COMPLETED => $months[$key]['completed'] = (int) $row->total,
                    self::STATUS_REJECTED => $months[$key]['rejected'] = (int) $row->total,
                    default => null,
                };
            });

        Appointment::where('status', $completedStatus)
            ->whereNotNull('appointment_date')
            ->get(['appointment_date', 'services_data'])
            ->each(function ($app) use (&$months) {
                $key = Carbon::parse($app->appointment_date)->format('Y-m');
                if (isset($months[$key])) {
                    $months[$key]['revenue'] += (float) ($app->services_data['summary']['grand_total'] ?? 0);
                }
            });

        UserSubscription::selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(price_paid) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->each(function ($row) use (&$months) {
                $key = sprintf('%04d-%02d', $row->y, $row->m);
                if (isset($months[$key])) {
                    $months[$key]['revenue'] += (float) $row->total;
                }
            });

        User::where('role', self::ROLE_CLIENT)
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COUNT(*) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->each(function ($row) use (&$months) {
                $key = sprintf('%04d-%02d', $row->y, $row->m);
                if (isset($months[$key])) {
                    $months[$key]['clients'] = (int) $row->total;
                }
            });

        User::where('role', self::ROLE_BEAUTICIAN)
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COUNT(*) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->each(function ($row) use (&$months) {
                $key = sprintf('%04d-%02d', $row->y, $row->m);
                if (isset($months[$key])) {
                    $months[$key]['beauticians'] = (int) $row->total;
                }
            });

        return [
            'monthLabels' => array_values(array_column($months, 'label')),
            'revenueMonthCounts' => array_values(array_map(fn ($m) => round($m['revenue'], 2), $months)),
            'pendingMonthCounts' => array_values(array_column($months, 'pending')),
            'assignedMonthCounts' => array_values(array_column($months, 'assigned')),
            'completedMonthCounts' => array_values(array_column($months, 'completed')),
            'rejectedMonthCounts' => array_values(array_column($months, 'rejected')),
            'clientMonthCounts' => array_values(array_column($months, 'clients')),
            'beauticianMonthCounts' => array_values(array_column($months, 'beauticians')),
        ];
    }

    private function initMonthBuckets(): array
    {
        $candidates = collect([
            Appointment::min('appointment_date'),
            User::whereIn('role', [self::ROLE_CLIENT, self::ROLE_BEAUTICIAN])->min('created_at'),
            UserSubscription::min('created_at'),
        ])->filter();

        $start = $candidates->isEmpty()
            ? now()->startOfMonth()
            : Carbon::parse($candidates->min())->startOfMonth();

        $end = now()->startOfMonth();
        $months = [];
        $month = $start->copy();

        while ($month <= $end) {
            $months[$month->format('Y-m')] = [
                'label' => $month->format('M Y'),
                'revenue' => 0,
                'pending' => 0,
                'assigned' => 0,
                'completed' => 0,
                'rejected' => 0,
                'clients' => 0,
                'beauticians' => 0,
            ];
            $month->addMonth();
        }

        return $months;
    }

    private function sumAppointmentRevenue($appointments): float
    {
        return $appointments->sum(function ($app) {
            return (float) ($app->services_data['summary']['grand_total'] ?? 0);
        });
    }
}
