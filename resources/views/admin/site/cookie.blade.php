
@extends('admin.layouts.master')

@section('master')
    @php
        $ci = is_array($cookie->data_info ?? null) ? $cookie->data_info : [];
    @endphp
    <div class="col-12">
        {{-- novalidate: CKEditor hides <textarea name="details">; native "required" on it causes "not focusable" and blocks submit --}}
        <form id="cookiePolicyForm" action="{{ route('admin.cookie.setting') }}" method="POST" novalidate>
            @csrf
            <div class="row g-4">
                <div class="col-12">
                    <div class="custom--card">
                        <div class="card-body">
                            <div class="row g-lg-4 g-3">
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Short Details')</label>
                                        </div>
                                        <div class="col-xxl-9">
                                            <textarea class="form--control" name="short_details" id="cookiePolicyShortDetails">{{ old('short_details', $ci['short_details'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Details')</label>
                                        </div>
                                        <div class="col-xxl-9 editor-wrapper">
                                            {{-- No HTML5 "required" here: CKEditor hides the textarea, so the browser cannot focus it and throws "not focusable". Server validates; JS checks below on submit. --}}
                                            <textarea class="form--control trumEdit" name="details" id="cookiePolicyDetails" autocomplete="off">{{ old('details', $ci['details'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Status')</label>
                                        </div>
                                        <div class="col-xxl-9">
                                            <div class="form-check form--switch">
                                                <input class="form-check-input" type="checkbox" name="status" value="1" @checked(old('status', !empty($ci['status'])))>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn--base px-4" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

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
                    node.removeAttribute('required');
                    ClassicEditor
                        .create(node)
                        .then(newEditor => {
                            window.editors[index] = newEditor;
                            node.removeAttribute('required');
                        })
                        .catch(function () {});
                });
            }

            // CKEditor keeps content outside the <textarea>; copy into textarea before submit (after removing native required on hidden field).
            document.getElementById('cookiePolicyForm')?.addEventListener('submit', function (e) {
                var form = e.target;
                var sd = form.querySelector('[name="short_details"]');
                if (sd && !String(sd.value || '').trim()) {
                    e.preventDefault();
                    sd.focus();
                    alert(@json(__('Please enter short details.')));
                    return;
                }
                document.querySelectorAll('.trumEdit').forEach(function (node, index) {
                    if (window.editors && window.editors[index]) {
                        node.value = window.editors[index].getData();
                    }
                });
                var det = form.querySelector('[name="details"]');
                var html = String(det ? det.value : '').replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').trim();
                if (!html) {
                    e.preventDefault();
                    alert(@json(__('Please enter cookie policy details.')));
                }
            });
        })(jQuery);
    </script>
@endpush
