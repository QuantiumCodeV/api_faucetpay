<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

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

    public function estimateWithdrawalCharges(Request $request)
    {
        $currency = Currency::where('tickers', 'like', '%' . $request->coin . '%')->first();
        if (!$currency) {
            return response()->json(['success' => false, 'message' => 'Currency not found', 'data' => []]);
        }
        $fee = $currency->min_withdraw * 0.0001;
        $receive_amount = $request->amount - $fee;
        return response()->json(['success' => true, 'message' => '', 'data' => ['fee' => $fee, 'minimum' => $currency->min_withdraw, 'receive_amount' => $receive_amount]]);
    }
}
