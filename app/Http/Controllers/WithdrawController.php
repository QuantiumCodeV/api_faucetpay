<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function askForReview(Request $request)
    {
        return response()->json(['success' => true, 'message' => '', 'request_review' => false]);
    }
}
