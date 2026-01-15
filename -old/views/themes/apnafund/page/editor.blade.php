@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
<div class="editor-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="editor-wrapper">
                    <!--
                      Adding the `tinymce-editor` element with various options set as attributes.
                    -->
                    <tinymce-editor
                      api-key="no-api-key"
                      height="500"
                      menubar="false"
                      plugins="advlist autolink lists link image charmap preview anchor
                        searchreplace visualblocks code fullscreen
                        insertdatetime media table code help wordcount"
                      toolbar="undo redo | blocks | bold italic backcolor |
                        alignleft aligncenter alignright alignjustify |
                        bullist numlist outdent indent | removeformat | help"
                      content_style="body
                      {
                        font-family:Helvetica,Arial,sans-serif;
                        font-size:14px
                      }"
                      >

                      <!-- Adding some initial editor content -->
                      &lt;p&gt;Welcome to the TinyMCE Web Component example!&lt;/p&gt;

                    </tinymce-editor>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.editor-page {
    min-height: 60vh;
}

.editor-wrapper {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>
@endsection

@push('page-script-lib')
<!--
  Sourcing the `tinymce-webcomponent` from jsDelivr,
  which sources TinyMCE from the Tiny Cloud.
-->
<script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-webcomponent/dist/tinymce-webcomponent.min.js"></script>
@endpush

