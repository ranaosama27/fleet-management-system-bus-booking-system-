<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripsMain;
use App\Models\TripsMid;
use App\Models\User_booking;

class UserBookingController extends Controller
{
    public function store(Request $request){
        $user_id=$request->user_id;
        $start_station_id=$request->start_station;
        $end_station_id=$request->end_station;
        $trip_main=TripsMain::where('start_station_id',$start_station_id)
        ->where('end_station_id',$end_station_id)->get();
        
        if($trip_main->count()>0){
            if(User_booking::create([
                'user_id'=>$user_id,
                'trip_main_id'=>$trip_main->first()->id
                ])){
                    echo "created main";

            }else{
                echo "not created";
            }
        
        }else{
            $trip_mid=TripsMid::where('start_station_id',$start_station_id)
            ->where('end_station_id',$end_station_id)->get();
            if($trip_mid->count()>0){
                if(User_booking::create([
                    'user_id'=>$user_id,
                    'trip_main_id'=>$trip_mid->first()->trip_main_id,
                    'trip_mid_id'=>$trip_mid->first()->id
                    ])){
                        echo "created mid";
                }else{
                    echo "not created";
                }
            }else{
                echo "trip doesn't exist";

            }
        }

    }
}
