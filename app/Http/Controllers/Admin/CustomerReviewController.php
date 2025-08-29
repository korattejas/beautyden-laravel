<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;

class CustomerReviewController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/CustomerReviewController";
    }

    public function index()
    {
        try {
            return view('admin.reviews.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $services = Service::where('status', 1)->select('id', 'name')->get();
            return view('admin.reviews.create', compact('services'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $review = CustomerReview::findOrFail(decryptId($id));
            $services = Service::where('status', 1)->select('id', 'name')->get();

            return view('admin.reviews.edit', compact('review', 'services'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataReviews(Request $request)
    {
        try {
            if ($request->ajax()) {
                $reviews = CustomerReview::query()
                    ->leftJoin('services as s', 's.id', '=', 'customer_reviews.service_id')
                    ->select('customer_reviews.*', 's.name as service_name');

                return DataTables::of($reviews)
                    ->addColumn('status', function ($r) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $r->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_popular', function ($r) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $r->is_popular
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($r) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.reviews.edit', encryptId($r->id)),
                            'delete_id' => $r->id,
                            'current_status' => $r->status,
                            'current_is_popular_priority_status' => $r->is_popular,
                            'hidden_id' => $r->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('photos', function ($r) {
                        $photos = json_decode($r->photos, true);
                        $html = '';
                        if (!empty($photos)) {
                            foreach ($photos as $photo) {
                                $html .= '<img src="' . asset("uploads/review/photos/" . $photo) . '" style="max-width:100px;  margin-right:5px;" alt="Review Photo" />';
                            }
                        }
                        return $html;
                    })
                    ->addColumn('video', function ($r) {
                        if ($r->video) {
                            return '<video width="120" controls>
                                        <source src="' . asset("uploads/review/videos/" . $r->video) . '" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>';
                        }
                        return '-';
                    })
                    ->rawColumns(['action', 'photos', 'video', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataReviews');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        try {
            $rules = [
                'service_id'    => 'required|exists:services,id',
                'customer_name' => 'required|string|max:100',
                'customer_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'rating'        => 'nullable|numeric|min:0|max:5',
                'review'        => 'nullable|string',
                'review_date'   => 'nullable|date',
                'photos.*'      => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'video'         => 'nullable',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                logValidationException($this->controller_name, 'store', $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $photo = null;
            if ($request->hasFile('customer_photo')) {
                $photo = ImageUploadHelper::reviewCustomerImageUpload($request->file('customer_photo'));
            } elseif ($id != 0) {
                $photo = CustomerReview::find($id)?->customer_photo;
            }

            $photos = null;
            if ($request->hasFile('photos')) {
                $photos_array = [];
                foreach ($request->file('photos') as $file) {
                    $photos_array[] = ImageUploadHelper::reviewImageUpload($file);
                }
                $photos = json_encode($photos_array);
            } elseif ($id != 0) {
                $photos = CustomerReview::find($id)?->photos;
            }

            $video = null;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $video = ImageUploadHelper::reviewVideoUpload($file);
            } elseif ($id != 0) {
                $video = CustomerReview::find($id)?->video;
            }

            $data = [
                'service_id'     => $request->service_id,
                'customer_name'  => $request->customer_name,
                'customer_photo' => $photo,
                'rating'         => $request->rating,
                'review'         => $request->review,
                'review_date'    => $request->review_date ?? today(),
                'helpful_count'  => $request->helpful_count ?? 0,
                'photos'         => $photos,
                'video'          => $video,
                'is_popular'     => (int) $request->is_popular,
                'status'         => (int) $request->status,
            ];

            if ($id == 0) {
                CustomerReview::create($data);
                return response()->json(['success' => true, 'message' => "Review added successfully"]);
            } else {
                CustomerReview::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Review updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            CustomerReview::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changePopularStatus($id, $status)
    {
        try {
            CustomerReview::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $review = CustomerReview::find($id);
            if ($review) {
                if ($review->customer_photo) {
                    $filePath = public_path('uploads/review/customer-photos/' . $review->customer_photo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }

                 if ($review->photos) {
                    $photos = json_decode($review->photos, true);
                    if (!empty($photos)) {
                        foreach ($photos as $photo) {
                            $filePath = public_path('uploads/review/photos/' . $photo);
                            if (File::exists($filePath)) {
                                File::delete($filePath);
                            }
                        }
                    }
                }

                if ($review->video) {
                    $filePath = public_path('uploads/review/videos/' . $review->video);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $review->delete();
                return response()->json(['message' => 'Review deleted successfully']);
            }
            return response()->json(['error' => 'Review not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
