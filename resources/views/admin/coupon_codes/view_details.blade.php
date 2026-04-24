<div class="c-row">
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Coupon Code</label>
            <p class="fw-bold text-primary fs-4">{{ $coupon->code }}</p>
        </div>
    </div>
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Status</label>
            <p>
                @if($coupon->status == 1)
                    <span class="badge badge-glow bg-success">Active</span>
                @else
                    <span class="badge badge-glow bg-danger">Inactive</span>
                @endif
            </p>
        </div>
    </div>
    
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Discount</label>
            <p>
                @if($coupon->discount_type == 'percentage')
                    {{ $coupon->discount_value }}%
                @else
                    ₹{{ number_format($coupon->discount_value, 2) }}
                @endif
            </p>
        </div>
    </div>
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Min Purchase Amount</label>
            <p>₹{{ number_format($coupon->min_purchase_amount, 2) }}</p>
        </div>
    </div>

    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Max Discount Amount</label>
            <p>{{ $coupon->max_discount_amount ? '₹'.number_format($coupon->max_discount_amount, 2) : 'No Limit' }}</p>
        </div>
    </div>
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>First Order Only?</label>
            <p>
                @if($coupon->is_first_order_only)
                    <span class="badge bg-light-info">Yes</span>
                @else
                    <span class="badge bg-light-secondary">No</span>
                @endif
            </p>
        </div>
    </div>

    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Validity Period</label>
            <p>
                <i class="bi bi-calendar-check me-1"></i> {{ $coupon->start_date->format('d M, Y') }} 
                to 
                <i class="bi bi-calendar-x me-1"></i> {{ $coupon->end_date->format('d M, Y') }}
            </p>
        </div>
    </div>
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Usage Limits</label>
            <p>
                Total: {{ $coupon->usage_limit ?? 'Unlimited' }}<br>
                Per User: {{ $coupon->usage_per_user ?? 'Unlimited' }}
            </p>
        </div>
    </div>

    <div class="c-col-12">
        <div class="c-detail-card">
            <label>Description</label>
            <p>{{ $coupon->description ?? 'No description provided.' }}</p>
        </div>
    </div>

    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Created At</label>
            <p>{{ $coupon->created_at->format('d-m-Y H:i') }}</p>
        </div>
    </div>
    <div class="c-col-6">
        <div class="c-detail-card">
            <label>Last Updated</label>
            <p>{{ $coupon->updated_at->format('d-m-Y H:i') }}</p>
        </div>
    </div>
</div>
