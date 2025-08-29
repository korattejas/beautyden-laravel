<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ImageUploadHelper;
use App\Models\FlashScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;



class FlashScreenController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/FlashScreenController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.flashScreen.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.flashScreen.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $flashScreen = FlashScreen::where('id', decryptId($id))->first();
            if ($flashScreen) {
                return view('admin.flashScreen.edit', [
                    'flashScreen' => $flashScreen
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataFlashScreen(Request $request)
    {
        $function_name = 'getDataFlashScreen';
        try {
            if ($request->ajax()) {
                $FlashScreen = DB::table('flash_screens_images')->select('flash_screens_images.*');
                return DataTables::of($FlashScreen)
                    ->addColumn('status', function ($FlashScreen) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $FlashScreen->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_main', function ($FlashScreen) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($FlashScreen) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.flash-screen.edit', encryptId($FlashScreen->id)),
                            'delete_id' => $FlashScreen->id,
                            'current_status' => $FlashScreen->status,
                            'hidden_id' => $FlashScreen->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('image', function ($FlashScreen) {
                        $baseAWSImageUrl = Storage::disk('s3')->url('');
                        $imageUrl = $baseAWSImageUrl . $FlashScreen->image;
                        return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Flash Screen Image" />';

                    })
                    ->rawColumns(['action', 'image', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'title' => [
                    'required',
                    $id == 0 ? 'unique:flash_screens_images,title' : 'unique:flash_screens_images,title,' . $id . ',id',
                ],
                'image' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg' : 'image|mimes:jpeg,png,jpg,gif,svg',
            ];

            $validateMessage = [
                'title.required' => 'The title title is required.',
                'title.unique' => 'The title title has already been taken.',
                'image.required' => 'The image is required.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('image')) {
                    $image = ImageUploadHelper::uploadFlashScreenImageS3($request->image);
                    $imageSizeInBytes = $request->file('image')->getSize();

                    if (isset($imageSizeInBytes)) {
                        if ($imageSizeInBytes >= 1024 * 1024) {
                            $imageSize = round($imageSizeInBytes / (1024 * 1024), 2);
                            $imageSizeFormatted = $imageSize . ' MB';
                        } else {
                            $imageSize = round($imageSizeInBytes / 1024, 2);
                            $imageSizeFormatted = $imageSize . ' KB';
                        }
                    }
                }

                FlashScreen::create([
                    'title' => $request->title,
                    'image_size' => $imageSizeFormatted,
                    'description' => $request->description,
                    'image' => $image,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.flash_screen_added_successfully')
                ]);
            } else {

                $flashScreen = FlashScreen::where('id', $id)->first();

                if ($request->hasFile('image')) {

                    $filePath = $flashScreen->image;
                    if (Storage::disk('s3')->exists($filePath)) {
                        Storage::disk('s3')->delete($filePath);
                    }

                    $image = ImageUploadHelper::uploadFlashScreenImageS3($request->image);
                    $imageSizeInBytes = $request->file('image')->getSize();
                    if (isset($imageSizeInBytes)) {
                        if ($imageSizeInBytes >= 1024 * 1024) {
                            $imageSize = round($imageSizeInBytes / (1024 * 1024), 2);
                            $imageSizeFormatted = $imageSize . ' MB';
                        } else {
                            $imageSize = round($imageSizeInBytes / 1024, 2);
                            $imageSizeFormatted = $imageSize . ' KB';
                        }
                    }
                } else {
                    $image = $flashScreen->image;
                    $imageSizeFormatted = $flashScreen->image_size;
                }

                FlashScreen::where('id', $id)->update([
                    'title' => $request->title,
                    'image_size' => $imageSizeFormatted,
                    'description' => $request->description,
                    'image' => $image,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.flash_screen_updated_successfully')
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }

    }

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            FlashScreen::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';
        try {
            $FlashScreen = FlashScreen::where('id', $id)->first();
            if ($FlashScreen) {
                $filePath = $FlashScreen->image;
                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }

                $FlashScreen->delete();

                return response()->json([
                    'message' => trans('admin_string.flash_screen_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the image from S3 or no image found.");
                return response()->json(['error' => 'Failed to delete the image from S3 or no image found.'], 500);
            }

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


}
