@if($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '1')
    <span class="badge badge-glow bg-success">Active</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '0')
    <span class="badge badge-glow bg-danger">InActive</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '2')
    <span class="badge badge-glow bg-warning">Pending</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'completed')
    <span class="badge badge-glow bg-info">Completed</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'rejected')
    <span class="badge badge-glow bg-warning">Rejected</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'upcoming')
    <span class="badge badge-glow bg-warning">Upcoming</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'coming_soon')
    <span class="badge badge-glow bg-warning">Coming Soon</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_popular_priority_status']) && $status_array['current_is_popular_priority_status'] == '1' && $status_array['current_status'] == '3')
    <span class="badge badge-glow bg-dark">High Priority</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_popular_priority_status']) && $status_array['current_is_popular_priority_status'] == '0' && $status_array['current_status'] == '3')
    <span class="badge badge-glow bg-info">Low Priority</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_new_priority_status']) && $status_array['current_is_new_priority_status'] == '1' && $status_array['current_status'] == '4')
    <span class="badge badge-glow bg-success">New Image</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_new_priority_status']) && $status_array['current_is_new_priority_status'] == '0' && $status_array['current_status'] == '4')
    <span class="badge badge-glow bg-secondary">Old Image</span>
@endif
