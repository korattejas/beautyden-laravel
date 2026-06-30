{{-- Usage: @include('admin.partials.page-header', ['title' => 'Users', 'subtitle' => 'Manage customers', 'actions' => '<a href="..." class="pa-btn pa-btn-primary">Add</a>']) --}}
<div class="pa-page-header">
    <div>
        <h1>{{ $title ?? 'Page Title' }}</h1>
        @if(!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
    </div>
    @if(!empty($actions))
        <div class="pa-page-actions">{!! $actions !!}</div>
    @endif
</div>
