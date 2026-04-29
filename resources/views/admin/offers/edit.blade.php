@extends('admin.layouts.app')
@section('header_style_content')
<style>
    .asset-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid #fff;
        background: #eee;
    }
    .asset-item img, .asset-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .remove-asset {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ef4444;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        font-size: 14px;
        transition: all 0.2s;
        z-index: 5;
    }
    .remove-asset:hover {
        background: #b91c1c;
        transform: scale(1.15);
    }
</style>
@endsection
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Edit Offer</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.offers.index') }}">Offers</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Offer</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $offer->id }}">
                                    <input type="hidden" id="form-method" value="edit">
                                    <div class="row row-sm">

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Offer Title</label>
                                                <input type="text" name="title" class="form-control" value="{{ $offer->title }}" placeholder="e.g. Summer Special 50% Off" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Display Position</label>
                                                <select name="position" class="form-control" required>
                                                    <option value="top_header" {{ $offer->position == 'top_header' ? 'selected' : '' }}>Top Header Slider</option>
                                                    <option value="footer" {{ $offer->position == 'footer' ? 'selected' : '' }}>Footer Banner</option>
                                                    <option value="other" {{ $offer->position == 'other' ? 'selected' : '' }}>Other Page</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Priority Order</label>
                                                <input type="number" name="priority" class="form-control" value="{{ $offer->priority }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Redirect Link (Optional)</label>
                                                <input type="url" name="link" class="form-control" value="{{ $offer->link }}" placeholder="https://beautyden.com/promo">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $offer->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $offer->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Media Type</label>
                                                <div class="mt-1">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="media_type" id="type_image" value="image" {{ $offer->media_type == 'image' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="type_image">Images</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="media_type" id="type_video" value="video" {{ $offer->media_type == 'video' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="type_video">Video</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <label class="mb-1">Current Assets ({{ count($offer->media ?? []) }} files)</label>
                                            <div class="row g-2 mb-2">
                                                @if($offer->media)
                                                    @foreach($offer->media as $item)
                                                        <div class="col-md-2 col-4" id="media-{{ md5($item) }}">
                                                            <div class="asset-item">
                                                                @if($offer->media_type == 'image')
                                                                    <img src="{{ asset('uploads/offers/images/'.$item) }}" alt="Offer Media">
                                                                @else
                                                                    <video src="{{ asset('uploads/offers/videos/'.$item) }}" muted loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                                                                @endif
                                                                <button type="button" class="remove-asset" onclick="removeMedia('{{ $item }}')">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2" id="image-upload-wrapper" style="{{ $offer->media_type == 'image' ? '' : 'display:none' }}">
                                            <div class="form-group">
                                                <label>Add More Images</label>
                                                <input type="file" class="filepond-multiple" multiple data-allow-reorder="true">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2" id="video-upload-wrapper" style="{{ $offer->media_type == 'video' ? '' : 'display:none' }}">
                                            <div class="form-group">
                                                <label>Replace Video</label>
                                                <input type="file" class="filepond-single">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    var form_url = 'offers/store';
    var redirect_url = 'offers';
    var media_field_name = '{{ $offer->media_type == 'image' ? 'media[]' : 'media' }}';
    var is_one_image_and_multiple_image_status = '{{ $offer->media_type == 'image' ? 'is_multiple_image' : 'is_one_image' }}';

    $(document).ready(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        
        let imagePond = FilePond.create(document.querySelector('.filepond-multiple'), {
            allowMultiple: true,
            instantUpload: false,
            allowProcess: false,
        });

        let videoPond = FilePond.create(document.querySelector('.filepond-single'), {
            allowMultiple: false,
            instantUpload: false,
            allowProcess: false,
        });

        pond = '{{ $offer->media_type == 'image' }}' == '1' ? imagePond : videoPond;

        $('input[name="media_type"]').on('change', function() {
            if ($(this).val() === 'video') {
                $('#image-upload-wrapper').hide();
                $('#video-upload-wrapper').show();
                pond = videoPond;
                media_field_name = 'media';
                is_one_image_and_multiple_image_status = 'is_one_image';
            } else {
                $('#image-upload-wrapper').show();
                $('#video-upload-wrapper').hide();
                pond = imagePond;
                media_field_name = 'media[]';
                is_one_image_and_multiple_image_status = 'is_multiple_image';
            }
        });
    });

    function removeMedia(mediaName) {
        Swal.fire({
            title: 'Remove this media?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary ms-1' },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                loaderView();
                $.ajax({
                    url: "{{ route('admin.offers.removeMedia') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: "{{ $offer->id }}",
                        media: mediaName
                    },
                    success: function(response) {
                        loaderHide();
                        if(response.success) {
                            notificationToast(response.message, 'success');
                            location.reload();
                        } else {
                            notificationToast(response.message, 'error');
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
