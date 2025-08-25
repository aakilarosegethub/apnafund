@extends('admin.layouts.master')
@section('master')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('Custom Code Management')</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.customcode.update') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Header Code Section -->
                                    <div class="custom--card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="ti ti-code text--primary"></i>
                                                @lang('Header Custom Code')
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>@lang('Custom Code for Header')</label>
                                                <textarea name="header_code" class="form-control" rows="8" placeholder="Enter your custom HTML, CSS, or JavaScript code for header...">{{ @$headerCode->data_info->code ?? '' }}</textarea>
                                                <small class="text-muted">@lang('This code will be added in the &lt;head&gt; section of your website.')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Code Section -->
                                    <div class="custom--card mt-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="ti ti-code text--primary"></i>
                                                @lang('Footer Custom Code')
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>@lang('Custom Code for Footer')</label>
                                                <textarea name="footer_code" class="form-control" rows="8" placeholder="Enter your custom HTML, CSS, or JavaScript code for footer...">{{ @$footerCode->data_info->code ?? '' }}</textarea>
                                                <small class="text-muted">@lang('This code will be added before the closing &lt;/body&gt; tag of your website.')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn--primary w-100">
                                            <i class="ti ti-device-floppy me-2"></i>@lang('Save Changes')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
<style>
    .custom--card {
        border: 1px solid #e5e9f2;
        border-radius: 8px;
        background: #fff;
    }
    
    .custom--card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e5e9f2;
        padding: 1rem;
        border-radius: 8px 8px 0 0;
    }
    
    .custom--card .card-body {
        padding: 1.5rem;
    }
    
    textarea.form-control {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        background: #f8f9fa;
        border: 1px solid #e5e9f2;
    }
    
    textarea.form-control:focus {
        background: #fff;
        border-color: #5e72e4;
        box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
    }
</style>
@endpush 