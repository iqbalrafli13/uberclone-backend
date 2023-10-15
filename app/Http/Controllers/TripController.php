<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin'=>'required',
            'destination'=>'required',
            'destination_name'=>'required',
        ]);

        return $request->user()->trips()->create($request->only([
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

    
    public function accept(Request $request, Trip $trip)
    {
        // driver accept the trip
        $request->validate([
            'driver_location'=>'required',
        ]);

        $trip->upadate([
            'driver_id'=> $request->user()->id,
            'driver_location'=> $request->driver_location
        ]);

        $trip->load('driver.user');
        
        return $trip;
    }

    public function start(Request $request, Trip $trip)
    {
        // a driver has started taking passanger to their destination
        $trip->upadate([
            'is_started'=> true
        ]);

        $trip->load('driver.user');
        
        return $trip;
    }

    public function end(Request $request, Trip $trip)
    {
        // adriver has ended a trip
        $trip->upadate([
            'is_complete'=> true
        ]);

        $trip->load('driver.user');
        
        return $trip;
    }

    public function location(Request $request, Trip $trip)
    {
        // update the driver current location
        $request->validate([
            'driver_location'=>'required',
        ]);

        $trip->upadate([
            'driver_location'=> $request->driver_location
        ]);

        $trip->load('driver.user');
        
        return $trip;
    }
}
