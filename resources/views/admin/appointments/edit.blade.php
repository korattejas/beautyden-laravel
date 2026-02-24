@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Edit Appointment</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.appointments.index') }}">Appointments</a></li>
                        <li class="breadcrumb-item active">Edit Appointment</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $appointment->id }}">
                                    <input type="hidden" name="services_json" id="services_json">

                                    <input type="hidden" name="travel_charges" id="hidden_travel">
                                    <input type="hidden" name="discount_percent" id="hidden_discount">
                                    <input type="hidden" name="discount_amount" id="hidden_discount_amount">
                                    <input type="hidden" name="sub_total" id="hidden_subtotal">
                                    <input type="hidden" name="grand_total" id="hidden_grandtotal">

                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" class="form-control live-json" name="first_name" value="{{ $appointment->first_name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control live-json" name="last_name" value="{{ $appointment->last_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control live-json" name="email" value="{{ $appointment->email }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="number" class="form-control live-json" name="phone" value="{{ $appointment->phone }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Appointment Date</label>
                                                <input type="date" class="form-control live-json" name="appointment_date" value="{{ $appointment->appointment_date }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Appointment Time</label>
                                                <input type="time" class="form-control live-json" name="appointment_time" value="{{ $appointment->appointment_time }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Service Address</label>
                                                <textarea class="form-control live-json" name="service_address" rows="2">{{ $appointment->service_address }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
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
                                            <div style="position:sticky;top:20px;border:1px solid #e5e5e5;border-radius:16px;padding:20px;background:#ffffff;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                                                <h5 style="font-weight:600;margin-bottom:18px;">Service Summary</h5>
                                                <div id="invoiceList" style="max-height:250px;overflow:auto;"></div>
                                                <hr>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Subtotal</span>
                                                    <span>₹ <span id="subTotal">0.00</span></span>
                                                </div>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Travelling Charges</span>
                                                    <input type="number" id="travelCharges" value="0" style="width:80px;text-align:center;border:1px solid #ddd;border-radius:6px;">
                                                </div>
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Discount (%)</span>
                                                    <input type="number" id="discountPercent" value="0" min="0" max="100" style="width:80px;text-align:center;border:1px solid #ddd;border-radius:6px;">
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
                                                <label style="font-weight:600;"><input type="checkbox" id="customToggle"> Add Custom Service</label>
                                            </div>
                                            <div id="customSection" style="display:none;border:1px solid #ddd;padding:15px;border-radius:12px;">
                                                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                                    <input type="text" id="customName" placeholder="Service Name" style="flex:2;min-width:200px;border:1px solid #ddd;border-radius:6px;padding:6px;">
                                                    <input type="number" id="customPrice" placeholder="Price" style="flex:1;min-width:120px;border:1px solid #ddd;border-radius:6px;padding:6px;">
                                                    <button type="button" id="addCustomBtn" style="background:#7367f0;color:#fff;border:none;border-radius:6px;padding:6px 15px;">Add</button>
                                                </div>
                                                <div id="customList" class="row mt-3"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <label>Special Notes</label>
                                            <textarea class="form-control live-json" name="special_notes" rows="2">{{ $appointment->special_notes }}</textarea>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1"
                                                        {{ isset($appointment) && $appointment->status == 1 ? 'selected' : '' }}>
                                                        Pending</option>
                                                    <option value="2"
                                                        {{ isset($appointment) && $appointment->status == 2 ? 'selected' : '' }}>
                                                        Assigned</option>
                                                    <option value="3"
                                                        {{ isset($appointment) && $appointment->status == 3 ? 'selected' : '' }}>
                                                        Completed</option>
                                                    <option value="4"
                                                        {{ isset($appointment) && $appointment->status == 4 ? 'selected' : '' }}>
                                                        Rejected</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3 text-end">
                                            <button type="submit" class="btn btn-primary">Update Appointment</button>
                                            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Cancel</a>
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
        let savedData = @json($appointment -> services_data);
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
                    <div id="cat${catId}" style="display:none;padding:14px;"><div class="row">`;

                    if (cat.services) $.each(cat.services, function(i, s) {
                        html += serviceCard(s);
                    });

                    if (cat.subcategories) {
                        $.each(cat.subcategories, function(subId, sub) {
                            html += `<div class="col-12 mt-2 border rounded p-2">
                            <div class="sub-toggle" data-id="sub${subId}" style="cursor:pointer;font-weight:500;">${sub.name}</div>
                            <div id="sub${subId}" style="display:none;" class="row mt-2">`;
                            $.each(sub.services, function(i, s) {
                                html += serviceCard(s);
                            });
                            html += `</div></div>`;
                        });
                    }
                    html += `</div></div></div>`;
                });
                $('#dynamicServices').html(html);

                if (isFirstLoad && savedData) fillFormFromJSON();
                else calculateTotal();
            });
        }

        function serviceCard(s) {
            return `<div class="col-md-6 mb-3"><div class="service-card" style="border:1px solid #e5e5e5;padding:18px;border-radius:14px;background:#fff;">
            <label style="font-weight:600;display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" class="service-check" data-name="${s.name}"> ${s.name}</label>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                <input type="number" value="${s.price}" class="price" style="width:80px;border:1px solid #ddd;border-radius:6px;" disabled>
                <div style="display:flex;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
                    <button type="button" class="qty-minus" style="width:30px;border:none;background:#f4f4f4;" disabled>−</button>
                    <input type="text" value="1" class="qty" style="width:35px;border:none;text-align:center;" readonly>
                    <button type="button" class="qty-plus" style="width:30px;border:none;background:#f4f4f4;" disabled>+</button>
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
                                    background: '#f8f7ff'
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
                background: '#f8f7ff'
            } : {
                border: '1px solid #e5e5e5',
                background: '#fff'
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