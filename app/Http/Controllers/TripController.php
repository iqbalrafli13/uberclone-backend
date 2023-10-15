<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin'=>'required',
            'destination'=>'required',
            'destination_name'=>'required',
        ]);

        return $request->user()->trips->create($request->only([
            'origin',
            'destination',
            'destination_name',
        ]));
    }

    public function show(Request $request, Trip $trip)
    {
        if ($trip->user->id == $request->user()->id) {
            return $trip;
        }

        // kalau trip nya ada sudah ada driver dan yang request driver
        if ($trip->driver && $request->user()->driver) {
            //trip nya punya driver yang akses
            if ($trip->driver->id == $request->user()->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message'=>'Cannot find this trip'],404);
    }
}
