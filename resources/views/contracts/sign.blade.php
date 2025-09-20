<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Step 2: Sign Agreement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        p {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .canvas-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }

        #signature-pad {
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            height: 200px;
        }

        .btn {
            display: inline-block;
            background: #6c63ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #574fd6;
        }

        form {
            margin-top: 20px;
        }

        select {
            width: 100%;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Step 2: Sign Agreement</h2>
        <p>Please sign below to complete your agreement:</p>

        <div class="canvas-wrapper">
            <canvas id="signature-pad"></canvas>
            <br>
            <button type="button" id="clear" class="btn" style="background:#ff5c5c;">Clear</button>
        </div>

        <form method="POST" action="{{ route('contracts.save') }}" onsubmit="return prepareSignature()">
            @csrf
            <input type="hidden" name="signature" id="signature">
            {{-- <select name="contract_type" required>
                <option value="NDA">NDA</option>
                <option value="Service Agreement">Service Agreement</option>
            </select> --}}
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
    <script>
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        document.getElementById('clear').addEventListener('click', () => {
            signaturePad.clear();
        });

        function prepareSignature() {
            if (signaturePad.isEmpty()) {
                alert('Please provide a signature before submitting.');
                return false;
            }
            document.getElementById('signature').value = signaturePad.toDataURL();
            return true;
        }
    </script>

</body>

</html>
