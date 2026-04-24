<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/OfferController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.offers.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.offers.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $offer = Offer::where('id', decryptId($id))->first();
            if ($offer) {
                return view('admin.offers.edit', [
                    'offer' => $offer
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
                'title' => 'required|string|max:255',
                'position' => 'required|in:top_header,footer,other',
                'media_type' => 'required|in:image,video',
                'link' => 'nullable|string|max:255',
                'priority' => 'nullable|integer',
            ];

            if ($id == 0) {
                if ($request->media_type == 'image') {
                    $validateArray['media'] = 'required|array';
                    $validateArray['media.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
                } else {
                    $validateArray['media'] = 'required|mimetypes:video/mp4,video/quicktime,video/x-realaudio,video/x-msvideo,video/x-ms-wmv|max:20480';
                }
            } else {
                if ($request->hasFile('media')) {
                    if ($request->media_type == 'image') {
                        $validateArray['media'] = 'array';
                        $validateArray['media.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
                    } else {
                        $validateArray['media'] = 'mimetypes:video/mp4,video/quicktime,video/x-realaudio,video/x-msvideo,video/x-ms-wmv|max:20480';
                    }
                }
            }

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], $this->validator_error_code);
            }

            $storedMedia = [];
            $offer = null;

            if ($id !== 0) {
                $offer = Offer::find($id);
                if ($offer && is_array($offer->media)) {
                    $storedMedia = $offer->media;
                }
            }

            if ($request->hasFile('media')) {
                if ($request->media_type == 'image') {
                    foreach ($request->file('media') as $photo) {
                        $filename = ImageUploadHelper::OfferImageUpload($photo);
                        $storedMedia[] = $filename;
                    }
                } else {
                    // For video, we might want to replace the old video if it's single
                    if ($id !== 0 && $offer->media_type == 'video' && !empty($offer->media)) {
                        foreach ($offer->media as $oldVid) {
                            $oldPath = public_path('uploads/offers/videos/' . $oldVid);
                            if (File::exists($oldPath)) File::delete($oldPath);
                        }
                        $storedMedia = [];
                    }
                    $filename = ImageUploadHelper::OfferVideoUpload($request->file('media'));
                    $storedMedia = [$filename];
                }
            }

            $data = [
                'title'   => $request->title,
                'position' => $request->position,
                'media_type' => $request->media_type,
                'media' => !empty($storedMedia) ? $storedMedia : null,
                'link' => $request->link,
                'priority' => (int) $request->input('priority', 0),
                'status' => (int) $request->input('status', 1),
            ];

            if ($id === 0) {
                Offer::create($data);
                $msg = 'Offer added successfully';
            } else {
                $offer->update($data);
                $msg = 'Offer updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            logger()->error("Offer store error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->error_message
            ], $this->exception_error_code);
        }
    }

    public function getDataOffers(Request $request)
    {
        $function_name = 'getDataOffers';

        try {
            if ($request->ajax()) {

                $offers = Offer::query();

                return DataTables::of($offers)
                    ->addColumn('status', function ($offer) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $offer->status
                        ];

                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($offer) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'view_id'         => $offer->id,
                            'edit_route'      => route('admin.offers.edit', encryptId($offer->id)),
                            'delete_id'       => $offer->id,
                            'current_status'  => $offer->status,
                            'hidden_id'       => $offer->id,
                        ];

                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('media', function ($offer) {
                        if (empty($offer->media)) {
                            return '<span class="text-muted">No Media</span>';
                        }

                        if ($offer->media_type == 'image') {
                            $html = '<div class="photo-stack">';
                            $limit = 4;
                            $count = 0;
                            $total = count($offer->media);

                            foreach ($offer->media as $img) {
                                if ($count >= $limit) break;
                                $url = asset('uploads/offers/images/' . $img);
                                $html .= '<img src="' . $url . '" class="photo-stack-item" title="Offer Image" />';
                                $count++;
                            }

                            if ($total > $limit) {
                                $html .= '<div class="photo-count-badge">+' . ($total - $limit) . '</div>';
                            }

                            $html .= '</div>';
                        } else {
                            $html = '<div class="badge bg-light-info text-info d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-play-circle-fill"></i> Video
                                     </div>';
                        }
                        return $html;
                    })
                    ->addColumn('position_label', function ($offer) {
                        return ucfirst(str_replace('_', ' ', $offer->position));
                    })
                    ->rawColumns(['status', 'action', 'media'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return response()->json([
                'error' => $this->error_message
            ], $this->exception_error_code);
        }
    }

    public function show(int $id)
    {
        try {
            $offer = Offer::find($id);
            if ($offer) {
                return view('admin.offers.view_details', [
                    'offer' => $offer
                ])->render();
            }
            return response()->json(['error' => 'Offer not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function changeStatus($id, $status)
    {
        try {
            Offer::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        try {
            $offer = Offer::find($id);

            if (!$offer) {
                return response()->json(['error' => 'Offer not found'], 404);
            }

            if (!empty($offer->media) && is_array($offer->media)) {
                $dir = $offer->media_type == 'image' ? 'uploads/offers/images/' : 'uploads/offers/videos/';
                foreach ($offer->media as $file) {
                    $filePath = public_path($dir . $file);
                    if (File::exists($filePath)) File::delete($filePath);
                }
            }

            $offer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Offer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function removeMedia(Request $request)
    {
        try {
            $id = $request->id;
            $mediaName = $request->media;

            $offer = Offer::find($id);
            if ($offer && is_array($offer->media)) {
                $media = $offer->media;

                if (($key = array_search($mediaName, $media)) !== false) {
                    unset($media[$key]);
                    $media = array_values($media);

                    $offer->update(['media' => $media]);

                    $dir = $offer->media_type == 'image' ? 'uploads/offers/images/' : 'uploads/offers/videos/';
                    $path = public_path($dir . $mediaName);
                    if (File::exists($path)) File::delete($path);

                    return response()->json(['success' => true, 'message' => 'Media removed successfully']);
                }
            }
            return response()->json(['success' => false, 'message' => 'Media not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
