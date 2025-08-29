@if(isset($action_array['is_link']) && $action_array['is_link']==1)
    <div class="d-flex align-items-center link__menu">
        <a href="{{ $action_array['link_url'] }}" class="d-flex align-items-center">
            <div class="avatar bg-light-success me-1">
                <div class="avatar-content">
                    <img src="{{ $action_array['image_url'] }}" height="32" width="32"
                         alt="{{ $action_array['name'] }}">
                </div>
            </div>
            <span>{{ $action_array['name'] }}</span>
        </a>
    </div>
@else
    <div class="d-flex align-items-center">
        <div class="avatar bg-light-success me-1">
            <div class="avatar-content">
                <img src="{{ $action_array['image_url'] }}" height="32" width="32" alt="{{ $action_array['name'] }}">
            </div>
        </div>
        <span>{{ $action_array['name'] }}</span>
    </div>
@endif
