@if(isset($action_array['is_link']) && $action_array['is_link']==1)
    <div class="link__menu">
        <a href="{{ $action_array['link_url'] }}">
            {{ $action_array['id'] }}
        </a>
    </div>
@else
    {{ $action_array['id'] }}
@endif
