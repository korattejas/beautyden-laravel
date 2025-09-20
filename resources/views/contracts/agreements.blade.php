<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Step 1: Agreements</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .agreement-block {
            margin-bottom: 30px;
        }

        iframe {
            width: 100%;
            height: 350px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 16px;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        form {
            display: none;
            margin-top: 20px;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        form textarea {
            resize: vertical;
            min-height: 80px;
        }

        form button {
            background: #6c63ff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        form button:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        form button:hover:enabled {
            background: #574fd6;
        }

        @media (max-width: 600px) {
            iframe {
                height: 250px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Step 1: Read Agreements</h2>

        <div class="agreement-block">
            <iframe src="{{ asset('agreement/nda.pdf') }}"></iframe>
            <label class="checkbox-label">
                <input type="checkbox" id="agree_nda"> I agree to NDA
            </label>
        </div>

        <div class="agreement-block">
            <iframe src="{{ asset('agreement/service_agreement.pdf') }}"></iframe>
            <label class="checkbox-label">
                <input type="checkbox" id="agree_service"> I agree to Provider Appointment & Service Agreement
            </label>
        </div>

        <form method="POST" action="{{ route('contracts.verify') }}" id="verifyForm">
            @csrf
            <input type="text" name="provider_name" placeholder="Full Name" required>
            <input type="number" name="provider_mobile" placeholder="Mobile No" required>
            <textarea name="provider_address" placeholder="Address" required></textarea>
            <button type="submit" id="continueBtn" disabled>Continue</button>
        </form>
    </div>

    <script>
        const ndaCheckbox = document.getElementById("agree_nda");
        const serviceCheckbox = document.getElementById("agree_service");
        const verifyForm = document.getElementById("verifyForm");
        const continueBtn = document.getElementById("continueBtn");

        function toggleForm() {
            const bothChecked = ndaCheckbox.checked && serviceCheckbox.checked;
            verifyForm.style.display = bothChecked ? "block" : "none";
            continueBtn.disabled = !bothChecked;
        }

        ndaCheckbox.addEventListener("change", toggleForm);
        serviceCheckbox.addEventListener("change", toggleForm);
    </script>

</body>

</html>
