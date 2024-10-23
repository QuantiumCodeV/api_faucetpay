<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;

class TransactionsController extends Controller
{
    public function getAll(Request $request)
    {
        $user = $request->user();
        //$transactions = Transactions::where('user_id', $user->id)->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'current_page' => 1,
                'transactions' => []
            ]
        ]);
    }
}
