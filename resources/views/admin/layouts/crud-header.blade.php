{{--
    Dashboard-style page header for CRUD pages (matches Profile / Dashboard design).
    Usage:
        @section('page_heading', 'Add Appointment')
        @include('admin.layouts.crud-header', [
            'title' => 'Add Appointment',
            'subtitle' => 'Fill in client details and select services',
            'items' => [
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Appointments', 'url' => route('admin.appointments.index')],
                ['label' => 'Add Appointment'],
            ],
        ])
--}}
@php
    $items = $items ?? [];
    $title = $title ?? (collect($items)->last()['label'] ?? '');
    $subtitle = $subtitle ?? null;
@endphp
<div class="pa-dashboard-header pa-crud-page-header">
    <div>
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @elseif (count($items) > 1)
            <div class="pa-breadcrumb-trail">
                @foreach ($items as $index => $item)
                    @if ($index > 0)
                        <span class="pa-breadcrumb-sep" aria-hidden="true">·</span>
                    @endif
                    @if (! empty($item['url']) && $index < count($items) - 1)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        <span @if($index === count($items) - 1) class="current" @endif>{{ $item['label'] }}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    @isset($actions)
        <div class="pa-page-actions">{!! $actions !!}</div>
    @endisset
</div>
