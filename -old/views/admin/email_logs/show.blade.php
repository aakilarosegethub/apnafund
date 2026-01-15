@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('Email Details')</h5>
                <div>
                    <a href="{{ route('admin.email-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> @lang('Back to List')
                    </a>
                    @if($emailLog->status === 'failed')
                        <form action="{{ route('admin.email-logs.resend', $emailLog->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" 
                                    onclick="return confirm('@lang('Are you sure you want to resend this email?')')">
                                <i class="fas fa-redo"></i> @lang('Resend Email')
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Email Information -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('Email Information')</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>@lang('To Email:')</strong></td>
                                    <td>{{ $emailLog->to_email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('To Name:')</strong></td>
                                    <td>{{ $emailLog->to_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('From Email:')</strong></td>
                                    <td>{{ $emailLog->from_email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('From Name:')</strong></td>
                                    <td>{{ $emailLog->from_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Subject:')</strong></td>
                                    <td>{{ $emailLog->subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Email Type:')</strong></td>
                                    <td>
                                        <span class="badge badge--{{ $emailLog->type_badge }}">
                                            {{ ucfirst($emailLog->email_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Status:')</strong></td>
                                    <td>
                                        <span class="badge badge--{{ $emailLog->status_badge }}">
                                            {{ ucfirst($emailLog->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Provider:')</strong></td>
                                    <td>{{ $emailLog->provider ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Template:')</strong></td>
                                    <td>{{ $emailLog->template_name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Timing Information -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('Timing Information')</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>@lang('Created At:')</strong></td>
                                    <td>{{ showDateTime($emailLog->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Sent At:')</strong></td>
                                    <td>{{ $emailLog->sent_at ? showDateTime($emailLog->sent_at) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('User ID:')</strong></td>
                                    <td>
                                        @if($emailLog->user)
                                            <a href="{{ route('admin.user.detail', $emailLog->user->id) }}" class="text-primary">
                                                {{ $emailLog->user->username }} ({{ $emailLog->user->email }})
                                            </a>
                                        @else
                                            {{ $emailLog->user_id ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('IP Address:')</strong></td>
                                    <td>{{ $emailLog->ip_address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('User Agent:')</strong></td>
                                    <td>
                                        @if($emailLog->user_agent)
                                            <small class="text-muted">{{ Str::limit($emailLog->user_agent, 50) }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Information (if failed) -->
            @if($emailLog->status === 'failed' && $emailLog->error_message)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">@lang('Error Information')</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger">
                                    <strong>@lang('Error Message:')</strong><br>
                                    {{ $emailLog->error_message }}
                                </div>
                                
                                @if($emailLog->provider_response)
                                    <div class="mt-3">
                                        <strong>@lang('Provider Response:')</strong>
                                        <pre class="bg-light p-3 mt-2">{{ json_encode($emailLog->provider_response, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Email Body -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('Email Body')</h6>
                        </div>
                        <div class="card-body">
                            <div class="email-body-preview" style="max-height: 600px; overflow-y: auto; border: 1px solid #dee2e6; padding: 20px; background: #f8f9fa;">
                                {!! $emailLog->body !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.email-logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> @lang('Back to List')
                            </a>
                        </div>
                        <div>
                            @if($emailLog->status === 'failed')
                                <form action="{{ route('admin.email-logs.resend', $emailLog->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('@lang('Are you sure you want to resend this email?')')">
                                        <i class="fas fa-redo"></i> @lang('Resend Email')
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.email-logs.destroy', $emailLog->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('@lang('Are you sure you want to delete this email log?')')">
                                    <i class="fas fa-trash"></i> @lang('Delete Log')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
