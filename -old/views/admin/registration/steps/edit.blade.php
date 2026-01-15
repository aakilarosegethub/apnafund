@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Edit Registration Step')</h5>
                <div class="card-header-actions">
                    <a href="{{ route('admin.registration.steps.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> @lang('Back to Steps')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.registration.steps.update', $step->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">@lang('Step Title') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ old('title', $step->title) }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="subtitle">@lang('Step Subtitle')</label>
                                <textarea class="form-control" id="subtitle" name="subtitle" rows="2">{{ old('subtitle', $step->subtitle) }}</textarea>
                                @error('subtitle')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="step_order">@lang('Step Order') <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="step_order" name="step_order" 
                                       value="{{ old('step_order', $step->step_order) }}" min="1" required>
                                @error('step_order')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $step->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_required" name="is_required" 
                                           value="1" {{ old('is_required', $step->is_required) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">
                                        @lang('Required Step')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('Update Step')
                        </button>
                        <a href="{{ route('admin.registration.steps.index') }}" class="btn btn-secondary">
                            @lang('Cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Step Information')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <strong>@lang('Questions'):</strong><br>
                        <span class="badge badge-info">{{ $step->questions->count() }}</span>
                    </div>
                    <div class="col-6">
                        <strong>@lang('Status'):</strong><br>
                        <span class="badge {{ $step->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $step->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <strong>@lang('Required'):</strong><br>
                        <span class="badge {{ $step->is_required ? 'badge-warning' : 'badge-secondary' }}">
                            {{ $step->is_required ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>@lang('Order'):</strong><br>
                        <span class="badge badge-primary">{{ $step->step_order }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Questions in this Step')</h5>
                <div class="card-header-actions">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addQuestionModal">
                        <i class="fas fa-plus"></i> @lang('Add Question')
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($step->questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Field Name')</th>
                                    <th>@lang('Label')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Required')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-questions">
                                @foreach($step->questions->sortBy('order') as $question)
                                    <tr data-id="{{ $question->id }}">
                                        <td>
                                            <span class="drag-handle">
                                                <i class="fas fa-grip-vertical"></i>
                                            </span>
                                            <span class="question-order">{{ $question->order }}</span>
                                        </td>
                                        <td><code>{{ $question->field_name }}</code></td>
                                        <td>{{ $question->label }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($question->type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $question->is_required ? 'badge-warning' : 'badge-secondary' }}">
                                                {{ $question->is_required ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm toggle-question-status {{ $question->is_active ? 'btn-success' : 'btn-secondary' }}" 
                                                    data-id="{{ $question->id }}" data-status="{{ $question->is_active }}">
                                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary edit-question" 
                                                        data-question="{{ json_encode($question) }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-question" 
                                                        data-id="{{ $question->id }}" data-label="{{ $question->label }}">
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
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h5>@lang('No questions in this step')</h5>
                        <p class="text-muted">@lang('Add questions to collect information from users.')</p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addQuestionModal">
                            <i class="fas fa-plus"></i> @lang('Add First Question')
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Question')</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addQuestionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="field_name">@lang('Field Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="field_name" name="field_name" required>
                                <small class="form-text text-muted">@lang('Unique identifier for this field (e.g., business_name)')</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">@lang('Field Type') <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">@lang('Select type')</option>
                                    <option value="text">@lang('Text')</option>
                                    <option value="email">@lang('Email')</option>
                                    <option value="tel">@lang('Phone')</option>
                                    <option value="password">@lang('Password')</option>
                                    <option value="textarea">@lang('Textarea')</option>
                                    <option value="select">@lang('Select Dropdown')</option>
                                    <option value="checkbox">@lang('Checkbox')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="label">@lang('Label') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="label" name="label" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="placeholder">@lang('Placeholder')</label>
                        <input type="text" class="form-control" id="placeholder" name="placeholder">
                    </div>
                    
                    <div class="form-group">
                        <label for="help_text">@lang('Help Text')</label>
                        <textarea class="form-control" id="help_text" name="help_text" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order">@lang('Order') <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="order" name="order" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="is_required" name="is_required" value="1" checked>
                                    <label class="form-check-label" for="is_required">
                                        @lang('Required')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="optionsContainer" style="display: none;">
                        <div class="form-group">
                            <label>@lang('Options')</label>
                            <div id="optionsList">
                                <div class="option-item mb-2">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="options[0][value]" placeholder="@lang('Value')">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="options[0][label]" placeholder="@lang('Label')">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-option">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="addOption">
                                <i class="fas fa-plus"></i> @lang('Add Option')
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('Add Question')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Question')</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editQuestionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Same form fields as add modal -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('Update Question')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Question Modal -->
<div class="modal fade" id="deleteQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirm Delete')</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>@lang('Are you sure you want to delete this question?')</p>
                <p><strong id="deleteQuestionLabel"></strong></p>
                <p class="text-danger">@lang('This action cannot be undone.')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                <form id="deleteQuestionForm" method="POST" style="display: inline;">
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
    // Show/hide options container based on field type
    $('#type').change(function() {
        if ($(this).val() === 'select') {
            $('#optionsContainer').show();
        } else {
            $('#optionsContainer').hide();
        }
    });

    // Add option
    $('#addOption').click(function() {
        var index = $('#optionsList .option-item').length;
        var optionHtml = `
            <div class="option-item mb-2">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="options[${index}][value]" placeholder="@lang('Value')">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="options[${index}][label]" placeholder="@lang('Label')">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-option">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#optionsList').append(optionHtml);
    });

    // Remove option
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-item').remove();
    });

    // Add question form
    $('#addQuestionForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.registration.steps.add-question", $step->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                // Handle validation errors
                console.log(errors);
            }
        });
    });

    // Sortable questions
    $("#sortable-questions").sortable({
        handle: '.drag-handle',
        update: function(event, ui) {
            var questions = [];
            $('#sortable-questions tr').each(function(index) {
                var questionId = $(this).data('id');
                if (questionId) {
                    questions.push({
                        id: questionId,
                        order: index + 1
                    });
                }
            });
            
            $.ajax({
                url: '{{ route("admin.registration.steps.reorder-questions", $step->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    questions: questions
                },
                success: function(response) {
                    if (response.success) {
                        $('#sortable-questions tr').each(function(index) {
                            $(this).find('.question-order').text(index + 1);
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

    // Toggle question status
    $('.toggle-question-status').click(function() {
        var questionId = $(this).data('id');
        var button = $(this);
        
        $.ajax({
            url: '{{ route("admin.registration.steps.toggle-question-status", [$step->id, ":id"]) }}'.replace(':id', questionId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
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

    // Delete question
    $('.delete-question').click(function() {
        var questionId = $(this).data('id');
        var questionLabel = $(this).data('label');
        
        $('#deleteQuestionLabel').text(questionLabel);
        $('#deleteQuestionForm').attr('action', '{{ route("admin.registration.steps.delete-question", [$step->id, ":id"]) }}'.replace(':id', questionId));
        $('#deleteQuestionModal').modal('show');
    });
});
</script>
@endpush
