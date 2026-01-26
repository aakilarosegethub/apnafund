@extends('admin.layouts.master')

@section('master')
    <!-- Template Info Header -->
    <div class="col-12 mb-4">
        <div class="custom--card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="title text-white mb-1">
                            <i class="las la-bell"></i> {{ $template->name }}
                        </h3>
                        <p class="text-white-50 mb-0 small">
                            <i class="las la-info-circle"></i> @lang('Template ID'): #{{ $template->id }} | 
                            @lang('Action'): <code class="bg-dark text-warning px-2 py-1 rounded">{{ $template->act }}</code>
                        </p>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#shortcodesSection">
                            <i class="las la-code"></i> @lang('View Shortcodes')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shortcodes Section (Collapsible) -->
    <div class="col-12 collapse mb-4" id="shortcodesSection">
        <div class="custom--card">
            <div class="card-header bg-info">
                <h5 class="title text-white mb-0">
                    <i class="las la-tags"></i> @lang('Available Shortcodes')
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="30%">
                                    <i class="las la-code"></i> @lang('Short Code')
                                </th>
                                <th width="70%">
                                    <i class="las la-file-alt"></i> @lang('Description')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_object($template->shortcodes) || is_array($template->shortcodes))
                                @foreach($template->shortcodes as $shortcode => $key)
                                    <tr>
                                        <td>
                                            <code class="text-primary cursor-pointer shortcode-item" 
                                                  data-shortcode="{{{{ $shortcode }}}}" 
                                                  title="@lang('Click to copy')">
                                                @php echo "{{". $shortcode ."}}" @endphp
                                            </code>
                                            <i class="las la-copy text-muted ms-1"></i>
                                        </td>
                                        <td class="text-muted">{{ __($key) }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @if(isset($setting) && (is_object($setting->universal_shortcodes) || is_array($setting->universal_shortcodes)))
                                <tr class="table-secondary">
                                    <td colspan="2" class="fw-bold">
                                        <i class="las la-globe"></i> @lang('Universal Shortcodes')
                                    </td>
                                </tr>
                                @foreach($setting->universal_shortcodes as $shortCode => $codeDetails)
                                    <tr>
                                        <td>
                                            <code class="text-success cursor-pointer shortcode-item" 
                                                  data-shortcode="@{{{{ $shortCode }}}}" 
                                                  title="@lang('Click to copy')">
                                                @{{@php echo $shortCode @endphp}}
                                            </code>
                                            <i class="las la-copy text-muted ms-1"></i>
                                        </td>
                                        <td class="text-muted">{{ __($codeDetails) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Edit Form -->
    <div class="col-12">
        <form class="row g-4" action="{{ route('admin.notification.template.update', $template->id) }}" method="POST" id="templateForm">
            @csrf
            
            <!-- Email Template Section -->
            <div class="col-lg-6">
                <div class="custom--card h-100 shadow-sm">
                    <div class="card-header bg-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="title text-white mb-0">
                                <i class="las la-envelope"></i> @lang('Email Template')
                            </h3>
                            <div class="form-check form--switch">
                                <input class="form-check-input" id="emailStatus" type="checkbox" 
                                       name="email_status" @if($template->email_status) checked @endif>
                                <label for="emailStatus" class="text-white ms-2 small">
                                    @lang('Active')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="las la-info-circle"></i>
                            <small>@lang('Email notifications will be sent to users when this event occurs.')</small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        
                        <div class="form-group">
                            <label class="form--label required">
                                <i class="las la-heading"></i> @lang('Email Subject')
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-tag"></i></span>
                                <input type="text" class="form--control" name="subject" 
                                       value="{{ $template->subj }}" 
                                       placeholder="@lang('Enter email subject')" 
                                       required
                                       maxlength="255">
                            </div>
                            <small class="text-muted">
                                <i class="las la-lightbulb"></i> @lang('Use shortcodes to personalize the subject')
                            </small>
                        </div>
                        
                        <div class="form-group mb-0 editor-wrapper">
                            <label class="form--label required">
                                <i class="las la-file-alt"></i> @lang('Email Body')
                            </label>
                            <textarea class="form--control trumEdit" 
                                      name="email_body" 
                                      rows="10">{{ $template->email_body }}</textarea>
                            <small class="text-muted mt-2 d-block">
                                <i class="las la-magic"></i> @lang('Rich text editor enabled - Format your email with HTML')
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SMS Template Section -->
            <div class="col-lg-6">
                <div class="custom--card h-100 shadow-sm">
                    <div class="card-header bg-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="title text-white mb-0">
                                <i class="las la-sms"></i> @lang('SMS Template')
                            </h3>
                            <div class="form-check form--switch">
                                <input class="form-check-input" id="smsStatus" type="checkbox" 
                                       name="sms_status" @if($template->sms_status) checked @endif>
                                <label for="smsStatus" class="text-white ms-2 small">
                                    @lang('Active')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="las la-exclamation-triangle"></i>
                            <small>@lang('SMS notifications are sent via configured SMS gateway. Keep messages concise.')</small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form--label required">
                                <i class="las la-comment-dots"></i> @lang('SMS Body')
                            </label>
                            <textarea class="form--control" 
                                      name="sms_body" 
                                      id="smsBody"
                                      rows="8" 
                                      placeholder="@lang('Enter SMS message content...')" 
                                      required
                                      maxlength="500">{{ $template->sms_body }}</textarea>
                            
                            <!-- Character Counter -->
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    <i class="las la-keyboard"></i> @lang('Characters'): 
                                    <span id="charCount" class="fw-bold text-primary">{{ strlen($template->sms_body) }}</span>
                                    <span class="text-muted">/ 500</span>
                                </small>
                                <small class="text-muted">
                                    <i class="las la-file"></i> @lang('Messages'): 
                                    <span id="smsCount" class="fw-bold text-success">{{ ceil(strlen($template->sms_body) / 160) }}</span>
                                    <span class="text-muted">(160 chars each)</span>
                                </small>
                            </div>
                            
                            <!-- SMS Preview Box -->
                            <div class="mt-3 p-3 bg-light border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="las la-mobile-alt text-success fs-4 me-2"></i>
                                    <strong class="text-muted small">@lang('SMS Preview')</strong>
                                </div>
                                <div class="sms-preview bg-white p-3 rounded border" style="min-height: 80px; font-family: monospace; font-size: 0.9rem; white-space: pre-wrap;">
                                    {{ $template->sms_body }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Insert Shortcodes -->
                        <div class="form-group mb-0">
                            <label class="form--label">
                                <i class="las la-plus-circle"></i> @lang('Quick Insert')
                            </label>
                            <div class="btn-group-sm" role="group">
                                @if(is_object($template->shortcodes) || is_array($template->shortcodes))
                                    @foreach(array_slice((array)$template->shortcodes, 0, 4, true) as $shortcode => $key)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary insert-shortcode mb-1 me-1" 
                                                data-shortcode="{{{{ $shortcode }}}}">
                                            <i class="las la-plus"></i> {{ $shortcode }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="col-12">
                <div class="custom--card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn--base px-5 py-2" type="submit">
                                <i class="las la-save"></i> @lang('Save Changes')
                            </button>
                            <button class="btn btn-secondary px-5 py-2" type="button" onclick="window.history.back()">
                                <i class="las la-times"></i> @lang('Cancel')
                            </button>
                            <button class="btn btn-info px-4 py-2" type="button" onclick="resetForm()">
                                <i class="las la-redo"></i> @lang('Reset')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Export Template Settings -->
    <div class="col-12 mt-3">
        <div class="custom--card">
            <div class="card-header bg-dark">
                <h5 class="title text-white mb-0">
                    <i class="las la-download"></i> @lang('Export Template Settings')
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <i class="las la-info-circle text-info"></i> 
                            @lang('Export this template configuration for backup or migration purposes.')
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-sm btn-outline-dark" onclick="exportTemplate()">
                            <i class="las la-file-export"></i> @lang('Export as JSON')
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="copySettings()">
                            <i class="las la-copy"></i> @lang('Copy Settings')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .cursor-pointer:hover {
        transform: scale(1.05);
        background: #f0f0f0;
        padding: 2px 4px;
        border-radius: 4px;
    }
    .shortcode-item:hover {
        background: #e3f2fd !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .sms-preview {
        background: linear-gradient(135deg, #667eea11 0%, #764ba211 100%);
        word-wrap: break-word;
    }
    .custom--card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .custom--card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .card-header.bg-gradient {
        border: none;
    }
    .form--control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn--base {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    .btn--base:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .insert-shortcode {
        transition: all 0.2s ease;
    }
    .insert-shortcode:hover {
        transform: scale(1.05);
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('page-script-lib')
    <script src="{{asset('assets/admin/js/page/ckEditor.js')}}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict";

            // Initialize CKEditor
            if ($(".trumEdit")[0]) {
                $('.editor-wrapper').find('.ck-editor').remove();
                window.editors = {};
                document.querySelectorAll('.trumEdit').forEach((node, index) => {
                    ClassicEditor
                        .create(node, {
                            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
                        })
                        .then(newEditor => {
                            window.editors[index] = newEditor;
                        })
                        .catch(error => {
                            console.error('CKEditor initialization error:', error);
                        });
                });
            }

            // Character counter for SMS
            const smsBody = document.getElementById('smsBody');
            const charCount = document.getElementById('charCount');
            const smsCount = document.getElementById('smsCount');
            const smsPreview = document.querySelector('.sms-preview');

            function updateSmsStats() {
                const length = smsBody.value.length;
                charCount.textContent = length;
                smsCount.textContent = Math.ceil(length / 160) || 1;
                smsPreview.textContent = smsBody.value || 'Your SMS preview will appear here...';

                // Color coding
                if (length > 480) {
                    charCount.className = 'fw-bold text-danger';
                } else if (length > 320) {
                    charCount.className = 'fw-bold text-warning';
                } else {
                    charCount.className = 'fw-bold text-primary';
                }
            }

            if (smsBody) {
                smsBody.addEventListener('input', updateSmsStats);
                updateSmsStats(); // Initial update
            }

            // Copy shortcode on click
            document.querySelectorAll('.shortcode-item').forEach(item => {
                item.addEventListener('click', function() {
                    const shortcode = this.getAttribute('data-shortcode');
                    
                    // Copy to clipboard
                    navigator.clipboard.writeText(shortcode).then(() => {
                        // Show success notification
                        showNotification('Shortcode copied: ' + shortcode, 'success');
                        
                        // Visual feedback
                        this.style.background = '#4caf50';
                        this.style.color = 'white';
                        setTimeout(() => {
                            this.style.background = '';
                            this.style.color = '';
                        }, 300);
                    }).catch(err => {
                        console.error('Copy failed:', err);
                        showNotification('Failed to copy shortcode', 'error');
                    });
                });
            });

            // Insert shortcode into SMS body
            document.querySelectorAll('.insert-shortcode').forEach(btn => {
                btn.addEventListener('click', function() {
                    const shortcode = this.getAttribute('data-shortcode');
                    const textarea = document.getElementById('smsBody');
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    
                    // Insert at cursor position
                    textarea.value = text.substring(0, start) + shortcode + text.substring(end);
                    textarea.focus();
                    textarea.selectionStart = textarea.selectionEnd = start + shortcode.length;
                    
                    // Update stats
                    updateSmsStats();
                    showNotification('Shortcode inserted', 'success');
                });
            });

            // Reset form function
            window.resetForm = function() {
                if (confirm('Are you sure you want to reset all changes?')) {
                    document.getElementById('templateForm').reset();
                    updateSmsStats();
                    showNotification('Form reset successfully', 'info');
                }
            };

            // Export template as JSON
            window.exportTemplate = function() {
                const template = {
                    id: {{ $template->id }},
                    name: "{{ $template->name }}",
                    action: "{{ $template->act }}",
                    email: {
                        status: document.getElementById('emailStatus').checked,
                        subject: document.querySelector('input[name="subject"]').value,
                        body: window.editors[0] ? window.editors[0].getData() : ''
                    },
                    sms: {
                        status: document.getElementById('smsStatus').checked,
                        body: document.getElementById('smsBody').value
                    },
                    shortcodes: @json($template->shortcodes),
                    exported_at: new Date().toISOString()
                };

                const dataStr = JSON.stringify(template, null, 2);
                const dataBlob = new Blob([dataStr], {type: 'application/json'});
                const url = URL.createObjectURL(dataBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'template_{{ $template->id }}_' + Date.now() + '.json';
                link.click();
                URL.revokeObjectURL(url);
                
                showNotification('Template exported successfully', 'success');
            };

            // Copy settings to clipboard
            window.copySettings = function() {
                const settings = {
                    email_subject: document.querySelector('input[name="subject"]').value,
                    email_status: document.getElementById('emailStatus').checked ? 'Active' : 'Inactive',
                    sms_body: document.getElementById('smsBody').value,
                    sms_status: document.getElementById('smsStatus').checked ? 'Active' : 'Inactive',
                    sms_length: document.getElementById('smsBody').value.length,
                    sms_parts: Math.ceil(document.getElementById('smsBody').value.length / 160)
                };

                const text = `Template Settings (ID: {{ $template->id }})\n` +
                            `Name: {{ $template->name }}\n` +
                            `Action: {{ $template->act }}\n\n` +
                            `EMAIL:\n` +
                            `- Status: ${settings.email_status}\n` +
                            `- Subject: ${settings.email_subject}\n\n` +
                            `SMS:\n` +
                            `- Status: ${settings.sms_status}\n` +
                            `- Length: ${settings.sms_length} characters\n` +
                            `- Parts: ${settings.sms_parts} message(s)\n` +
                            `- Body:\n${settings.sms_body}`;

                navigator.clipboard.writeText(text).then(() => {
                    showNotification('Settings copied to clipboard', 'success');
                }).catch(err => {
                    console.error('Copy failed:', err);
                    showNotification('Failed to copy settings', 'error');
                });
            };

            // Show notification helper
            function showNotification(message, type) {
                const colors = {
                    success: '#4caf50',
                    error: '#f44336',
                    info: '#2196f3',
                    warning: '#ff9800'
                };

                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${colors[type] || colors.info};
                    color: white;
                    padding: 15px 25px;
                    border-radius: 5px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    z-index: 9999;
                    animation: slideIn 0.3s ease;
                `;
                notification.innerHTML = `<i class="las la-check-circle"></i> ${message}`;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);

            // Form validation
            document.getElementById('templateForm').addEventListener('submit', function(e) {
                const emailBody = window.editors[0] ? window.editors[0].getData() : '';
                const smsBody = document.getElementById('smsBody').value;

                if (!emailBody.trim()) {
                    e.preventDefault();
                    showNotification('Email body cannot be empty', 'error');
                    return false;
                }

                if (!smsBody.trim()) {
                    e.preventDefault();
                    showNotification('SMS body cannot be empty', 'error');
                    return false;
                }

                if (smsBody.length > 500) {
                    e.preventDefault();
                    showNotification('SMS body exceeds 500 characters limit', 'error');
                    return false;
                }

                showNotification('Saving template...', 'info');
            });

        })(jQuery);
    </script>
@endpush

