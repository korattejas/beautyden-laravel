@extends('admin.layouts.app')
@section('content')

<div class="app-content content pa-catalog-page pa-catalog-view">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <div class="service-view-header pa-catalog-view-hero shadow-lg">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($service->icon)
                            <img src="{{ asset('uploads/service/' . $service->icon) }}" class="rounded-circle border border-3 border-white shadow-lg bg-white p-1" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-white rounded-circle shadow-lg d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;"><i data-feather="image" class="text-primary"></i></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="badge-premium mb-1 d-inline-block">Premium Service ID: #{{ str_pad($service->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <h1 class="text-white fw-bold mb-1">{{ $service->name }}</h1>
                        <p class="text-white-50 mb-0"><i data-feather="clock" class="me-1"></i> {{ $service->duration ?? 'Standard' }} | <i data-feather="tag" class="me-1"></i> {{ $service->price }} INR (Disc: {{ $service->discount_price ?? 0 }} INR)</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('admin.service-master.edit', encryptId($service->id)) }}" class="btn btn-light me-1">
                            <i data-feather="edit" class="me-50" style="width: 16px;"></i> Edit Service
                        </a>
                        <a href="{{ route('admin.service-master.index') }}" class="btn btn-outline-light">
                            <i data-feather="arrow-left" class="me-50" style="width: 16px;"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row pa-catalog-layout">
                <!-- Left Sidebar: Basic & Media -->
                <div class="col-lg-4">
                    <div class="pa-catalog-sidebar">
                    <!-- Basic Info Card -->
                    <div class="card mb-2">
                        <div class="card-header border-bottom"><h4 class="card-title">Quick Stats</h4></div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between mb-1"><span>Rating:</span> <span class="fw-bold">{{ $service->rating ?? '0' }} ⭐ ({{ $service->reviews ?? 0 }} Reviews)</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>Skin Type:</span> <span class="badge bg-light-primary text-primary">{{ $service->skin_type }}</span></div>
                            <div class="d-flex justify-content-between mb-1"><span>Popular:</span> <span class="badge bg-{{ $service->is_popular ? 'success' : 'light' }}">{{ $service->is_popular ? 'Yes' : 'No' }}</span></div>
                            <div class="d-flex justify-content-between"><span>Status:</span> <span class="badge bg-{{ $service->status ? 'primary' : 'danger' }}">{{ $service->status ? 'Active' : 'Inactive' }}</span></div>
                        </div>
                    </div>

                    <!-- Service Variants Card -->
                    @if($service->has_variants && count($service->variants ?? []) > 0)
                        <div class="card mb-2 shadow-sm border-0">
                            <div class="card-header border-bottom"><h4 class="card-title text-primary"><i data-feather="layers" class="me-50"></i> Service Variants</h4></div>
                            <div class="card-body pt-2 px-2">
                                @foreach($service->variants as $variant)
                                    <div class="border rounded p-2 mb-2 bg-light position-relative">
                                        {{-- Thumbnail + Name Row --}}
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if($variant->thumbnail_image)
                                                <img src="{{ asset('uploads/service-variant/' . $variant->thumbnail_image) }}"
                                                     style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #dee2e6;">
                                            @else
                                                <div style="width:48px;height:48px;border-radius:8px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;">
                                                    <i data-feather="image" style="width:18px;color:#aaa;"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $variant->name }}</div>
                                                @if($variant->description)
                                                    <div class="text-muted small mt-1">{{ $variant->description }}</div>
                                                @endif
                                                @if($variant->duration)
                                                    <div class="text-muted small"><i data-feather="clock" style="width:11px"></i> {{ $variant->duration }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- Price + Discount Row --}}
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div>
                                                <span class="fw-bold text-success fs-6">₹{{ $variant->price }}</span>
                                                @if($variant->discount_percentage)
                                                    <span class="badge bg-danger ms-1">{{ $variant->discount_percentage }}% OFF</span>
                                                @endif
                                            </div>
                                            {{-- Rating + Reviews --}}
                                            <div class="text-end">
                                                @if($variant->rating > 0)
                                                    <span class="badge bg-warning text-dark">
                                                        ⭐ {{ number_format($variant->rating, 1) }}
                                                    </span>
                                                @endif
                                                @if($variant->reviews > 0)
                                                    <span class="text-muted small ms-1">({{ $variant->reviews }} reviews)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Banner Media Section -->
                    <h5 class="mb-1 text-primary fw-bold">Banner Media ({{ count($service->banner_media ?? []) }})</h5>
                    @foreach($service->banner_media ?? [] as $media)
                        @php 
                            $isScroll = isset($media['is_scroll_banner_image']) ? $media['is_scroll_banner_image'] : 1; 
                        @endphp
                        <div class="media-card mb-2">
                            @if($media['type'] == 'image')
                                <img src="{{ asset('uploads/service-media/' . $media['url']) }}" alt="Banner">
                            @elseif($media['type'] == 'video')
                                <video controls><source src="{{ asset('uploads/service-media/' . $media['url']) }}" type="video/mp4"></video>
                            @endif
                            <div class="ba-tag">{{ ucfirst($media['type']) }} {{ $isScroll ? '(Scroll Banner)' : '(Normal Banner)' }}</div>
                        </div>
                    @endforeach

                    <!-- Before After -->
                    @if(count($service->before_after ?? []) > 0)
                        <h5 class="mt-3 mb-1 text-primary fw-bold">Before / After Gallery</h5>
                        @foreach($service->before_after ?? [] as $ba)
                            @php 
                                $img = is_array($ba) ? ($ba['before'] ?? ($ba['after'] ?? null)) : $ba; 
                            @endphp
                            @if($img)
                                <div class="ba-card mb-2 p-1 position-relative">
                                    <div class="ba-tag bg-primary shadow-sm">B&A Result</div>
                                    <img src="{{ asset('uploads/service-media/' . $img) }}" class="img-fluid rounded w-100" style="max-height: 250px; object-fit: cover;">
                                </div>
                            @endif
                        @endforeach
                    @endif
                    </div>
                </div>

                <!-- Right Side: Content Sections -->
                <div class="col-lg-8">
                    <div class="card pa-catalog-card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Detailed Service Catalog</h4>
                            <span class="text-muted small">{{ count($service->content_json ?? []) }} Dynamic Sections</span>
                        </div>
                        <div class="card-body pt-3 pb-5">
                            @if($service->description)
                                <div class="mb-4">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-1">Standard Description</h6>
                                    <p class="text-dark">{{ $service->description }}</p>
                                </div>
                            @endif

                            @foreach($service->content_json ?? [] as $section)
                                @php $type = $section['type']; @endphp

                                @if($type == 'overview' && !empty($section['essential_ids']))
                                    <div class="section-preview">
                                        <h5 class="mb-1">Service Essentials</h5>
                                        <div class="d-flex flex-wrap">
                                            @foreach($section['essential_ids'] as $eid)
                                                @if(isset($essentials[$eid]))
                                                    <div class="d-flex align-items-center me-2 mb-2 p-1 border rounded bg-white shadow-sm" style="min-width: 150px;">
                                                        <div class="essential-icon-view">
                                                            <img src="{{ asset('uploads/essential/' . $essentials[$eid]->icon) }}" style="width: 25px; height: 25px;">
                                                        </div>
                                                        <span class="small fw-bold">{{ $essentials[$eid]->title }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif(($type == 'ritual' || $type == 'procedure') && !empty($section['steps']))
                                    <div class="section-preview">
                                        <h5 class="mb-1 text-primary">{{ $section['title'] ?? 'The Process' }}</h5>
                                        <div class="row mt-2">
                                            @foreach($section['steps'] as $sKey => $step)
                                                <div class="col-md-6 mb-2">
                                                    <div class="card h-100 shadow-sm border mb-0">
                                                        @if($step['image'])
                                                            <img src="{{ asset('uploads/service-content/' . $step['image']) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                        @endif
                                                        <div class="card-body p-1 px-2">
                                                            <h6 class="mb-0 fw-bold text-primary">0{{ $sKey+1 }}. {{ $step['title'] }}</h6>
                                                            <p class="small text-muted mb-0">{{ $step['desc'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($type == 'expert' && (!empty($section['points']) || !empty($section['image'])))
                                    <div class="section-preview">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded border-start border-primary border-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-3 text-center">
                                                    @if(!empty($section['image']))
                                                        <img src="{{ asset('uploads/service-content/' . $section['image']) }}" class="rounded-circle border border-3 border-white shadow-lg" style="width: 120px; height: 120px; object-fit: cover;">
                                                    @endif
                                                    <h6 class="mt-1 mb-0 fw-bold">Service Expert</h6>
                                                </div>
                                                <div class="col-md-9 border-start ps-3">
                                                    <h5 class="mb-1 fw-bold text-primary">Professional Qualifications</h5>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($section['points'] ?? [] as $p)
                                                            <li class="mb-0 small"><i data-feather="check-circle" class="text-success me-1" style="width: 14px;"></i> {{ $p }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                 @elseif(($type == 'list' || $type == 'aftercare' || $type == 'note') && !empty($section['points']))
                                    @php
                                        $icon = "arrow-right";
                                        $color = "primary";
                                        $borderClass = "";
                                        if($type == 'aftercare') { $icon = "heart"; $color = "danger"; $borderClass = "border-danger"; }
                                        if($type == 'note') { $icon = "alert-circle"; $color = "warning"; $borderClass = "border-warning"; }
                                    @endphp
                                    <div class="section-preview {{ $borderClass }}">
                                        <h5 class="mb-1 text-{{ $color }}">{{ $section['title'] ?? ($type == 'aftercare' ? 'Aftercare Tips' : ($type == 'note' ? 'Please Note' : 'Information')) }}</h5>
                                        <div class="row">
                                            @foreach($section['points'] as $p)
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-1 align-items-start">
                                                        <i data-feather="{{ $icon }}" class="text-{{ $color }} me-1" style="width: 15px; margin-top: 3px;"></i>
                                                        <span class="small">{{ $p }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($type == 'protocol' && !empty($section['items']))
                                    <div class="section-preview">
                                        <h5 class="mb-1">Safety & Hygiene Protocols</h5>
                                        <div class="row mt-1">
                                            @foreach($section['items'] as $item)
                                                <div class="col-4 text-center mb-2">
                                                    @if($item['image'])
                                                        <div class="bg-white p-1 rounded shadow-sm mb-1 d-inline-block">
                                                            <img src="{{ asset('uploads/service-content/' . $item['image']) }}" style="width: 60px; height: 60px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                    <div class="small fw-bold">{{ $item['title'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @if(count($service->content_json ?? []) == 0)
                                <div class="text-center py-5">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7223933.png" style="max-width: 200px;">
                                    <p class="text-muted">No dynamic sections added to this service catalog.</p>
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
