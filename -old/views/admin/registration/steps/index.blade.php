@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Registration Steps Management')</h5>
                <div class="card-header-actions">
                    <a href="{{ route('admin.registration.steps.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> @lang('Add New Step')
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($steps->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Step')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Questions')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Required')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-steps">
                                @foreach($steps as $step)
                                    <tr data-id="{{ $step->id }}">
                                        <td>
                                            <span class="drag-handle">
                                                <i class="fas fa-grip-vertical"></i>
                                            </span>
                                            <span class="step-order">{{ $step->step_order }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $step->title }}</strong>
                                            @if($step->subtitle)
                                                <br><small class="text-muted">{{ $step->subtitle }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $step->questions->count() }} questions</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm toggle-status {{ $step->is_active ? 'btn-success' : 'btn-secondary' }}" 
                                                    data-id="{{ $step->id }}" data-status="{{ $step->is_active }}">
                                                {{ $step->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge {{ $step->is_required ? 'badge-warning' : 'badge-secondary' }}">
                                                {{ $step->is_required ? 'Required' : 'Optional' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.registration.steps.edit', $step->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-step" 
                                                        data-id="{{ $step->id }}" data-title="{{ $step->title }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5>@lang('No registration steps found')</h5>
                        <p class="text-muted">@lang('Create your first registration step to get started.')</p>
                        <a href="{{ route('admin.registration.steps.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> @lang('Add First Step')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirm Delete')</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>@lang('Are you sure you want to delete this registration step?')</p>
                <p><strong id="deleteStepTitle"></strong></p>
                <p class="text-danger">@lang('This action cannot be undone and will also delete all associated questions.')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">@lang('Delete')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Sortable functionality
    $("#sortable-steps").sortable({
        handle: '.drag-handle',
        update: function(event, ui) {
            var steps = [];
            $('#sortable-steps tr').each(function(index) {
                var stepId = $(this).data('id');
                if (stepId) {
                    steps.push({
                        id: stepId,
                        step_order: index + 1
                    });
                }
            });
            
            $.ajax({
                url: '{{ route("admin.registration.steps.reorder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    steps: steps
                },
                success: function(response) {
                    if (response.success) {
                        // Update step order numbers
                        $('#sortable-steps tr').each(function(index) {
                            $(this).find('.step-order').text(index + 1);
                        });
                        iziToast.success({
                            message: response.message,
                            position: "topRight"
                        });
                    }
                }
            });
        }
    });

    // Toggle status
    $('.toggle-status').click(function() {
        var stepId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var button = $(this);
        
        $.ajax({
            url: '{{ route("admin.registration.steps.toggle-status", ":id") }}'.replace(':id', stepId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.data('status', response.is_active);
                    if (response.is_active) {
                        button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                    } else {
                        button.removeClass('btn-success').addClass('btn-secondary').text('Inactive');
                    }
                    iziToast.success({
                        message: response.message,
                        position: "topRight"
                    });
                }
            }
        });
    });

    // Delete step
    $('.delete-step').click(function() {
        var stepId = $(this).data('id');
        var stepTitle = $(this).data('title');
        
        $('#deleteStepTitle').text(stepTitle);
        $('#deleteForm').attr('action', '{{ route("admin.registration.steps.destroy", ":id") }}'.replace(':id', stepId));
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
