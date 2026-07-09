<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryLookbook;
use App\Models\ServiceCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class CategoryLookbookController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/CategoryLookbookController";
    }
    
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.category_lookbook.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $categories = ServiceCategory::where('status', 1)->get();
            return view('admin.category_lookbook.create', compact('categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $lookbook = CategoryLookbook::where('id', decryptId($id))->first();
            if ($lookbook) {
                $categories = ServiceCategory::where('status', 1)->get();
                return view('admin.category_lookbook.edit', [
                    'lookbook' => $lookbook,
                    'categories' => $categories
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = $request->all();

        try {
            $id = (int) $request->input('edit_value', 0);

            $validateArray = [
                'category_id' => [
                    'required',
                    $id == 0
                        ? 'unique:category_lookbooks,category_id'
                        : 'unique:category_lookbooks,category_id,' . $id . ',id',
                ],
                'photos'   => 'nullable|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'category_id.required'   => 'The category selection is required.',
                'category_id.unique'     => 'A lookbook for this category already exists.',
                'photos.*.image'  => 'Each file must be an image.',
                'photos.*.mimes'  => 'Images must be jpeg, png, jpg, gif, svg, webp.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], $this->validator_error_code);
            }

            $storedPhotos = [];
            $lookbook = null;

            if ($id !== 0) {
                $lookbook = CategoryLookbook::find($id);
                if ($lookbook && is_array($lookbook->photos)) {
                    $storedPhotos = $lookbook->photos;
                }
                if ($request->has('reordered_photos') && !empty($request->reordered_photos)) {
                    $reordered = json_decode($request->reordered_photos, true);
                    if (is_array($reordered)) {
                        // Keep only valid reordered photos that exist in the stored photos just in case
                        $validReordered = array_intersect($reordered, $storedPhotos);
                        // Add any photos that were in storedPhotos but missed from reordered to the end
                        $missingPhotos = array_diff($storedPhotos, $validReordered);
                        $storedPhotos = array_merge($validReordered, $missingPhotos);
                    }
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    // Using same helper method but will upload to lookbook
                    $filename = ImageUploadHelper::PortfolioImageUpload($photo); // It might upload to portfolio folder, we'll see if that's fine or if we need a custom one
                    $storedPhotos[] = $filename;
                }
            }

            $data = [
                'category_id'   => $request->category_id,
                'photos' => !empty($storedPhotos) ? $storedPhotos : null,
                'status' => (int) $request->input('status', 1),
            ];

            if ($id === 0) {
                CategoryLookbook::create($data);
                $msg = 'Category Lookbook added successfully';
            } else {
                $lookbook->update($data);
                $msg = 'Category Lookbook updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            logger()->error("Lookbook store error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->error_message
            ], $this->exception_error_code);
        }
    }



    public function getDataLookbook(Request $request)
    {
        $function_name = 'getDataLookbook';

        try {
            if ($request->ajax()) {

                $lookbooks = CategoryLookbook::with('category');

                return DataTables::of($lookbooks)
                    ->addColumn('category_name', function ($lookbook) {
                        return $lookbook->category ? $lookbook->category->name : '-';
                    })
                    ->addColumn('status', function ($lookbook) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $lookbook->status
                        ];

                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })

                    ->addColumn('action', function ($lookbook) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route'      => route('admin.category_lookbook.edit', encryptId($lookbook->id)),
                            'delete_id'       => $lookbook->id,
                            'current_status'  => $lookbook->status,
                            'hidden_id'       => $lookbook->id,
                        ];

                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })

                    ->addColumn('photos', function ($lookbook) {
                        if (empty($lookbook->photos)) return '<span class="text-muted">No Photos</span>';
                        
                        $html = '<div class="photo-stack">';
                        $limit = 4;
                        $count = 0;
                        $total = count($lookbook->photos);

                        foreach ($lookbook->photos as $img) {
                            if ($count >= $limit) break;
                            $url = asset('uploads/portfolio/' . $img);
                            $html .= '<img src="' . $url . '" class="photo-stack-item" title="Lookbook Image" />';
                            $count++;
                        }

                        if ($total > $limit) {
                            $html .= '<div class="photo-count-badge">+' . ($total - $limit) . '</div>';
                        }
                        
                        $html .= '</div>';
                        return $html;
                    })

                    ->rawColumns(['status', 'action', 'photos'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return response()->json([
                'error' => $this->error_message
            ], $this->exception_error_code);
        }
    }




    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            CategoryLookbook::where('id', $id)->update(['status' => $status]);
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
            $lookbook = CategoryLookbook::find($id);

            if (!$lookbook) {
                return response()->json([
                    'error' => 'Lookbook not found'
                ], 404);
            }

            if (!empty($lookbook->photos) && is_array($lookbook->photos)) {
                foreach ($lookbook->photos as $photo) {
                    $filePath = public_path('uploads/portfolio/' . $photo);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }

            $lookbook->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lookbook deleted successfully'
            ]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());

            return response()->json([
                'error' => $this->error_message
            ], $this->exception_error_code);
        }
    }
    
    public function removeImage(Request $request)
    {
        try {
            $id = $request->id;
            $imageName = $request->image;

            $lookbook = CategoryLookbook::find($id);
            if ($lookbook && is_array($lookbook->photos)) {
                $photos = $lookbook->photos;

                // Remove the image name from the array
                if (($key = array_search($imageName, $photos)) !== false) {
                    unset($photos[$key]);

                    // Reset array keys to avoid index issues
                    $photos = array_values($photos);

                    // Update the lookbook
                    $lookbook->update(['photos' => $photos]);

                    // Delete the physical file
                    $path = public_path('uploads/portfolio/' . $imageName);
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    return response()->json(['success' => true, 'message' => 'Image removed successfully']);
                }
            }
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
