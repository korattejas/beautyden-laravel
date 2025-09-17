@if ($action_array['is_simple_action'] == 1)
    <div class="btn-icon-group" role="group" aria-label="Basic example" style="width: 200px;">

        @if (isset($action_array['edit_route']))
            <a href="{{ $action_array['edit_route'] }}" class="btn btn-info btn-icon" data-toggle="tooltip"
                data-placement="top" title="EDIT">
                <i class="bx bx-pencil font-size-16 align-middle"></i>
            </a>
        @endif
        @if (isset($action_array['delete_id']))
            <button data-id="{{ $action_array['delete_id'] }}" class="delete-single btn btn-danger btn-icon"
                data-toggle="tooltip" data-placement="top" title="DELETE">
                <i class="bx bx-trash font-size-16 align-middle"></i>
            </button>
        @endif

        @if (isset($action_array['current_status']))
            @if ($action_array['current_status'] == '1')
                <button data-id="{{ $action_array['hidden_id'] }}" data-change-status="0"
                    class="status-change btn btn-success btn-icon" data-effect="effect-fall" data-toggle="tooltip"
                    data-placement="top" title="InActive">
                    <i class="bx bx-refresh font-size-16 align-middle"></i>
                </button>
            @else
                <button data-id="{{ $action_array['hidden_id'] }}" data-change-status="1"
                    class="status-change btn btn-success btn-icon" data-effect="effect-fall" data-toggle="tooltip"
                    data-placement="top" title="Active">
                    <i class="bx bx-refresh font-size-16 align-middle"></i>
                </button>
            @endif
        @endif

        @if (isset($action_array['current_is_popular_priority_status']))
            @if ($action_array['current_is_popular_priority_status'] == '1')
                <button data-id="{{ $action_array['hidden_id'] }}" data-priority-change-status="0"
                    class="priority-status-change btn btn-primary btn-icon" data-effect="effect-fall"
                    data-toggle="tooltip" data-placement="top" title="Low Priority">
                    <i class="bx bx-sync font-size-16 align-middle"></i>
                </button>
            @else
                <button data-id="{{ $action_array['hidden_id'] }}" data-priority-change-status="1"
                    class="priority-status-change btn btn-primary btn-icon" data-effect="effect-fall"
                    data-toggle="tooltip" data-placement="top" title="High Priority">
                    <i class="bx bx-sync font-size-16 align-middle"></i>
                </button>
            @endif
        @endif

        @if (isset($action_array['current_is_new_priority_status']))
            @if ($action_array['current_is_new_priority_status'] == '1')
                <button data-id="{{ $action_array['hidden_id'] }}" data-new-old-priority-status-change="0"
                    class="new-old-priority-status-change btn btn-warning btn-icon" data-effect="effect-fall"
                    data-toggle="tooltip" data-placement="top" title="Old Image">
                    <i class="bx bx-sync font-size-16 align-middle"></i>
                </button>
            @else
                <button data-id="{{ $action_array['hidden_id'] }}" data-new-old-priority-status-change="1"
                    class="new-old-priority-status-change btn btn-warning btn-icon" data-effect="effect-fall"
                    data-toggle="tooltip" data-placement="top" title="New Image">
                    <i class="bx bx-sync font-size-16 align-middle"></i>
                </button>
            @endif
        @endif

        @if (isset($action_array['view_id']))
            <button type="button" class="btn btn-dark btn-icon btn-view" data-id="{{ $action_array['view_id'] }}"
                data-toggle="tooltip" data-placement="top" title="VIEW">
                <i class="bx bx-bullseye font-size-16 align-middle"></i>
            </button>
        @endif

        @if (isset($action_array['pdf_id']))
        {{-- <button type="button" class="btn btn-dark btn-icon btn-view" data-id="{{ $action_array['view_id'] }}"
        data-toggle="tooltip" data-placement="top" title="VIEW">
        <i class="bx bx-bullseye font-size-16 align-middle"></i>
    </button> --}}
            <a type="button" href="{{ route('admin.appointments.pdf', $action_array['pdf_id']) }}" class="btn btn-dark btn-icon btn-pdf"
                title="Download PDF">
                <i class="bx bx-file"></i>
            </a>
        @endif

        @if (isset($action_array['assign_id']))
            <button data-id="{{ $action_array['assign_id'] }}" data-new-old-priority-status-change="0"
                class="assign-member btn btn-warning btn-icon" data-effect="effect-fall" data-toggle="tooltip"
                data-placement="top" title="Assign team member">
                <i class="bx bx-user-plus font-size-16 align-middle"></i>
            </button>
        @endif

    </div>
@endif
