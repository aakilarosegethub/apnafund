@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Create Registration Step')</h5>
                <div class="card-header-actions">
                    <a href="{{ route('admin.registration.steps.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> @lang('Back to Steps')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.registration.steps.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">@lang('Step Title') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="subtitle">@lang('Step Subtitle')</label>
                                <textarea class="form-control" id="subtitle" name="subtitle" rows="2">{{ old('subtitle') }}</textarea>
                                @error('subtitle')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="step_order">@lang('Step Order') <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="step_order" name="step_order" 
                                       value="{{ old('step_order', 1) }}" min="1" required>
                                @error('step_order')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_required" name="is_required" 
                                           value="1" {{ old('is_required', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">
                                        @lang('Required Step')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('Create Step')
                        </button>
                        <a href="{{ route('admin.registration.steps.index') }}" class="btn btn-secondary">
                            @lang('Cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
