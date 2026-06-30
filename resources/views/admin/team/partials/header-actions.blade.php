<a href="{{ route('admin.team.exportActive') }}" class="pa-btn pa-btn-outline">
    <i class="bi bi-file-earmark-excel"></i> Export
</a>
<a href="{{ route('admin.team.create') }}" class="pa-btn pa-btn-primary">
    <i class="bi bi-plus-lg"></i> Add
</a>
<div class="btn-group">
    <button class="pa-btn pa-btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-funnel"></i> Filter
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
        <div class="mb-2">
            <label class="form-label">Status</label>
            <select id="filter-status" class="form-select">
                <option value="">All</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">Is Popular</label>
            <select id="filter-popular" class="form-select">
                <option value="">All</option>
                <option value="1" {{ request('popular') == '1' ? 'selected' : '' }}>High Priority</option>
                <option value="0" {{ request('popular') == '0' ? 'selected' : '' }}>Low Priority</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">Experience (Years)</label>
            <select id="filter-year-of-experience" class="form-select">
                <option value="">All</option>
                @for ($i = 0; $i <= 10; $i++)
                    @php $val = ($i < 10) ? $i : '10+'; @endphp
                    <option value="{{ $val }}" {{ request('year_of_experience') == (string)$val ? 'selected' : '' }}>
                        {{ $i < 10 ? $i . ' year' . ($i > 1 ? 's' : '') : '10+ years' }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label class="form-label">Month</label>
                <select id="filter-month" class="form-select">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Year</label>
                <select id="filter-year" class="form-select">
                    <option value="">All Years</option>
                    @foreach(range(date('Y'), 2020) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Created Date</label>
            <input type="date" id="filter-created-date" class="form-control" value="{{ request('created_date') }}">
        </div>
        <div class="d-flex justify-content-between pt-1 gap-2">
            <button id="btn-apply-card-filters" type="button" class="pa-btn pa-btn-primary flex-fill">Apply</button>
            <button id="btn-reset-card-filters" type="button" class="pa-btn pa-btn-outline flex-fill">Reset</button>
        </div>
    </div>
</div>
