@extends('admin.layouts.app')
@section('content')
<style>
    .product-view-header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 15px; padding: 2rem; margin-bottom: 2rem; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); }
    .product-view-header::after { content: ''; position: absolute; top: 0; right: 0; width: 400px; height: 100%; background: linear-gradient(to left, rgba(255, 255, 255, 0.1), transparent); transform: skewX(-25deg); pointer-events: none; }
    .badge-premium { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; font-weight: 500; font-size: 0.85rem; letter-spacing: 0.5px; }
    .media-card { border-radius: 12px; overflow: hidden; position: relative; aspect-ratio: 16/9; background: #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .media-card video, .media-card img { width: 100%; height: 100%; object-fit: cover; }
    .ba-tag { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; text-transform: uppercase; }
    .section-preview { border-left: 4px solid #4f46e5; background: #f8fafc; padding: 1.5rem; border-radius: 0 12px 12px 0; margin-bottom: 1.5rem; }
    .variant-card { border-radius: 8px; border: 1px solid #e2e8f0; padding: 1rem; margin-bottom: 0.5rem; background: white; }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <!-- Header Section -->
            <div class="product-view-header shadow-lg">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @php 
                            $mainImage = $product->media->where('is_main', 1)->first() ?? $product->media->first();
                        @endphp
                        @if($mainImage)
                            <img src="{{ asset('uploads/product-media/' . $mainImage->file_path) }}" class="rounded border border-3 border-white shadow-lg bg-white p-1" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-white rounded shadow-lg d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;"><i data-feather="box" class="text-primary" style="width: 40px; height: 40px;"></i></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="badge-premium mb-1 d-inline-block">Premium Product ID: #{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <h1 class="text-white fw-bold mb-1">{{ $product->name }}</h1>
                        <p class="text-white-50 mb-0">
                            <i data-feather="tag" class="me-1"></i> ₹{{ $product->price }} 
                            @if($product->discount_percentage)
                                <span class="badge bg-danger ms-1">{{ $product->discount_percentage }}% OFF</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('admin.product-item.edit', encryptId($product->id)) }}" class="btn btn-light me-1">
                            <i data-feather="edit" class="me-50" style="width: 16px;"></i> Edit Product
                        </a>
                        <a href="{{ route('admin.product-item.index') }}" class="btn btn-outline-light">
                            <i data-feather="arrow-left" class="me-50" style="width: 16px;"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Sidebar: Quick Stats & Media -->
                <div class="col-lg-4">
                    <!-- Quick Stats Card -->
                    <div class="card mb-2 shadow-sm">
                        <div class="card-header border-bottom"><h4 class="card-title">Quick Stats</h4></div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between mb-1"><span>SKU:</span> <span class="fw-bold">{{ $product->sku ?? 'N/A' }}</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>Stock:</span> <span class="fw-bold">{{ $product->stock_quantity }}</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>Featured:</span> <span class="badge bg-{{ $product->is_featured ? 'success' : 'light' }}">{{ $product->is_featured ? 'Yes' : 'No' }}</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>New:</span> <span class="badge bg-{{ $product->is_new ? 'success' : 'light' }}">{{ $product->is_new ? 'Yes' : 'No' }}</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>Client App:</span> <span class="badge bg-{{ $product->show_in_client_app ? 'primary' : 'light' }}">{{ $product->show_in_client_app ? 'Visible' : 'Hidden' }}</span></div>
                            <div class="d-flex justify-content-between"><span>Status:</span> <span class="badge bg-{{ $product->status ? 'primary' : 'danger' }}">{{ $product->status ? 'Active' : 'Inactive' }}</span></div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    @if(count($product->variants) > 0)
                        <h5 class="mb-1 text-primary fw-bold">Variants ({{ count($product->variants) }})</h5>
                        @foreach($product->variants as $variant)
                            <div class="variant-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0">{{ $variant->variant_name }}</h6>
                                    <span class="text-primary fw-bold">₹{{ $variant->price ?? $product->price }}</span>
                                </div>
                                <div class="text-muted small mt-50">Stock: {{ $variant->stock_quantity }}</div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Media Gallery -->
                    @if(count($product->media) > 0)
                        <h5 class="mt-2 mb-1 text-primary fw-bold">Product Gallery ({{ count($product->media) }})</h5>
                        @foreach($product->media as $media)
                            <div class="media-card mb-2">
                                @if($media->type == 'image')
                                    <img src="{{ asset('uploads/product-media/' . $media->file_path) }}" alt="Gallery">
                                @else
                                    <video controls><source src="{{ asset('uploads/product-media/' . $media->file_path) }}" type="video/mp4"></video>
                                @endif
                                <div class="ba-tag">{{ $media->type }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Right Side: Content Sections -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Product Details & Catalog</h4>
                            <span class="text-muted small">{{ count($product->content_json ?? []) }} Dynamic Sections</span>
                        </div>
                        <div class="card-body pt-3 pb-5">
                            @if($product->short_description)
                                <div class="mb-2">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-50">Short Description</h6>
                                    <p class="text-dark">{{ $product->short_description }}</p>
                                </div>
                            @endif

                            @if($product->description)
                                <div class="mb-4">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-50">Full Description</h6>
                                    <div class="text-dark">{!! nl2br(e($product->description)) !!}</div>
                                </div>
                            @endif

                            @foreach($product->content_json ?? [] as $section)
                                @php $type = $section['type']; @endphp

                                @if($type == 'list' && !empty($section['points']))
                                    <div class="section-preview">
                                        <h5 class="mb-1 text-primary">{{ $section['title'] ?? 'Features' }}</h5>
                                        <div class="row">
                                            @foreach($section['points'] as $p)
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-1 align-items-start">
                                                        <i data-feather="check" class="text-success me-1" style="width: 15px; margin-top: 3px;"></i>
                                                        <span class="small">{{ $p }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($type == 'how_to_use' && !empty($section['steps']))
                                    <div class="section-preview">
                                        <h5 class="mb-1 text-primary">{{ $section['title'] ?? 'How to Use' }}</h5>
                                        <div class="row mt-1">
                                            @foreach($section['steps'] as $sKey => $step)
                                                <div class="col-md-6 mb-2">
                                                    <div class="card h-100 shadow-sm border mb-0">
                                                        <div class="card-body p-1 px-2">
                                                            <h6 class="mb-0 fw-bold text-primary">Step 0{{ $sKey+1 }}: {{ $step['title'] }}</h6>
                                                            <p class="small text-muted mb-0">{{ $step['desc'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($type == 'note' && !empty($section['points']))
                                    <div class="section-preview border-warning">
                                        <h5 class="mb-1 text-warning">{{ $section['title'] ?? 'Important Note' }}</h5>
                                        <div class="row">
                                            @foreach($section['points'] as $p)
                                                <div class="col-12">
                                                    <div class="d-flex mb-1 align-items-start">
                                                        <i data-feather="alert-circle" class="text-warning me-1" style="width: 15px; margin-top: 3px;"></i>
                                                        <span class="small">{{ $p }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @if(count($product->content_json ?? []) == 0)
                                <div class="text-center py-5">
                                    <i data-feather="layers" class="text-muted" style="width: 50px; height: 50px;"></i>
                                    <p class="text-muted mt-1">No dynamic sections added to this product.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
