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
                                    @php $shortcodeTag = '{{' . $shortcode . '}}'; @endphp
                                    <tr>
                                        <td>
                                            <code class="text-primary cursor-pointer shortcode-item" 
                                                  data-shortcode="{{ $shortcodeTag }}" 
                                                  title="@lang('Click to copy')">
                                                {{ $shortcodeTag }}
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
                                    @php $universalShortcodeTag = '{{' . $shortCode . '}}'; @endphp
                                    <tr>
                                        <td>
                                            <code class="text-success cursor-pointer shortcode-item" 
                                                  data-shortcode="{{ $universalShortcodeTag }}" 
                                                  title="@lang('Click to copy')">
                                                {{ $universalShortcodeTag }}
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
            
            <!-- Email Template Section (SMS is managed separately; not edited here) -->
            <div class="col-12">
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
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="las la-lightbulb"></i> @lang('Use shortcodes to personalize the subject')
                            </small>
                        </div>
                        
                        <div class="form-group mb-3 editor-wrapper">
                            <label class="form--label required">
                                <i class="las la-file-alt"></i> @lang('Email Body')
                            </label>
                            <textarea class="form--control trumEdit" 
                                      name="email_body" 
                                      rows="14">{{ $template->email_body }}</textarea>
                            <small class="text-muted mt-2 d-block">
                                <i class="las la-magic"></i> @lang('Rich text editor — no character limit')
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form--label">
                                <i class="las la-plus-circle"></i> @lang('Quick insert shortcodes into email')
                            </label>
                            <div class="btn-group-sm" role="group">
                                @if(is_object($template->shortcodes) || is_array($template->shortcodes))
                                    @foreach((array) $template->shortcodes as $shortcode => $key)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary insert-shortcode-email mb-1 me-1"
                                                data-shortcode="{{ $shortcode }}">
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

            document.querySelectorAll('.shortcode-item').forEach(item => {
                item.addEventListener('click', function() {
                    const shortcode = this.getAttribute('data-shortcode');
                    navigator.clipboard.writeText(shortcode).then(() => {
                        showNotification('Shortcode copied: ' + shortcode, 'success');
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

            document.querySelectorAll('.insert-shortcode-email').forEach(btn => {
                btn.addEventListener('click', function() {
                    const code = this.getAttribute('data-shortcode');
                    const tag = '{{' + code + '}}';
                    const ed = window.editors[0];
                    if (!ed) return;
                    ed.model.change(writer => {
                        const pos = ed.model.document.selection.getFirstPosition();
                        writer.insertText(tag, pos);
                    });
                    showNotification('Shortcode inserted', 'success');
                });
            });

            window.resetForm = function() {
                if (confirm('Are you sure you want to reset all changes?')) {
                    document.getElementById('templateForm').reset();
                    showNotification('Form reset — reload page to restore editor content', 'info');
                }
            };

            const templateSmsSnapshot = {
                status: @json((bool) $template->sms_status),
                body: @json($template->sms_body)
            };

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
                    sms: templateSmsSnapshot,
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

            window.copySettings = function() {
                const emailBody = window.editors[0] ? window.editors[0].getData() : '';
                const text = `Template (ID: {{ $template->id }})\n` +
                    `Name: {{ $template->name }}\n` +
                    `Action: {{ $template->act }}\n\n` +
                    `EMAIL:\n` +
                    `- Status: ${document.getElementById('emailStatus').checked ? 'Active' : 'Inactive'}\n` +
                    `- Subject: ${document.querySelector('input[name="subject"]').value}\n` +
                    `- Body:\n${emailBody}`;
                navigator.clipboard.writeText(text).then(() => {
                    showNotification('Settings copied to clipboard', 'success');
                }).catch(err => {
                    console.error('Copy failed:', err);
                    showNotification('Failed to copy settings', 'error');
                });
            };

            function showNotification(message, type) {
                const colors = { success: '#4caf50', error: '#f44336', info: '#2196f3', warning: '#ff9800' };
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed; top: 20px; right: 20px;
                    background: ${colors[type] || colors.info}; color: white;
                    padding: 15px 25px; border-radius: 5px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999;
                    animation: slideIn 0.3s ease;
                `;
                notification.innerHTML = `<i class="las la-check-circle"></i> ${message}`;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            const anim = document.createElement('style');
            anim.textContent = `
                @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
                @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
            `;
            document.head.appendChild(anim);

            document.getElementById('templateForm').addEventListener('submit', function(e) {
                const emailBody = window.editors[0] ? window.editors[0].getData() : '';
                if (!emailBody.trim()) {
                    e.preventDefault();
                    showNotification('Email body cannot be empty', 'error');
                    return false;
                }
                const ta = document.querySelector('textarea[name="email_body"]');
                if (ta && window.editors[0]) {
                    ta.value = emailBody;
                }
                showNotification('Saving template...', 'info');
            });

        })(jQuery);
    </script>
@endpush

