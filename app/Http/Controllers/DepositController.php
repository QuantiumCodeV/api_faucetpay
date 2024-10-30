<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;

class DepositController extends Controller
{
    public function getDepositHistory(Request $request)
    {
        $user = $request->user();
        $page = $request->input('page', 1);
        $deposits = Deposit::where('user_id', $user->id)->paginate(10, '*', 'page', $page);
        $formattedDeposits = $deposits->map(function($deposit) {
            return [
                'id' => $deposit->id,
                'amount' => $deposit->amount,
                'coin' => $deposit->coin,
                'confirmations' => $deposit->confirmations,
                'credited' => $deposit->credited,
                'datetime' => $deposit->datetime,
                'transaction_id' => $deposit->transaction_id
            ];
        });
        return response()->json([
            'data' => [
                'deposits' => $formattedDeposits,
                'total_pages' => $deposits->lastPage(),
                'current_page' => $deposits->currentPage()
            ],
            'message' => '',
            'success' => true
        ]);
    }
}
