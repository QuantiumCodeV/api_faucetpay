<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkAdresses;

class LinkAdressesController extends Controller
{
    public function getLinkedAddresses(Request $request)
    {
        $linkedAddresses = LinkAdresses::where('user_id', $request->user()->id)->get();

        $formattedAddresses = $linkedAddresses->map(function ($address) {
            return [
                "id" => $address->id,
                "coin" => $address->coin,
                "address" => $address->address,
                "label" => $address->label
            ];
        });

        return response()->json([
            'success' => true,
            'message' => '',
            'addresses' => $formattedAddresses
        ]);
    }

    public function linkAddress(Request $request)
    {
        $request->validate([
            'coin' => 'required|string',
            'address' => 'required|string',
            'label' => 'required|string'
        ]);

        LinkAdresses::create([
            'user_id' => $request->user()->id,
            'coin' => $request->coin,
            'address' => $request->address,
            'label' => $request->label
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Great news! The address has been linked to your account.'
        ]);
    }

    public function removeLinkedAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|integer'
        ]);


        $address = LinkAdresses::find($request->address_id);
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'The address has been deleted.'
        ]);
    }
}
