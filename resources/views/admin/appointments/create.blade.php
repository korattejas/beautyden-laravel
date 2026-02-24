@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Add Appointment</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.appointments.index') }}">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Add Appointment</li>
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
                                <form method="POST" data-parsley-validate id="addEditForm" role="form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">
                                    <input type="hidden" name="services_json" id="services_json">
                                    <input type="hidden" name="travel_charges" id="hidden_travel">
                                    <input type="hidden" name="discount_percent" id="hidden_discount">
                                    <input type="hidden" name="discount_amount" id="hidden_discount_amount">
                                    <input type="hidden" name="sub_total" id="hidden_subtotal">
                                    <input type="hidden" name="grand_total" id="hidden_grandtotal">
                                    <div class="row">

                                        <!-- First Name -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" name="first_name"
                                                    placeholder="First Name" required>
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" name="last_name"
                                                    placeholder="Last Name">
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Email">
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="number" class="form-control" name="phone"
                                                    placeholder="Phone">
                                            </div>
                                        </div>

                                        <!-- Quantity -->
                                        <!-- <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" class="form-control" name="quantity"
                                                    min="1" value="1">
                                            </div>
                                        </div> -->

                                        <!-- Price -->
                                        <!-- <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" step="0.01" class="form-control" name="price"
                                                    placeholder="0.00">
                                            </div>
                                        </div> -->

                                        <!-- Discount Price -->
                                        <!-- <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Discount Price</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="discount_price" placeholder="0.00">
                                            </div>
                                        </div> -->


                                        <!-- Appointment Date -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Appointment Date</label>
                                                <input type="date" class="form-control" name="appointment_date">
                                            </div>
                                        </div>

                                        <!-- Appointment Time -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Appointment Time</label>
                                                <input type="time" class="form-control" name="appointment_time">
                                            </div>
                                        </div>

                                        <!-- Service Address -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Service Address</label>
                                                <textarea class="form-control" name="service_address" rows="3" placeholder="Full Address"></textarea>
                                            </div>
                                        </div>

                                        <!-- City Dropdown -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label for="city_id">City</label>
                                                <select name="city_id" id="city_id" class="form-control select2">
                                                    <option value="">Select City</option>
                                                    @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- LEFT SERVICES -->
                                        <div class="col-lg-8 col-12 mt-4">
                                            <div id="dynamicServices"></div>
                                        </div>


                                        <!-- RIGHT SUMMARY PANEL -->
                                        <div class="col-lg-4 col-12 mt-4">

                                            <div style="position:sticky;top:20px;border:1px solid #e5e5e5;border-radius:16px;padding:20px;background:#ffffff;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                                                <h5 style="font-weight:600;margin-bottom:18px;">
                                                    Service Summary
                                                </h5>

                                                <div id="invoiceList" style="max-height:250px;overflow:auto;"></div>

                                                <hr>

                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Subtotal</span>
                                                    <span>₹ <span id="subTotal">0.00</span></span>
                                                </div>

                                                <!-- Traveling Charges -->
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Travelling Charges</span>
                                                    <input type="number" id="travelCharges"
                                                        value="0"
                                                        style="width:80px;text-align:center;border:1px solid #ddd;border-radius:6px;">
                                                </div>

                                                <!-- Discount Input -->
                                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                                    <span>Discount (%)</span>
                                                    <input type="number" id="discountPercent"
                                                        value="0" min="0" max="100"
                                                        style="width:80px;text-align:center;border:1px solid #ddd;border-radius:6px;">
                                                </div>

                                                <!-- Discount Row (Hide if 0) -->
                                                <div id="discountRow"
                                                    style="display:none;justify-content:space-between;margin-bottom:8px;color:#ea5455;">
                                                    <span>Discount</span>
                                                    <span>- ₹ <span id="discountAmount">0.00</span></span>
                                                </div>

                                                <hr>

                                                <div style="display:flex;justify-content:space-between;font-size:20px;font-weight:700;">
                                                    <span>Total</span>
                                                    <span style="color:#28c76f;">
                                                        ₹ <span id="grandTotal">0.00</span>
                                                    </span>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- CUSTOM SERVICE TOGGLE -->
                                        <div class="col-12 mt-4">

                                            <div style="margin-bottom:10px;">
                                                <label style="font-weight:600;">
                                                    <input type="checkbox" id="customToggle">
                                                    Add Custom Service
                                                </label>
                                            </div>

                                            <div id="customSection" style="display:none;border:1px solid #ddd;padding:15px;border-radius:12px;">

                                                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                                    <input type="text" id="customName"
                                                        placeholder="Service Name"
                                                        style="flex:2;min-width:200px;border:1px solid #ddd;border-radius:6px;padding:6px;">

                                                    <input type="number" id="customPrice"
                                                        placeholder="Price"
                                                        style="flex:1;min-width:120px;border:1px solid #ddd;border-radius:6px;padding:6px;">

                                                    <button type="button" id="addCustomBtn"
                                                        style="background:#7367f0;color:#fff;border:none;border-radius:6px;padding:6px 15px;">
                                                        Add
                                                    </button>
                                                </div>

                                                <div id="customList" class="row mt-3"></div>

                                            </div>
                                        </div>

                                        <!-- Special Notes -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Special Notes</label>
                                                <textarea class="form-control" name="special_notes" rows="3" placeholder="Special instructions"></textarea>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" selected>Pending</option>
                                                    <option value="2">Assigned</option>
                                                    <option value="3">Completed</option>
                                                    <option value="4">Rejected</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.appointments.index') }}"
                                                class="btn btn-secondary">Cancel</a>
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

