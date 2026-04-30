@extends('admin.layouts.app')
@section('content')
<style>
    .service-view-header { background: linear-gradient(135deg, #1a237e 0%, #311b92 100%); color: white; border-radius: 15px; padding: 2rem; margin-bottom: 2rem; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); }
    .service-view-header::after { content: ''; position: absolute; top: 0; right: 0; width: 400px; height: 100%; background: linear-gradient(to left, rgba(255, 255, 255, 0.1), transparent); transform: skewX(-25deg); pointer-events: none; }
    .badge-premium { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; font-weight: 500; font-size: 0.85rem; letter-spacing: 0.5px; }
    .media-card { border-radius: 12px; overflow: hidden; position: relative; aspect-ratio: 16/9; background: #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .media-card video, .media-card img { width: 100%; height: 100%; object-fit: cover; }
    .ba-card { border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .ba-tag { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; text-transform: uppercase; }
    .section-preview { border-left: 4px solid #1a237e; background: #f8fafc; padding: 1.5rem; border-radius: 0 12px 12px 0; margin-bottom: 1.5rem; }
    .essential-icon-view { width: 40px; height: 40px; border-radius: 8px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; margin-right: 10px; }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <!-- Header Section -->
            <div class="service-view-header shadow-lg">
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

            <div class="row">
                <!-- Left Sidebar: Basic & Media -->
                <div class="col-lg-4">
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

                    <!-- Banner Media Section -->
                    <h5 class="mb-1 text-primary fw-bold">Banner Media ({{ count($service->banner_media ?? []) }})</h5>
                    @foreach($service->banner_media ?? [] as $media)
                        <div class="media-card mb-2">
                            @if($media['type'] == 'image')
                                <img src="{{ asset('uploads/service-media/' . $media['url']) }}" alt="Banner">
                            @else
                                <video controls><source src="{{ asset('uploads/service-media/' . $media['url']) }}" type="video/mp4"></video>
                            @endif
                            <div class="ba-tag">{{ $media['type'] }}</div>
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

                <!-- Right Side: Content Sections -->
                <div class="col-lg-8">
                    <div class="card">
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

                                @elseif($type == 'list' && !empty($section['points']))
                                    <div class="section-preview">
                                        <h5 class="mb-1">{{ $section['title'] ?? 'Information' }}</h5>
                                        <div class="row">
                                            @foreach($section['points'] as $p)
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-1 align-items-start">
                                                        <i data-feather="arrow-right" class="text-primary me-1" style="width: 15px; margin-top: 3px;"></i>
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
