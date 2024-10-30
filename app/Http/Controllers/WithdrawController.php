<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function askForReview(Request $request)
    {
        return response()->json(['success' => true, 'message' => '', 'request_review' => false]);
    }

    public function getWithdrawalInformation(Request $request)
    {
        return response()->json(['success' => true, 'message' => '', 'accepted_modes' => ['normal' => false, 'priority' => true], 'tfa_code_required' => false, 'memo_required' => false, 'coin' => $request->coin]);
    }
}