<div id="invoicePrintArea" style="display:none;padding:30px;font-family:Arial;">

    <h2 style="text-align:center;color:#7367f0;">Service Invoice</h2>

    <div style="margin-top:20px;">
        <strong>Client:</strong> <span id="printClientName"></span><br>
        <strong>Date:</strong> <span id="printDate"></span>
    </div>

    <hr>

    <div id="printServices"></div>

    <hr>

    <div style="text-align:right;">
        <div>Subtotal: ₹ <span id="printSubTotal"></span></div>
        <div id="printTravelRow">Travelling: ₹ <span id="printTravel"></span></div>
        <div id="printDiscountRow">Discount: - ₹ <span id="printDiscount"></span></div>
        <h3>Total: ₹ <span id="printGrandTotal"></span></h3>
    </div>

    <p style="text-align:center;margin-top:30px;font-size:12px;color:#888;">
        Thank you for choosing our service ❤️
    </p>

</div>

@section('footer_script_content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>


<script>
    var form_url = 'appointments/store';
    var redirect_url = 'appointments';

    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%'
    });

    $('#category_id').on('change', function() {
        var categoryId = $(this).val();

        $('#sub_category_id').empty().append('<option value="">Select Sub Category</option>');

        if (categoryId) {
            $.ajax({
                url: 'get-appoinmentSubcategories/' + categoryId,
                type: 'GET',
                success: function(data) {
                    $.each(data, function(key, subCategory) {
                        $('#sub_category_id').append('<option value="' + subCategory.id +
                            '">' + subCategory.name + '</option>');
                    });

                    $('#sub_category_id').trigger('change');
                }
            });
        }
    });
    $(document).ready(function() {

        /* =====================================
           CITY LOAD SERVICES
        ===================================== */

        $('#city_id').on('change', function() {

            let cityId = $(this).val();
            $('#dynamicServices').html('');
            resetTotals();

            if (!cityId) return;

            $.get('/admin/get-city-services/' + cityId, function(response) {

                let html = '';

                $.each(response, function(categoryId, category) {

                    html += `
                <div style="margin-bottom:14px;border:1px solid #ddd;border-radius:12px;">
                    <div class="cat-toggle"
                         data-id="cat${categoryId}"
                         style="padding:14px;background:#f4f4f4;cursor:pointer;font-weight:600;">
                         ${category.name}
                    </div>

                    <div id="cat${categoryId}" style="display:none;padding:14px;">
                `;

                    if (category.services && category.services.length > 0) {
                        html += `<div class="row">`;
                        $.each(category.services, function(i, service) {
                            html += serviceCard(service);
                        });
                        html += `</div>`;
                    }

                    if (category.subcategories) {
                        $.each(category.subcategories, function(subId, subCategory) {

                            html += `
                        <div style="margin-top:10px;border:1px solid #eee;border-radius:10px;">
                            <div class="sub-toggle"
                                 data-id="sub${subId}"
                                 style="padding:12px;background:#fafafa;cursor:pointer;font-weight:500;">
                                 ${subCategory.name}
                            </div>

                            <div id="sub${subId}" style="display:none;padding:12px;">
                                <div class="row">
                        `;

                            $.each(subCategory.services, function(i, service) {
                                html += serviceCard(service);
                            });

                            html += `</div></div></div>`;
                        });
                    }

                    html += `</div></div>`;
                });

                $('#dynamicServices').html(html);
            });
        });


        /* =====================================
           ACCORDION
        ===================================== */

        $(document).on('click', '.cat-toggle', function() {
            let id = $(this).data('id');
            $('[id^=cat]').not('#' + id).slideUp();
            $('#' + id).slideToggle();
        });

        $(document).on('click', '.sub-toggle', function() {
            let id = $(this).data('id');
            $('#' + id).slideToggle();
        });


        /* =====================================
           SERVICE CARD HTML
        ===================================== */

        function serviceCard(service) {
            return `
        <div class="col-md-6 mb-3">
            <div class="service-card"
                style="border:1px solid #e5e5e5;padding:18px;border-radius:14px;background:#fff;transition:0.25s;box-shadow:0 2px 6px rgba(0,0,0,0.05);">

                <label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
                    <input type="checkbox" class="service-check">
                    ${service.name}
                </label>

                <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;">

                    <div>
                        <div style="font-size:12px;color:#888;">Price</div>
                        <input type="number"
                               value="${service.price}"
                               class="price"
                               style="width:90px;border:1px solid #ddd;border-radius:8px;padding:6px;font-weight:600;color:#7367f0;background:#f8f7ff;"
                               disabled>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#888;">Qty</div>
                        <div style="display:flex;align-items:center;border:1px solid #ddd;border-radius:8px;overflow:hidden;width:110px;background:#fff;">

                            <button type="button"
                                    class="qty-minus"
                                    style="width:35px;border:none;background:#f4f4f4;font-size:18px;"
                                    disabled>−</button>

                            <input type="text"
                                   value="1"
                                   class="qty"
                                   style="width:40px;border:none;text-align:center;font-weight:600;"
                                   readonly>

                            <button type="button"
                                    class="qty-plus"
                                    style="width:35px;border:none;background:#f4f4f4;font-size:18px;"
                                    disabled>+</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        `;
        }


        /* =====================================
           ENABLE SERVICE
        ===================================== */

        $(document).on('change', '.service-check', function() {

            let card = $(this).closest('.service-card');
            let enabled = $(this).is(':checked');

            if (enabled) {
                card.css({
                    border: '2px solid #7367f0',
                    background: '#f8f7ff',
                    boxShadow: '0 8px 18px rgba(115,103,240,0.15)'
                });
            } else {
                card.css({
                    border: '1px solid #e5e5e5',
                    background: '#fff',
                    boxShadow: '0 2px 6px rgba(0,0,0,0.05)'
                });
            }

            card.find('.price, .qty-plus, .qty-minus')
                .prop('disabled', !enabled);

            calculateTotal();
        });


        /* =====================================
           QTY BUTTONS
        ===================================== */

        $(document).on('click', '.qty-plus', function() {
            let card = $(this).closest('.service-card');
            let input = card.find('.qty');
            let value = parseInt(input.val()) || 1;
            input.val(value + 1);
            calculateTotal();
        });

        $(document).on('click', '.qty-minus', function() {
            let card = $(this).closest('.service-card');
            let input = card.find('.qty');
            let value = parseInt(input.val()) || 1;
            if (value > 1) {
                input.val(value - 1);
                calculateTotal();
            }
        });

        $(document).on('keyup change', '.price', function() {
            calculateTotal();
        });


        /* =====================================
           CUSTOM SERVICE TOGGLE
        ===================================== */

        $('#customToggle').on('change', function() {
            $('#customSection').toggle($(this).is(':checked'));
        });


        /* =====================================
           ADD CUSTOM SERVICE
        ===================================== */

        $('#addCustomBtn').on('click', function() {

            let name = $('#customName').val();
            let price = $('#customPrice').val();

            if (!name || !price) return;

            let html = `
        <div class="custom-item" style="margin-top:10px;border:1px dashed #7367f0;padding:12px;border-radius:10px;">
            <div style="font-weight:600;margin-bottom:6px;">${name}</div>

            <div style="display:flex;gap:10px;align-items:center;">
                <input type="number" value="${price}" class="custom-price"
                       style="width:90px;border:1px solid #ddd;border-radius:6px;padding:6px;">

                <div style="display:flex;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
                    <button type="button" class="custom-minus"
                            style="width:35px;border:none;background:#eee;">−</button>
                    <input type="text" value="1" class="custom-qty"
                           style="width:40px;border:none;text-align:center;" readonly>
                    <button type="button" class="custom-plus"
                            style="width:35px;border:none;background:#eee;">+</button>
                </div>
            </div>
        </div>
        `;

            $('#customList').append(html);
            $('#customName').val('');
            $('#customPrice').val('');

            calculateTotal();
        });

        $(document).on('click', '.custom-plus', function() {
            let wrapper = $(this).closest('.custom-item');
            let input = wrapper.find('.custom-qty');
            let value = parseInt(input.val()) || 1;
            input.val(value + 1);
            calculateTotal();
        });

        $(document).on('click', '.custom-minus', function() {
            let wrapper = $(this).closest('.custom-item');
            let input = wrapper.find('.custom-qty');
            let value = parseInt(input.val()) || 1;
            if (value > 1) {
                input.val(value - 1);
                calculateTotal();
            }
        });

        $(document).on('keyup change', '.custom-price', function() {
            calculateTotal();
        });


        /* =====================================
           TRAVEL + DISCOUNT
        ===================================== */

        $(document).on('keyup change', '#discountPercent, #travelCharges', function() {
            calculateTotal();
        });


        /* =====================================
           TOTAL CALCULATION
        ===================================== */

        function calculateTotal() {

            let servicesArray = [];
            let subtotal = 0;
            let invoiceHtml = '';

            $('.service-check:checked').each(function() {

                let card = $(this).closest('.service-card');
                let name = card.find('label').text().trim();
                let price = parseFloat(card.find('.price').val()) || 0;
                let qty = parseInt(card.find('.qty').val()) || 1;

                let total = price * qty;
                subtotal += total;

                servicesArray.push({
                    type: "service",
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

                let name = $(this).find('div:first').text().trim();
                let price = parseFloat($(this).find('.custom-price').val()) || 0;
                let qty = parseInt($(this).find('.custom-qty').val()) || 1;

                let total = price * qty;
                subtotal += total;

                servicesArray.push({
                    type: "custom",
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

            let travel = parseFloat($('#travelCharges').val()) || 0;
            subtotal += travel;

            if (travel > 0) {
                invoiceHtml += `
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <div>Travelling Charges</div>
                    <div>₹${travel.toFixed(2)}</div>
                </div>
            `;
            }

            let discountPercent = parseFloat($('#discountPercent').val()) || 0;
            let discountAmount = subtotal * discountPercent / 100;
            let grandTotal = subtotal - discountAmount;

            $('#invoiceList').html(invoiceHtml);
            $('#subTotal').text(subtotal.toFixed(2));
            $('#discountAmount').text(discountAmount.toFixed(2));
            $('#grandTotal').text(grandTotal.toFixed(2));

            if (discountPercent > 0) {
                $('#discountRow').css('display', 'flex');
            } else {
                $('#discountRow').hide();
            }

            // ===============================
            // STORE HIDDEN VALUES
            // ===============================
            $('#hidden_travel').val(travel);
            $('#hidden_discount').val(discountPercent);
            $('#hidden_discount_amount').val(discountAmount.toFixed(2));
            $('#hidden_subtotal').val(subtotal.toFixed(2));
            $('#hidden_grandtotal').val(grandTotal.toFixed(2));

            // ===============================
            // FINAL JSON STRUCTURE
            // ===============================
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
                    discount_percent: discountPercent,
                    discount_amount: discountAmount.toFixed(2),
                    grand_total: grandTotal.toFixed(2)
                }
            };

            $('#services_json').val(JSON.stringify(finalJson));
        }

        function resetTotals() {
            $('#subTotal').text('0.00');
            $('#discountAmount').text('0.00');
            $('#grandTotal').text('0.00');
            $('#discountPercent').val(0);
            $('#travelCharges').val(0);
            $('#invoiceList').html('');
        }

        $('#downloadInvoice').on('click', function() {

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // =============================
            // COMPANY DETAILS
            // =============================

            doc.setFontSize(18);
            doc.setTextColor(115, 103, 240);
            doc.text("YOUR COMPANY NAME", 14, 20);

            doc.setFontSize(10);
            doc.setTextColor(0);
            doc.text("Address Line 1, City", 14, 26);
            doc.text("Phone: +91 9999999999", 14, 31);
            doc.text("Email: info@company.com", 14, 36);

            doc.setDrawColor(200);
            doc.line(14, 40, 196, 40);

            // =============================
            // INVOICE TITLE
            // =============================

            doc.setFontSize(16);
            doc.setTextColor(0);
            doc.text("SERVICE INVOICE", 150, 25);

            doc.setFontSize(10);
            doc.text("Invoice Date: " + new Date().toLocaleDateString(), 150, 32);
            doc.text("Invoice No: INV-" + Math.floor(Math.random() * 10000), 150, 38);

            // =============================
            // CLIENT DETAILS
            // =============================

            let firstName = $('input[name="first_name"]').val() || '';
            let lastName = $('input[name="last_name"]').val() || '';
            let email = $('input[name="email"]').val() || '';
            let phone = $('input[name="phone"]').val() || '';
            let appointmentDate = $('input[name="appointment_date"]').val() || '';
            let appointmentTime = $('input[name="appointment_time"]').val() || '';
            let address = $('textarea[name="service_address"]').val() || '';
            let notes = $('textarea[name="special_notes"]').val() || '';

            doc.setFontSize(11);
            doc.text("Bill To:", 14, 50);

            doc.setFontSize(10);
            doc.text(firstName + " " + lastName, 14, 56);
            doc.text("Phone: " + phone, 14, 61);
            doc.text("Email: " + email, 14, 66);
            doc.text("Appointment: " + appointmentDate + " " + appointmentTime, 14, 71);

            let splitAddress = doc.splitTextToSize("Address: " + address, 90);
            doc.text(splitAddress, 14, 76);

            let startY = 90;

            // =============================
            // SERVICE TABLE
            // =============================

            let tableData = [];
            let subtotal = 0;

            $('.service-check:checked').each(function() {

                let card = $(this).closest('.service-card');
                let name = card.find('label').text().trim();
                let price = parseFloat(card.find('.price').val()) || 0;
                let qty = parseInt(card.find('.qty').val()) || 1;
                let total = price * qty;

                subtotal += total;

                tableData.push([
                    name,
                    qty,
                    "₹ " + price.toFixed(2),
                    "₹ " + total.toFixed(2)
                ]);
            });

            $('.custom-item').each(function() {

                let name = $(this).find('div:first').text().trim();
                let price = parseFloat($(this).find('.custom-price').val()) || 0;
                let qty = parseInt($(this).find('.custom-qty').val()) || 1;
                let total = price * qty;

                subtotal += total;

                tableData.push([
                    name,
                    qty,
                    "₹ " + price.toFixed(2),
                    "₹ " + total.toFixed(2)
                ]);
            });

            doc.autoTable({
                startY: startY,
                head: [
                    ['Service', 'Qty', 'Price', 'Total']
                ],
                body: tableData,
                theme: 'grid',
                headStyles: {
                    fillColor: [115, 103, 240]
                },
                styles: {
                    fontSize: 10
                }
            });

            let finalY = doc.lastAutoTable.finalY + 10;

            // =============================
            // TOTAL CALCULATION
            // =============================

            let travel = parseFloat($('#travelCharges').val()) || 0;
            let discountPercent = parseFloat($('#discountPercent').val()) || 0;

            subtotal += travel;
            let discountAmount = subtotal * discountPercent / 100;
            let grandTotal = subtotal - discountAmount;

            doc.setFontSize(11);

            doc.text("Subtotal:", 140, finalY);
            doc.text("₹ " + subtotal.toFixed(2), 170, finalY);

            finalY += 6;

            if (travel > 0) {
                doc.text("Travelling:", 140, finalY);
                doc.text("₹ " + travel.toFixed(2), 170, finalY);
                finalY += 6;
            }

            if (discountPercent > 0) {
                doc.setTextColor(234, 84, 85);
                doc.text("Discount (" + discountPercent + "%):", 140, finalY);
                doc.text("- ₹ " + discountAmount.toFixed(2), 170, finalY);
                doc.setTextColor(0);
                finalY += 6;
            }

            doc.setFontSize(14);
            doc.setTextColor(40, 199, 111);
            doc.text("Grand Total:", 140, finalY);
            doc.text("₹ " + grandTotal.toFixed(2), 170, finalY);

            finalY += 15;

            if (notes) {
                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text("Notes: " + notes, 14, finalY);
            }

            doc.save("Service-Invoice.pdf");

        });


    });
</script>
@endsection