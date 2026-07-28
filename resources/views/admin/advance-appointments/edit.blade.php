@extends('admin.layouts.app')

@section('page_title', 'Edit Appointment')
@section('page_heading', 'Edit Appointment')

@section('header_style_content')
<link rel="stylesheet" href="{{ asset('panel-assets/css/premium-appointment-form.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="app-content content pa-appointment-form-page">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="pa-catalog-toolbar" style="margin-top:1rem;margin-left:1.5rem;margin-right:1.5rem;">
            <div>
                <h2>Edit Advance Appointment</h2>
                <p>Update booking details, services and pricing</p>
            </div>
            <div class="pa-catalog-toolbar-actions">
                <a href="{{ route('admin.advance-appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" form="addEditForm" class="btn btn-primary" id="submitBtn">Update Appointment</button>
            </div>
        </div>

        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="pa-card-subtle">
                            <div class="pa-card-header">
                                <h6><i class="bi bi-pencil-square me-2 text-muted"></i>Appointment Details</h6>
                            </div>
                            <div class="pa-card-body">
                                <form method="POST" data-parsley-validate id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $appointment->id }}">
                                    <input type="hidden" id="form-method" value="edit">
                                    <input type="hidden" name="services_json" id="services_json">
                                    <input type="hidden" name="travel_charges" id="hidden_travel">
                                    <input type="hidden" name="discount_percent" id="hidden_discount">
                                    <input type="hidden" name="discount_amount" id="hidden_discount_amount">
                                    <input type="hidden" name="sub_total" id="hidden_subtotal">
                                    <input type="hidden" name="grand_total" id="hidden_grandtotal">

                                    <div class="row gy-2 gx-3">
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="first_name">First Name</label>
                                                <input type="text" class="form-control live-json" id="first_name" name="first_name" value="{{ $appointment->first_name }}" placeholder="Enter first name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" class="form-control live-json" id="last_name" name="last_name" value="{{ $appointment->last_name }}" placeholder="Enter last name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control live-json" id="email" name="email" value="{{ $appointment->email }}" placeholder="Enter email address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="phone">Phone</label>
                                                <input type="number" class="form-control live-json" id="phone" name="phone" value="{{ $appointment->phone }}" placeholder="Enter phone number">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="appointment_date">Appointment Date</label>
                                                <input type="date" class="form-control live-json" id="appointment_date" name="appointment_date" value="{{ $appointment->appointment_date }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pa-form-field">
                                                <label for="appointment_time">Appointment Time</label>
                                                <input type="time" class="form-control live-json" id="appointment_time" name="appointment_time" value="{{ $appointment->appointment_time }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="pa-form-field">
                                                <label for="service_address">Service Address</label>
                                                <textarea class="form-control live-json" id="service_address" name="service_address" rows="3" placeholder="Enter full service address">{{ $appointment->service_address }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="pa-form-field">
                                                <label for="city_id">City</label>
                                                <select name="city_id" id="city_id" class="form-control select2">
                                                    <option value="">Select City</option>
                                                    @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" {{ $appointment->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-8 col-12 mt-4">
                                            <div id="dynamicServices"></div>
                                        </div>

                                        <div class="col-lg-4 col-12 mt-4">
                                            <div class="pa-service-summary">
                                                <h5 class="pa-service-summary-title">Service Summary</h5>
                                                <div id="invoiceList" style="max-height:250px;overflow:auto;"></div>
                                                <hr>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Subtotal</span>
                                                    <span>₹ <span id="subTotal">0.00</span></span>
                                                </div>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Travelling Charges</span>
                                                    <input type="number" id="travelCharges" class="pa-inline-input" value="0">
                                                </div>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Discount (%)</span>
                                                    <input type="number" id="discountPercent" class="pa-inline-input" value="0" min="0" max="100">
                                                </div>
                                                <div id="discountRow" style="display:none;justify-content:space-between;margin-bottom:8px;color:#ea5455;">
                                                    <span>Discount</span>
                                                    <span>- ₹ <span id="discountAmount">0.00</span></span>
                                                </div>
                                                
                                                <div id="walletRow" style="display:none;justify-content:space-between;margin-bottom:8px;color:#ea5455;">
                                                    <span>Wallet Used</span>
                                                    <span>- ₹ <span id="walletUsedAmount">0.00</span></span>
                                                </div>

                                                <div id="couponRow" style="display:none;justify-content:space-between;margin-bottom:8px;color:#28c76f;font-size:13px;">
                                                    <span>Coupon Applied</span>
                                                    <span id="couponCodeText" style="font-weight:600;"></span>
                                                </div>

                                                <div id="durationRow" style="display:none;justify-content:space-between;margin-bottom:8px;color:#666;font-size:13px;">
                                                    <span>Total Duration</span>
                                                    <span id="totalDurationText"></span>
                                                </div>

                                                <hr>
                                                <div style="display:flex;justify-content:space-between;font-size:20px;font-weight:700;">
                                                    <span>Total</span>
                                                    <span style="color:#28c76f;">₹ <span id="grandTotal">0.00</span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <div style="margin-bottom:10px;">
                                                <label style="font-weight:600;">
                                                    <input type="checkbox" id="customToggle"> Add Custom Service
                                                </label>
                                            </div>
                                            <div id="customSection" class="pa-custom-service-box" style="display:none;">
                                                <div class="d-flex gap-2 align-items-center flex-nowrap">
                                                    <input type="text" id="customName" class="form-control flex-grow-1" placeholder="Service Name">
                                                    <input type="number" id="customPrice" class="form-control" placeholder="Price" style="max-width:120px;">
                                                    <button type="button" id="addCustomBtn" class="btn btn-primary" style="white-space:nowrap;">Add</button>
                                                </div>
                                                <div id="customList" class="row mt-3"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="pa-form-field">
                                                <label for="special_notes">Special Notes</label>
                                                <textarea class="form-control live-json" id="special_notes" name="special_notes" rows="3" placeholder="Any special instructions">{{ $appointment->special_notes }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="pa-form-field">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1" {{ isset($appointment) && $appointment->status == 1 ? 'selected' : '' }}>Pending</option>
                                                    <option value="2" {{ isset($appointment) && $appointment->status == 2 ? 'selected' : '' }}>Assigned</option>
                                                    <option value="3" {{ isset($appointment) && $appointment->status == 3 ? 'selected' : '' }}>Completed</option>
                                                    <option value="4" {{ isset($appointment) && $appointment->status == 4 ? 'selected' : '' }}>Rejected</option>
                                                </select>
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
    var form_url = 'advance-appointments/store';
    var redirect_url = 'advance-appointments';
    $(document).ready(function() {
        let savedData = @json($appointment->services_data);
        if (typeof savedData === 'string') savedData = JSON.parse(savedData);

        $('.select2').select2({
            width: '100%'
        });

        // Initial City Load
        if ($('#city_id').val()) {
            loadServices($('#city_id').val(), true);
        }

        $('#city_id').on('change', function() {
            loadServices($(this).val(), false);
        });

        // Load Services from Server
        function loadServices(cityId, isFirstLoad) {
            if (!cityId) return;
            $.get('/admin/get-advance-city-services/' + cityId, function(response) {
                let tabsHtml = `<ul class="nav nav-pills mb-3" role="tablist" style="gap: 10px; overflow-x: auto; flex-wrap: wrap;">`;
                let contentHtml = `<div class="tab-content">`;
                let isFirst = true;

                $.each(response, function(catId, cat) {
                    let activeClass = isFirst ? 'active' : '';
                    let activePane = isFirst ? 'show active' : '';
                    let btnStyle = 'padding: 8px 16px; border: 1px solid #7367f0; border-radius: 8px; font-weight: 600;';

                    tabsHtml += `
                        <li class="nav-item" role="presentation">
                            <button class="nav-link ${activeClass}" id="cat-tab-${catId}" data-bs-toggle="pill" data-bs-target="#cat-pane-${catId}" type="button" role="tab" style="${btnStyle}">
                                ${cat.name}
                            </button>
                        </li>
                    `;

                    contentHtml += `
                        <div class="tab-pane fade ${activePane}" id="cat-pane-${catId}" role="tabpanel">
                    `;

                    if (cat.services) {
                        contentHtml += `<div class="row">`;
                        $.each(cat.services, function(i, s) {
                            contentHtml += serviceCard(s);
                        });
                        contentHtml += `</div>`;
                    }

                    if (cat.subcategories) {
                        let hasSubcategories = Object.keys(cat.subcategories).length > 0;
                        if (hasSubcategories) {
                            let subTabsHtml = `<ul class="nav nav-pills mt-3 mb-3" role="tablist" style="gap: 10px; overflow-x: auto; flex-wrap: wrap;">`;
                            let subContentHtml = `<div class="tab-content">`;
                            let isSubFirst = true;

                            $.each(cat.subcategories, function(subId, subCategory) {
                                let subActiveClass = isSubFirst ? 'active' : '';
                                let subActivePane = isSubFirst ? 'show active' : '';
                                let subBtnStyle = 'padding: 6px 14px; border: 1px solid #888; border-radius: 6px; font-weight: 500; font-size: 14px;';

                                subTabsHtml += `
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link ${subActiveClass}" id="sub-tab-${catId}-${subId}" data-bs-toggle="pill" data-bs-target="#sub-pane-${catId}-${subId}" type="button" role="tab" style="${subBtnStyle}">
                                            ${subCategory.name}
                                        </button>
                                    </li>
                                `;

                                subContentHtml += `
                                    <div class="tab-pane fade ${subActivePane}" id="sub-pane-${catId}-${subId}" role="tabpanel">
                                        <div class="row">
                                `;

                                $.each(subCategory.services, function(i, service) {
                                    subContentHtml += serviceCard(service);
                                });

                                subContentHtml += `</div></div>`;
                                isSubFirst = false;
                            });

                            subTabsHtml += `</ul>`;
                            subContentHtml += `</div>`;
                            
                            contentHtml += subTabsHtml + subContentHtml;
                        }
                    }
                    contentHtml += `</div>`;
                    isFirst = false;
                });

                tabsHtml += `</ul>`;
                contentHtml += `</div>`;
                $('#dynamicServices').html(tabsHtml + contentHtml);

                if (isFirstLoad && savedData) fillFormFromJSON();
                else calculateTotal();
            });
        }

        function serviceCard(service) {
            let cardsHtml = '';
            
            if (service.has_variants && service.variants && service.variants.length > 0) {
                // Return a card for EACH variant
                $.each(service.variants, function(i, v) {
                    let finalPrice = parseFloat(v.price) || 0;
                    let discPercent = parseFloat(v.discount_price) || 0;
                    let mainPrice = finalPrice;
                    if (discPercent > 0 && discPercent < 100) {
                        mainPrice = finalPrice / (1 - (discPercent / 100));
                    } else if (discPercent > 0) {
                        mainPrice = finalPrice + (finalPrice * discPercent / 100);
                    }
                    mainPrice = Math.round(mainPrice);

                    cardsHtml += `
        <div class="col-md-6 mb-3">
            <div class="service-card" data-service-id="${service.id}" data-variant-id="${v.id}" data-duration="${v.duration || service.duration || 0}"
                style="border:1px solid #e5e5e5;padding:18px;border-radius:14px;background:#fff;transition:0.25s;box-shadow:0 2px 6px rgba(0,0,0,0.05);">

                <label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
                    <input type="checkbox" class="service-check">
                    <span class="service-name">${service.name} - ${v.name}</span>
                </label>

                <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Main Price</div>
                        <input type="number" value="${mainPrice}" class="main-price" style="width:80px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#555;background:#f4f4f4;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Disc (%)</div>
                        <input type="number" value="${discPercent}" class="discount-percent" style="width:70px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#555;background:#f4f4f4;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Final Price</div>
                        <input type="number" value="${finalPrice.toFixed(2)}" class="price" style="width:90px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#7367f0;background:#f8f7ff;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Qty</div>
                        <div style="display:flex;align-items:center;border:1px solid #ddd;border-radius:8px;overflow:hidden;width:90px;background:#fff;">
                            <button type="button" class="qty-minus" style="width:25px;border:none;background:#f4f4f4;font-size:18px;" disabled>−</button>
                            <input type="text" value="1" class="qty" style="width:40px;border:none;text-align:center;font-weight:600;" readonly>
                            <button type="button" class="qty-plus" style="width:25px;border:none;background:#f4f4f4;font-size:18px;" disabled>+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
                });
            } else {
                // Return a card for the service (no variants)
                let finalPrice = parseFloat(service.price) || 0;
                let discPercent = parseFloat(service.discount_price) || 0;
                let mainPrice = finalPrice;
                if (discPercent > 0 && discPercent < 100) {
                    mainPrice = finalPrice / (1 - (discPercent / 100));
                } else if (discPercent > 0) {
                    mainPrice = finalPrice + (finalPrice * discPercent / 100);
                }
                mainPrice = Math.round(mainPrice);

                cardsHtml += `
        <div class="col-md-6 mb-3">
            <div class="service-card" data-service-id="${service.id}" data-variant-id="" data-duration="${service.duration || 0}"
                style="border:1px solid #e5e5e5;padding:18px;border-radius:14px;background:#fff;transition:0.25s;box-shadow:0 2px 6px rgba(0,0,0,0.05);">

                <label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
                    <input type="checkbox" class="service-check">
                    <span class="service-name">${service.name}</span>
                </label>

                <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Main Price</div>
                        <input type="number" value="${mainPrice}" class="main-price" style="width:80px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#555;background:#f4f4f4;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Disc (%)</div>
                        <input type="number" value="${discPercent}" class="discount-percent" style="width:70px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#555;background:#f4f4f4;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Final Price</div>
                        <input type="number" value="${finalPrice.toFixed(2)}" class="price" style="width:90px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#7367f0;background:#f8f7ff;" disabled>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#888;margin-bottom:4px;">Qty</div>
                        <div style="display:flex;align-items:center;border:1px solid #ddd;border-radius:8px;overflow:hidden;width:90px;background:#fff;">
                            <button type="button" class="qty-minus" style="width:25px;border:none;background:#f4f4f4;font-size:18px;" disabled>−</button>
                            <input type="text" value="1" class="qty" style="width:40px;border:none;text-align:center;font-weight:600;" readonly>
                            <button type="button" class="qty-plus" style="width:25px;border:none;background:#f4f4f4;font-size:18px;" disabled>+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
            }
            return cardsHtml;
        }



        // FILL DATA FROM JSON
        function fillFormFromJSON() {
            if (savedData.summary) {
                $('#travelCharges').val(savedData.summary.travel_charges || 0);
                $('#discountPercent').val(savedData.summary.discount_percent || 0);
            }
            if (savedData.services) {
                savedData.services.forEach(item => {
                    if (item.type === "service") {
                        $('.service-card').each(function() {
                            let card = $(this);
                            let cardServiceId = parseInt(card.data('service-id'));
                            let cardVariantId = parseInt(card.data('variant-id')) || null;
                            let cardName = card.find('.service-name').text().trim();

                            let itemServiceId = item.service_master_id ? parseInt(item.service_master_id) : null;
                            let itemVariantId = item.variant_id ? parseInt(item.variant_id) : null;

                            let isMatch = false;

                            if (itemServiceId) {
                                if (cardServiceId === itemServiceId && cardVariantId === itemVariantId) {
                                    isMatch = true;
                                }
                            } else {
                                let savedServiceIds = "{{ $appointment->service_id }}".split(',').map(id => id.trim());
                                let itemName = item.name ? item.name.trim() : "";
                                
                                if (savedServiceIds.includes(cardServiceId.toString())) {
                                    if (cardVariantId === null) {
                                        // No variants
                                        if (cardName === itemName) isMatch = true;
                                    } else {
                                        // Has variants
                                        if (cardName === itemName || cardName.startsWith(itemName)) isMatch = true;
                                    }
                                }
                            }

                            if (isMatch) {
                                card.find('.service-check').prop('checked', true);
                                card.find('.price').val(item.price).prop('disabled', false);
                                card.find('.qty').val(item.qty);
                                card.find('.qty-plus, .qty-minus').prop('disabled', false);
                                card.css({
                                    border: '2px solid #7367f0',
                                    background: '#f8f7ff',
                                    boxShadow: '0 8px 18px rgba(115,103,240,0.15)'
                                });
                            }
                        });
                    } else if (item.type === "custom") {
                        $('#customToggle').prop('checked', true).trigger('change');
                        addCustomRow(item.name, item.price, item.qty);
                    }
                });
            }
            calculateTotal();
        }

        // Events for calculation and JSON update
        $(document).on('click', '.cat-toggle, .sub-toggle', function() {
            $('#' + $(this).data('id')).slideToggle();
        });

        $(document).on('change', '.service-check', function() {
            let chk = $(this).is(':checked');
            let card = $(this).closest('.service-card');
            card.find('.price, .discount-percent, .qty-plus, .qty-minus').prop('disabled', !chk);
            card.css(chk ? {
                border: '2px solid #7367f0',
                background: '#f8f7ff',
                boxShadow: '0 8px 18px rgba(115,103,240,0.15)'
            } : {
                border: '1px solid #e5e5e5',
                background: '#fff',
                boxShadow: '0 2px 6px rgba(0,0,0,0.05)'
            });
            calculateTotal();
        });

        $(document).on('click', '.qty-plus', function() {
            let i = $(this).siblings('.qty');
            i.val(parseInt(i.val()) + 1);
            calculateTotal();
        });
        $(document).on('click', '.qty-minus', function() {
            let i = $(this).siblings('.qty');
            if (parseInt(i.val()) > 1) {
                i.val(parseInt(i.val()) - 1);
                calculateTotal();
            }
        });

        $(document).on('keyup change', '.price, .main-price, .discount-percent', function() {
            let card = $(this).closest('.service-card');
            
            if ($(this).hasClass('price') || $(this).hasClass('discount-percent')) {
                // If Final Price or Disc % changes, recalculate Main Price
                let finalPrice = parseFloat(card.find('.price').val()) || 0;
                let discPercent = parseFloat(card.find('.discount-percent').val()) || 0;
                let mainPrice = finalPrice;
                if (discPercent > 0 && discPercent < 100) {
                    mainPrice = finalPrice / (1 - (discPercent / 100));
                } else if (discPercent > 0) {
                    mainPrice = finalPrice + (finalPrice * discPercent / 100);
                }
                card.find('.main-price').val(Math.round(mainPrice));
            } else if ($(this).hasClass('main-price')) {
                // If Main Price changes manually, recalculate Final Price
                let mainPrice = parseFloat(card.find('.main-price').val()) || 0;
                let discPercent = parseFloat(card.find('.discount-percent').val()) || 0;
                let finalPrice = mainPrice;
                if (discPercent > 0) {
                    finalPrice = mainPrice - (mainPrice * discPercent / 100);
                }
                card.find('.price').val(finalPrice.toFixed(2));
            }
            
            calculateTotal();
        });

        // Custom Service Logic (Create Design)
        $('#customToggle').on('change', function() {
            $('#customSection').toggle($(this).is(':checked'));
        });
        $('#addCustomBtn').on('click', function() {
            let n = $('#customName').val(),
                p = $('#customPrice').val();
            if (n && p) {
                addCustomRow(n, p, 1);
                $('#customName, #customPrice').val('');
                calculateTotal();
            }
        });

        function addCustomRow(name, price, qty) {
            $('#customList').append(`<div class="custom-item col-md-6 mb-2">
            <div style="border:1px dashed #7367f0;padding:12px;border-radius:10px;background:#fff;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <input type="text" value="${name}" class="custom-name" style="font-weight:600;border:1px solid #ddd;border-radius:6px;padding:4px;flex-grow:1;margin-right:10px;">
                    <button type="button" class="btn btn-sm text-danger remove-custom" style="padding:0;font-size:20px;line-height:1;">&times;</button>
                </div>
                <div style="display:flex;gap:10px;margin-top:6px;">
                    <input type="number" value="${price}" class="custom-price" style="width:90px;border:1px solid #ddd;border-radius:6px;padding:4px;">
                    <div style="display:flex;border:1px solid #ddd;border-radius:6px;overflow:hidden;">
                        <button type="button" class="c-qty-minus" style="border:none;background:#f4f4f4;padding:0 8px;">−</button>
                        <input type="text" value="${qty}" class="custom-qty" style="width:30px;border:none;text-align:center;" readonly>
                        <button type="button" class="c-qty-plus" style="border:none;background:#f4f4f4;padding:0 8px;">+</button>
                    </div>
                </div>
            </div>
        </div>`);
        }

        $(document).on('click', '.remove-custom', function() {
            $(this).closest('.custom-item').remove();
            calculateTotal();
        });
        $(document).on('click', '.c-qty-plus', function() {
            let i = $(this).siblings('.custom-qty');
            i.val(parseInt(i.val()) + 1);
            calculateTotal();
        });
        $(document).on('click', '.c-qty-minus', function() {
            let i = $(this).siblings('.custom-qty');
            if (parseInt(i.val()) > 1) {
                i.val(parseInt(i.val()) - 1);
                calculateTotal();
            }
        });

        // LIVE JSON UPDATE on any field change
        $(document).on('keyup change', '.live-json, #travelCharges, #discountPercent, .price, .custom-price, .custom-name', function() {
            calculateTotal();
        });

        /* =====================================
           VARIANT CHANGE
        ===================================== */
        $(document).on('change', '.variant-select', function() {
            let card = $(this).closest('.service-card');
            let selectedOption = $(this).find('option:selected');
            let newPrice = selectedOption.data('price');
            card.find('.price').val(newPrice);
            calculateTotal();
        });

        function calculateTotal() {
            let servicesArray = [],
                subtotal = 0,
                totalDuration = 0,
                invoiceHtml = '';

            $('.service-check:checked').each(function() {
                let card = $(this).closest('.service-card');
                let serviceId = card.data('service-id');
                let name = card.find('.service-name').text().trim();
                let price = parseFloat(card.find('.price').val()) || 0;
                let qty = parseInt(card.find('.qty').val()) || 1;
                let duration = parseInt(card.data('duration')) || 0;
                
                let variantId = card.data('variant-id') || null;

                let total = price * qty;
                subtotal += total;
                totalDuration += (duration * qty);

                servicesArray.push({
                    type: "service",
                    service_master_id: serviceId,
                    variant_id: variantId,
                    name: name,
                    price: price,
                    qty: qty,
                    total: total
                });

                invoiceHtml += `
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:14px;">
                    <div>${name} (${qty} × ₹${price})</div>
                    <div>₹${total.toFixed(2)}</div>
                </div>
            `;
            });

            $('.custom-item').each(function() {
                let n = $(this).find('.custom-name').val(),
                    p = parseFloat($(this).find('.custom-price').val()) || 0,
                    q = parseInt($(this).find('.custom-qty').val()) || 1;
                let tot = p * q;
                subtotal += tot;
                servicesArray.push({
                    type: "custom",
                    name: n,
                    price: p,
                    qty: q,
                    total: tot
                });
                invoiceHtml += `<div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>${n} (${q}x)</span><span>₹${tot.toFixed(2)}</span></div>`;
            });

            let travel = parseFloat($('#travelCharges').val()) || 0;
            let discP = parseFloat($('#discountPercent').val()) || 0;
            let discA = (subtotal + travel) * discP / 100;

            let walletUsed = 0;
            let couponCode = null;

            if (typeof savedData !== 'undefined' && savedData && savedData.summary) {
                walletUsed = parseFloat(savedData.summary.wallet_used) || 0;
                couponCode = savedData.summary.coupon_code || null;
                
                // If discount_percent is 0 but discount_amount was provided by the app, use it
                if (discP === 0 && savedData.summary.discount_amount && parseFloat(savedData.summary.discount_amount) > 0) {
                    discA = parseFloat(savedData.summary.discount_amount);
                }
            }

            let grand = (subtotal + travel) - discA - walletUsed;
            if (grand < 0) grand = 0;

            $('#invoiceList').html(invoiceHtml);
            $('#subTotal').text(subtotal.toFixed(2));
            $('#discountAmount').text(discA.toFixed(2));
            $('#discountRow').toggle(discA > 0);
            
            if (walletUsed > 0) {
                $('#walletUsedAmount').text(walletUsed.toFixed(2));
                $('#walletRow').show();
            } else {
                $('#walletRow').hide();
            }

            if (couponCode) {
                $('#couponCodeText').text(couponCode);
                $('#couponRow').show();
            } else {
                $('#couponRow').hide();
            }

            if (totalDuration > 0) {
                let hrs = Math.floor(totalDuration / 60);
                let mins = totalDuration % 60;
                let durationStr = "";
                if (hrs > 0) durationStr += hrs + " Hr" + (hrs > 1 ? "s" : "") + " ";
                if (mins > 0) durationStr += mins + " Min" + (mins > 1 ? "s" : "");
                
                $('#totalDurationText').text(durationStr.trim());
                $('#durationRow').show();
            } else {
                $('#durationRow').hide();
            }

            $('#grandTotal').text(grand.toFixed(2));

            // Update hidden fields
            $('#hidden_travel').val(travel);
            $('#hidden_discount').val(discP);
            $('#hidden_discount_amount').val(discA.toFixed(2));
            $('#hidden_subtotal').val(subtotal.toFixed(2));
            $('#hidden_grandtotal').val(grand.toFixed(2));

            // FINAL JSON
            let finalJson = {
                client: {
                    first_name: $('input[name="first_name"]').val(),
                    last_name: $('input[name="last_name"]').val(),
                    email: $('input[name="email"]').val(),
                    phone: $('input[name="phone"]').val()
                },
                appointment: {
                    date: $('input[name="appointment_date"]').val(),
                    time: $('input[name="appointment_time"]').val(),
                    address: $('textarea[name="service_address"]').val(),
                    notes: $('textarea[name="special_notes"]').val()
                },
                services: servicesArray,
                summary: {
                    sub_total: subtotal.toFixed(2),
                    travel_charges: travel.toFixed(2),
                    discount_percent: discP,
                    discount_amount: discA.toFixed(2),
                    coupon_code: couponCode,
                    wallet_used: walletUsed.toFixed(2),
                    total_duration: totalDuration,
                    grand_total: grand.toFixed(2)
                }
            };
            $('#services_json').val(JSON.stringify(finalJson));
        }
    });
</script>
@endsection
