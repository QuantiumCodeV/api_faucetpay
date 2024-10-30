<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\LinkAdresses;
use App\Services\WestWalletService;
use App\Models\Withdraw;

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
        if ($request->type == 'NORMAL') {
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

    public function createWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string',
            'address_id' => 'required|integer',
            'coin' => 'required|string',
            'code' => 'nullable|string',
            'memo' => 'nullable|string'
        ]);

        $linkedAddress = LinkAdresses::find($request->address_id);
        $currency = Currency::where('tickers', 'like', '%' . $request->coin . '%')->first();
        if (!$linkedAddress) {
            return response()->json(['success' => false, 'message' => 'Linked address not found', 'data' => []]);
        }

        if ($linkedAddress->user_id != $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to use this address', 'data' => []]);
        }

        if ($linkedAddress->coin != $request->coin) {
            return response()->json(['success' => false, 'message' => 'Address is not for this coin', 'data' => []]);
        }

        if ($request->amount < $currency->min_withdraw) {
            return response()->json(['success' => false, 'message' => 'Amount is too low', 'data' => []]);
        }

        if ($request->amount > $currency->max_withdraw_per_transaction) {
            return response()->json(['success' => false, 'message' => 'Amount is too high', 'data' => []]);
        }

        $fee = $currency->min_withdraw * 0.0001;
        if ($request->type == 'PRIORITY') {
            $fee = $currency->min_withdraw * 0.0005;
        }
        if ($request->type == 'NORMAL') {
            $fee = $currency->min_withdraw * 0.0001;
        }

        $receive_amount = $request->amount - $fee;

        if ($receive_amount < $currency->min_withdraw) {
            $receive_amount = 0;
        }

        if ($receive_amount > $currency->max_withdraw_per_transaction) {
            return response()->json(['success' => false, 'message' => 'Amount is too high', 'data' => []]);
        }


        $westWalletService = new WestWalletService();
        $result = $westWalletService->createWithdrawal($request->coin, $receive_amount, $linkedAddress->address);
        if ($result == "Insufficient funds") {
            return response()->json(['success' => false, 'message' => 'Insufficient funds', 'data' => []]);
        }

        if ($result == null) {
            return response()->json(['success' => false, 'message' => 'Error creating withdrawal', 'data' => []]);
        }

        $withdrawal = new Withdraw();
        $withdrawal->user_id = $request->user()->id;
        $withdrawal->coin = $request->coin;
        $withdrawal->amount = $request->amount;
        $withdrawal->type = $request->type;
        $withdrawal->address = $linkedAddress->address;
        $withdrawal->save();

        

        return response()->json(['success' => true, 'message' => 'Your withdrawal has been placed. It will be processed shortly.', 'data' => []]);
    }
}
