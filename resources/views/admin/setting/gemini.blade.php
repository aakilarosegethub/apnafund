@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">@lang('Gemini AI Settings')</h3>
            <p class="text-muted">@lang('Configure Google Gemini API for AI assistant. Get API key from') <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>.</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.gemini.update') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form--label">@lang('API Key')</label>
                        <input type="password" class="form--control" name="api_key" value="" placeholder="AIzaSy..." autocomplete="off">
                        @if($apiKeyMasked)
                            <small class="text-muted">@lang('Current key')</strong>: {{ $apiKeyMasked }} — @lang('Leave blank to keep existing')</small>
                        @else
                            <small class="text-muted">@lang('Leave blank to keep existing key')</small>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form--label">@lang('Model')</label>
                        <select class="form--control form-select" name="model">
                            <option value="gemini-1.5-flash" @selected(($data['model'] ?? '') == 'gemini-1.5-flash')>gemini-1.5-flash</option>
                            <option value="gemini-1.5-pro" @selected(($data['model'] ?? '') == 'gemini-1.5-pro')>gemini-1.5-pro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form--label">@lang('System Instructions')</label>
                        <textarea class="form--control" name="system_instructions" rows="6" placeholder="Tu Lahore Property Guide ka AI assistant ho...">{{ $data['system_instructions'] ?? '' }}</textarea>
                        <small class="text-muted">@lang('Main system prompt — AI ka role/behavior')</small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form--label">@lang('Conversation Instructions')</label>
                        <textarea class="form--control" name="conversation_instructions" rows="4" placeholder="Ek ek question poocho...">{{ $data['conversation_instructions'] ?? '' }}</textarea>
                        <small class="text-muted">@lang('Conversation flow rules')</small>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3 d-flex flex-wrap gap-2">
                    <button class="btn btn--base px-4" type="submit">
                        <i class="ti ti-device-floppy"></i> @lang('Save Settings')
                    </button>
                    <form action="{{ route('admin.gemini.test') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn--primary px-4" type="submit">
                            <i class="ti ti-plug"></i> @lang('Test Connection')
                        </button>
                    </form>
                    <form action="{{ route('admin.gemini.reset') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn--secondary px-4" type="submit" onclick="return confirm('@lang('Reset to default instructions?')')">
                            <i class="ti ti-refresh"></i> @lang('Reset Instructions')
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <div class="custom--card mt-4">
        <div class="card-header">
            <h5 class="title">@lang('API Reference')</h5>
        </div>
        <div class="card-body">
            <p class="mb-2">@lang('Use these endpoints with Bearer token (get token via') <code>POST /api/admin/login</code>):</p>
            <ul class="list-unstyled mb-0">
                <li><code>GET {{ url('/api/admin/settings/gemini') }}</code> — @lang('Fetch settings')</li>
                <li><code>PUT {{ url('/api/admin/settings/gemini') }}</code> — @lang('Save settings')</li>
                <li><code>POST {{ url('/api/admin/settings/gemini/test') }}</code> — @lang('Test connection')</li>
                <li><code>POST {{ url('/api/admin/settings/gemini/reset') }}</code> — @lang('Reset instructions')</li>
            </ul>
        </div>
    </div>
</div>
@endsection
