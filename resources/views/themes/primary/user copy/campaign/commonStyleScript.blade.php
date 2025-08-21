
@push('page-style')
    <style>
        .date-picker {
            caret-color: transparent;
            cursor: pointer;
        }
        .dropzone {
            border-color: hsl(var(--black) / 0.1);
            min-height: auto;
            transition: .3s
        }
        .dropzone:hover,
        .dropzone:focus,
        .dropzone:focus-within {
            border-color: hsl(var(--base));
        }
        .dropzone .dz-message {
            margin: 30px 0;
        }
        .dropzone .dz-message button::before {
            content: "\eb47";
            font-family: "tabler-icons";
            font-weight: 900;
            display: block;
            font-size: 3rem;
            color: hsl(var(--base));
        }
        .dropzone .dz-preview .dz-image {
            border-radius: 5px;
        }
        .dropzone .dz-preview .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .dropzone .dz-preview .dz-remove {
            position: absolute;
            top: -3px;
            right: -3px;
            font-size: 0;
            width: 17px;
            height: 17px;
            background: hsl(var(--danger));
            border-radius: 50%;
            z-index: 20;
        }
        .dropzone .dz-preview .dz-remove::after {
            content: '\eb55';
            font-family: "tabler-icons";
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-45%, -50%);
            font-size: 12px;
            color: hsl(var(--white));
            line-height: 17px;
        }
    </style>
@endpush

@push('page-script')
    <script type="text/javascript">
        (function($) {
            "use strict"

            new Dropzone('.dropzone', {
                thumbnailWidth: 200,
                acceptedFiles: '.jpg, .jpeg, .png',
                addRemoveLinks: true,
                success: function(file, response) {
                    file.unique_name = response.image

                    showToasts('success', response.message)
                },
                error: function(file, response) {
                    showToasts('error', response.error.file[0])
                },
                removedfile: function(file) {
                    let url = "{{ route('user.campaign.gallery.remove') }}"
                    let data = {
                        file: file.unique_name,
                        _token: "{{ csrf_token() }}",
                    }

                    $.post(url, data, function(response) {
                        if (response.status === 'success') {
                            showToasts('success', response.message)
                        } else {
                            console.error(response)
                        }
                    })

                    let fileRef = file.previewElement

                    return fileRef != null ? fileRef.parentNode.removeChild(fileRef) : void 0
                }
            });
        })(jQuery)
    </script>
@endpush