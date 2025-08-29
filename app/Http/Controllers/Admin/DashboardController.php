<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/CityController ";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $function_name = 'index';
        try {
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => config('custom.aws_region'),
                'credentials' => [
                    'key' => config('custom.aws_access_key_id'),
                    'secret' => config('custom.AWS_secret_access_key'),
                ],
            ]);

            $bucket = config('custom.aws_bucket');

            $result = $s3->listObjectsV2(['Bucket' => $bucket]);

            $totalSize = 0;

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $totalSize += $object['Size'];
                }
            }

            if ($totalSize >= (1024 * 1024 * 1024)) {
                $totalUsedSpace = round($totalSize / (1024 * 1024 * 1024), 2) . ' GB';
            } else {
                $totalUsedSpace = round($totalSize / (1024 * 1024), 2) . ' MB';
            }

            $awsPlanExpire = Setting::where([
                ['screen_name', '=', 'aws'],
                ['key', '=', 'aws_plan_expire'],
                ['status', '=', 1]
            ])->value('value');

            $awsTotalSpace = Setting::where([
                ['screen_name', '=', 'aws'],
                ['key', '=', 'aws_total_free_space_gb'],
                ['status', '=', 1]
            ])->value('value');

            $awsTotalSpaceConvertMB = $awsTotalSpace * 1000; 

            $totalAWSSpaceInMB = $awsTotalSpaceConvertMB * 1024 * 1024;

            $remainingSize = $totalAWSSpaceInMB - $totalSize;
            if ($remainingSize >= (1024 * 1024 * 1024)) {
                $remainingSpace = round($remainingSize / (1024 * 1024 * 1024), 2) . ' GB';
            } else {
                $remainingSpace = round($remainingSize / (1024 * 1024), 2) . ' MB';
            }

            return view('admin.dashboard.index', [
                'totalUsedSpace' => $totalUsedSpace,
                'remainingSpace' => $remainingSpace,
                'awsPlanExpire' => $awsPlanExpire,
                'awsTotalSpace' => $awsTotalSpace
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

}
