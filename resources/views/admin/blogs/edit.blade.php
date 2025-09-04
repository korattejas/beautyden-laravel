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
                        <h2 class="content-header-title float-start mb-0">Edit Blog</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.blogs.index') }}">Blogs</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="#">Edit Blog</a>
                                </li>
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
                                <form method="POST" id="addEditForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $blog->id }}">

                                    <div class="row row-sm">

                                        <!-- Category -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category_id" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" 
                                                            {{ $blog->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title"
                                                    value="{{ $blog->title }}" placeholder="Blog Title" required>
                                            </div>
                                        </div>

                                         <!-- Slug -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Slug</label>
                                                <input type="text" class="form-control" name="slug"
                                                    value="{{ $blog->slug }}" placeholder="Slug Title" required>
                                            </div>
                                        </div>

                                        <!-- Excerpt -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Excerpt</label>
                                                <textarea class="form-control" name="excerpt" rows="3"
                                                    placeholder="Short excerpt">{{ $blog->excerpt }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Content</label>
                                                <textarea class="form-control editor" name="content" rows="6"
                                                    placeholder="Blog content">{{ $blog->content }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Read Time -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Read Time</label>
                                                <input type="text" class="form-control" name="read_time"
                                                    value="{{ $blog->read_time }}" placeholder="e.g. 5 min">
                                            </div>
                                        </div>

                                        <!-- Author -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Author</label>
                                                <input type="text" class="form-control" name="author"
                                                    value="{{ $blog->author }}" placeholder="Author Name">
                                            </div>
                                        </div>

                                        <!-- Publish Date -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Publish Date</label>
                                                <input type="date" class="form-control" name="publish_date"
                                                    value="{{ $blog->publish_date }}">
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Tags (comma separated)</label>
                                                <input type="text" class="form-control" name="tags"
                                                    value=" {{
                                                        is_array($blog->tags)
                                                            ? implode(',', $blog->tags)
                                                            : ($blog->tags
                                                                ? implode(',', json_decode($blog->tags, true))  {{-- string hoy to decode --}}
                                                                : '')
                                                    }}"
                                                    placeholder="e.g. PHP,Laravel,Backend">
                                            </div>
                                        </div>

                                        <!-- Icon -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon</label>
                                                <input type="file" class="filepond" name="icon">
                                                @if($blog->icon)
                                                    <img src="{{ asset('uploads/blogs/' . $blog->icon) }}"
                                                        alt="Blog Icon" style="max-width:100px; margin-top:10px;">
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Featured -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Featured</label>
                                                <select name="featured" class="form-control" required>
                                                    <option value="1" {{ $blog->featured ? 'selected' : '' }}>Yes</option>
                                                    <option value="0" {{ !$blog->featured ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" {{ $blog->status ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ !$blog->status ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Cancel</a>
                                            </div>
                                        </div>

                                    </div> <!-- row end -->
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
    var form_url = 'blogs/store';
    var redirect_url = 'blogs';
    var is_one_image_and_multiple_image_status = 'is_one_image';


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
