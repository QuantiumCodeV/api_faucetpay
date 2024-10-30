<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function hasAgreed(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => '',
                'agreed' => true
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'agreed' => false
            ]);
        }
    }

    public function canAccess(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => '',
                'can_access' => true
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'can_access' => false
            ]);
        }
    }
}
