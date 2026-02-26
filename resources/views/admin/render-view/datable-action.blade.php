@if ($action_array['is_simple_action'] == 1)
    <div class="dropdown">
        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-haspopup="true" aria-expanded="false" style="background: transparent; border: none;">
            <i class="bi bi-three-dots-vertical" style="font-size: 1.5rem; color: #444050;"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius: 12px; border: none; padding: 0.8rem; min-width: 200px; z-index: 9999; background-color: #ffffff !important; box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;">
            
            @if (isset($action_array['view_id']))
                <button type="button" class="dropdown-item btn-view d-flex align-items-center py-50" data-id="{{ $action_array['view_id'] }}">
                    <i class="bi bi-eye-fill me-1 text-info"></i>
                    <span>Quick View</span>
                </button>
            @endif

            @if (isset($action_array['edit_route']))
                <a href="{{ $action_array['edit_route'] }}" class="dropdown-item d-flex align-items-center py-50">
                    <i class="bi bi-pencil-square me-1 text-primary"></i>
                    <span>Edit</span>
                </a>
            @endif

            @if (isset($action_array['assign_id']))
                <button data-id="{{ $action_array['assign_id'] }}" 
                    data-members="{{ $action_array['current_members'] ?? '' }}"
                    class="dropdown-item assign-member d-flex align-items-center py-50">
                    <i class="bi bi-person-plus-fill me-1 text-warning"></i>
                    <span>Assign Team</span>
                </button>
            @endif

            @if (isset($action_array['pdf_id']))
                <a href="{{ route('admin.appointments.pdf', $action_array['pdf_id']) }}" class="dropdown-item d-flex align-items-center py-50">
                    <i class="bi bi-file-pdf-fill me-1 text-danger"></i>
                    <span>Download Invoice</span>
                </a>
            @endif

            <div class="dropdown-divider"></div>

            @if (isset($action_array['delete_id']))
                <button data-id="{{ $action_array['delete_id'] }}" class="dropdown-item delete-single d-flex align-items-center py-50 text-danger">
                    <i class="bi bi-trash3-fill me-1"></i>
                    <span>Delete Record</span>
                </button>
            @endif

        </div>
    </div>
@endif
