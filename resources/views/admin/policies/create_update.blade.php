@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Policies</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Policies</a></li>
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
                                    <form method="POST" data-parsley-validate id="addEditForm" role="form">
                                        @csrf

                                        <div class="row row-sm">

                                            <!-- Payment, Pricing & Refund Policy -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Payment, Pricing & Refund Policy</label>
                                                    <textarea class="form-control editor" name="payment_policy" rows="5"
                                                        placeholder="Enter Payment, Pricing & Refund Policy" required>{{ old('payment_policy', $policies['payment_policy'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Privacy Policy -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Privacy Policy</label>
                                                    <textarea class="form-control editor" name="privacy_policy" rows="5" placeholder="Enter Privacy Policy" required>{{ old('privacy_policy', $policies['privacy_policy'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Terms & Conditions -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Terms & Conditions</label>
                                                    <textarea class="form-control editor" name="terms_conditions" rows="5" placeholder="Enter Terms & Conditions" required>{{ old('terms_conditions', $policies['terms_conditions'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.faqs.index') }}"
                                                            class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
        var form_url = 'policies/store';
        var redirect_url = 'policies';


        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.editor').forEach((el) => {
                ClassicEditor.create(el, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough',
                        'subscript', 'superscript', '|',
                        'link', 'bulletedList', 'numberedList',
                        'outdent', 'indent', 'alignment', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
                        'highlight', '|',
                        'blockQuote', 'code', 'codeBlock', '|',
                        'insertTable', 'mediaEmbed', 'imageUpload', 'imageInsert', '|',
                        'horizontalLine', 'pageBreak', 'specialCharacters', 'removeFormat', '|',
                        'undo', 'redo'
                    ],
                    table: {
                        contentToolbar: [
                            'tableColumn', 'tableRow', 'mergeTableCells',
                            'insertTableRowAbove', 'insertTableRowBelow',
                            'insertTableColumnLeft', 'insertTableColumnRight'
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageStyle:full', 'imageStyle:side',
                            '|', 'imageTextAlternative', 'linkImage'
                        ]
                    },
                    mediaEmbed: {
                        previewsInData: true
                    }
                }).catch(error => {
                    console.error(error);
                });
            });
        });
    </script>
@endsection
