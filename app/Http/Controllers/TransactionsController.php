<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function getAll(Request $request)
    {
        $user = $request->user();
        $transactions = $user->transactions()->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'current_page' => $transactions->currentPage(),
                'transactions' => $transactions->items()
            ]
        ]);
    }
}
