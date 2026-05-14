<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\BeauticianSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    protected $error_message, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->controller_name = "Admin/SettlementController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            $members = TeamMember::where('status', 1)->get();
            
            foreach ($members as $member) {
                $member->settlement = BeauticianSettlement::firstOrCreate(
                    ['team_member_id' => $member->id],
                    [
                        'company_to_beautician' => 0,
                        'beautician_to_company' => 0
                    ]
                );
            }

            return view('admin.settlement.index', compact('members'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return redirect()->back()->with('error', $this->error_message);
        }
    }

    public function update(Request $request)
    {
        $function_name = 'update';
        try {
            $request->validate([
                'team_member_id' => 'required|exists:team_members,id',
                'company_to_beautician' => 'required|numeric|min:0',
                'beautician_to_company' => 'required|numeric|min:0',
            ]);

            BeauticianSettlement::updateOrCreate(
                ['team_member_id' => $request->team_member_id],
                [
                    'company_to_beautician' => $request->company_to_beautician,
                    'beautician_to_company' => $request->beautician_to_company,
                    'updated_at' => now()
                ]
            );

            return response()->json(['success' => true, 'message' => 'Settlement updated successfully', 'updated_at' => now()->format('d M Y, h:i A')]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['success' => false, 'message' => $this->error_message]);
        }
    }
}
