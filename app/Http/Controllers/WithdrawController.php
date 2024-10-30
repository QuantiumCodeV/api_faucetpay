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
        if ($request->type == 'PRIORITY') {
            $fee = $currency->min_withdraw * 0.0005;
        }
        if($request->type == 'NORMAL'){
            $fee = $currency->min_withdraw * 0.0001;
        }

        if ($request->amount == 0) {
            $receive_amount = 0;
        } else {
            $receive_amount = $request->amount - $fee;
        }

        if ($receive_amount < $currency->min_withdraw) {
            $receive_amount = 0;
        }
        if ($receive_amount > $currency->max_withdraw_per_transaction) {
            return response()->json(['success' => false, 'message' => 'Amount is too high', 'data' => []]);
        }

        return response()->json(['success' => true, 'message' => '', 'data' => ['fee' => $fee, 'minimum' => $currency->min_withdraw, 'receive_amount' => $receive_amount]]);
    }
}
