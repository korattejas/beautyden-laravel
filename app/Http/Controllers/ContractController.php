<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractSigned;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    protected $error_message;
    protected $exception_error_code;
    protected $validator_error_code;
    protected $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message', 'Something went wrong!');
        $this->exception_error_code = config('custom.exception_error_code', 500);
        $this->validator_error_code = config('custom.validator_error_code', 422);
        $this->controller_name = "ContractController";
    }

    public function showAgreements()
    {
        try {
            return view('contracts.agreements');
        } catch (\Exception $e) {
            Log::error("{$this->controller_name} - showAgreements: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function verifyProvider(Request $request)
    {
        $function_name = 'verifyProvider';
        try {
            $request->validate([
                'provider_name' => 'required|string|max:255',
                'provider_mobile' => 'required|string|max:15',
                'provider_address' => 'nullable|string|max:500'
            ]);

            session([
                'provider_name' => $request->provider_name,
                'provider_mobile' => $request->provider_mobile,
                'provider_address' => $request->provider_address,
            ]);

            return redirect()->route('contracts.sign');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], $this->validator_error_code);
        } catch (\Exception $e) {
            Log::error("{$this->controller_name} - {$function_name}: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function saveSignature(Request $request)
    {
        $function_name = 'saveSignature';
        try {
            $request->validate([
                'signature' => 'required|string',
            ]);

            $provider = session()->all();

            $signatureFolder = public_path('contracts/signatures/');
            if (!file_exists($signatureFolder)) {
                mkdir($signatureFolder, 0777, true);
            }
            $signatureFileName = time() . '.png';
            $signaturePath = $signatureFolder . $signatureFileName;
            file_put_contents(
                $signaturePath,
                base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->signature))
            );

            $pdfFolder = public_path('contracts/pdf/');
            if (!file_exists($pdfFolder)) {
                mkdir($pdfFolder, 0777, true);
            }
            $pdfFileName = time() . '.pdf';
            $pdfPath = $pdfFolder . $pdfFileName;

            $pdf = Pdf::loadView('contracts.pdf', [
                'provider' => $provider,
                'contract_type' => $request->contract_type,
                'signature_path' => 'contracts/signatures/' . $signatureFileName,
            ]);
            $pdf->save($pdfPath);

            ContractSigned::create([
                'provider_id' => null,
                'provider_name' => $provider['provider_name'] ?? null,
                'provider_mobile' => $provider['provider_mobile'] ?? null,
                'provider_address' => $provider['provider_address'] ?? null,
                'contract_type' => $request->contract_type ?? null,
                'signed_pdf' => 'contracts/pdf/' . $pdfFileName,
                'signature_image' => 'contracts/signatures/' . $signatureFileName,
                'ip_address' => $request->ip(),
                'signed_at' => now(),
                'status' => 1,
            ]);

            return redirect()->route('contracts.success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], $this->validator_error_code);
        } catch (\Exception $e) {
            Log::error("{$this->controller_name} - {$function_name}: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function success()
    {
        try {
            return view('contracts.success');
        } catch (\Exception $e) {
            Log::error("{$this->controller_name} - success: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
