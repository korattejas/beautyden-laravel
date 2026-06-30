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
        @include('admin.layouts.crud-header', [
            'title' => 'Edit Appointment',
            'subtitle' => 'Update booking details, services and pricing',
            'items' => [
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Appointments', 'url' => route('admin.appointments.index')],
                ['label' => 'Edit Appointment'],
            ],
        ])

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

                                    <div class="row g-3">
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
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <input type="text" id="customName" class="form-control flex-grow-1" placeholder="Service Name" style="min-width:200px;">
                                                    <input type="number" id="customPrice" class="form-control" placeholder="Price" style="max-width:140px;">
                                                    <button type="button" id="addCustomBtn" class="btn btn-primary">Add</button>
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

                                        <div class="col-12 pa-form-actions">
                                            <button type="submit" class="pa-btn pa-btn-primary">
                                                <i class="bi bi-check2"></i> Update Appointment
                                            </button>
                                            <a href="{{ route('admin.appointments.index') }}" class="pa-btn pa-btn-outline">Cancel</a>
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
    var form_url = 'appointments/store';
    var redirect_url = 'appointments';
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
            $.get('/admin/get-city-services/' + cityId, function(response) {
                let html = '';
                $.each(response, function(catId, cat) {
                    html += `<div style="margin-bottom:14px;border:1px solid #ddd;border-radius:12px;overflow:hidden;">
                    <div class="cat-toggle" data-id="cat${catId}" style="padding:14px;background:#f4f4f4;cursor:pointer;font-weight:600;">${cat.name}</div>
                    <div id="cat${catId}" style="display:none;padding:14px;">`;

                    if (cat.services) {
                        html += `<div class="row">`;
                        $.each(cat.services, function(i, s) {
                            html += serviceCard(s);
                        });
                        html += `</div>`;
                    }

                    if (cat.subcategories) {
                        $.each(cat.subcategories, function(subId, sub) {
                            html += `<div style="margin-top:10px;border:1px solid #eee;border-radius:10px;">
                            <div class="sub-toggle" data-id="sub${subId}" style="padding:12px;background:#fafafa;cursor:pointer;font-weight:500;">${sub.name}</div>
                            <div id="sub${subId}" style="display:none;padding:12px;"><div class="row">`;
                            $.each(sub.services, function(i, s) {
                                html += serviceCard(s);
                            });
                            html += `</div></div></div>`;
                        });
                    }
                    html += `</div></div>`;
                });
                $('#dynamicServices').html(html);

                if (isFirstLoad && savedData) fillFormFromJSON();
                else calculateTotal();
            });
        }

        function serviceCard(s) {
            return `<div class="col-md-6 mb-3">
            <div class="service-card" style="border:1px solid #e5e5e5;padding:18px;border-radius:14px;background:#fff;transition:0.25s;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
            <label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
                <input type="checkbox" class="service-check" data-name="${s.name}"> ${s.name}
            </label>
            <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:12px;color:#888;">Price</div>
                    <input type="number" value="${s.price}" class="price" style="width:90px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#7367f0;background:#f8f7ff;" disabled>
                </div>
                <div>
                    <div style="font-size:12px;color:#888;">Qty</div>
                    <div style="display:flex;align-items:center;border:1px solid #ddd;border-radius:8px;overflow:hidden;width:110px;background:#fff;">
                        <button type="button" class="qty-minus" style="width:35px;border:none;background:#f4f4f4;font-size:18px;" disabled>−</button>
                        <input type="text" value="1" class="qty" style="width:40px;border:none;text-align:center;font-weight:600;" readonly>
                        <button type="button" class="qty-plus" style="width:35px;border:none;background:#f4f4f4;font-size:18px;" disabled>+</button>
                    </div>
                </div>
            </div>
        </div></div>`;
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
                        $('.service-check').each(function() {
                            if ($(this).data('name') === item.name) {
                                let card = $(this).closest('.service-card');
                                $(this).prop('checked', true);
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
            card.find('.price, .qty-plus, .qty-minus').prop('disabled', !chk);
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
                <div style="display:flex;justify-content:space-between;"><strong>${name}</strong><button type="button" class="btn btn-sm text-danger remove-custom">×</button></div>
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
        $(document).on('keyup change', '.live-json, #travelCharges, #discountPercent, .price, .custom-price', function() {
            calculateTotal();
        });

        function calculateTotal() {
            let servicesArray = [],
                subtotal = 0,
                invoiceHtml = '';

            $('.service-check:checked').each(function() {
                let card = $(this).closest('.service-card');
                let n = $(this).data('name'),
                    p = parseFloat(card.find('.price').val()) || 0,
                    q = parseInt(card.find('.qty').val()) || 1;
                let tot = p * q;
                subtotal += tot;
                servicesArray.push({
                    type: "service",
                    name: n,
                    price: p,
                    qty: q,
                    total: tot
                });
                invoiceHtml += `<div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>${n} (${q}x)</span><span>₹${tot.toFixed(2)}</span></div>`;
            });

            $('.custom-item').each(function() {
                let n = $(this).find('strong').text(),
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
            let grand = (subtotal + travel) - discA;

            $('#invoiceList').html(invoiceHtml);
            $('#subTotal').text(subtotal.toFixed(2));
            $('#discountAmount').text(discA.toFixed(2));
            $('#discountRow').toggle(discP > 0);
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
                    grand_total: grand.toFixed(2)
                }
            };
            $('#services_json').val(JSON.stringify(finalJson));
        }
    });
</script>
@endsection