@if($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '1')
    <span class="badge badge-glow bg-success">Active</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '0')
    <span class="badge badge-glow bg-danger">Inactive</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == '2')
    <span class="badge badge-glow bg-warning text-dark">Pending</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'completed')
    <span class="badge badge-glow bg-primary">Completed</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'rejected')
    <span class="badge badge-glow bg-danger">Rejected</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'upcoming')
    <span class="badge badge-glow bg-info text-dark">Upcoming</span>

@elseif($status_array['is_simple_active'] == 1 && $status_array['current_status'] == 'coming_soon')
    <span class="badge badge-glow bg-secondary">Coming Soon</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_popular_priority_status']) && $status_array['current_is_popular_priority_status'] == '1' && $status_array['current_status'] == '3')
    <span class="badge badge-glow bg-danger">High Priority</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_popular_priority_status']) && $status_array['current_is_popular_priority_status'] == '0' && $status_array['current_status'] == '3')
    <span class="badge badge-glow bg-secondary">Low Priority</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_new_priority_status']) && $status_array['current_is_new_priority_status'] == '1' && $status_array['current_status'] == '4')
    <span class="badge badge-glow bg-success">New Image</span>

@elseif($status_array['is_simple_active'] == 1 && isset($status_array['current_is_new_priority_status']) && $status_array['current_is_new_priority_status'] == '0' && $status_array['current_status'] == '4')
    <span class="badge badge-glow bg-dark">Old Image</span>
@endif
