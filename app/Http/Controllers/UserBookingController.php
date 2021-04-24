<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripsMain;
use App\Models\TripsMid;
use App\Models\User_booking;

class UserBookingController extends Controller
{
    public function store(Request $request){
        $results = [
            'success' => false,
            'data' => [],
            'message' => ""
            ];
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
                    $results['success']=true;
                    $results['message']="Bus booked successfully!";

            }else{
                $results['success'] = false;
                $results['message'] = "Something went wrong, we were not able to book the trip for you.";
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
                        $results['success']=true;
                        $results['message']="Bus booked successfully!";
                }else{
                    $results['success'] = false;
                    $results['message'] = "Something went wrong, we were not able to book the trip for you.";
                }
            }else{
                $results['success'] = false;
                $results['message'] = "Sorry, Your Trip doesn't exist !";

            }
        }
        return response()->json($results);

    }
    public function index(Request $request){
        $results = [
            'success' => false,
            'data' => [],
            'message' => ""
            ];
        $start_station_id=$request->start_id;
        $end_station_id=$request->end_id;
        $trip_main=TripsMain::where('start_station_id',$start_station_id)
        ->where('end_station_id',$end_station_id)->get();
        if($trip_main->count()>0){
            $trip_id=$trip_main->first()->id;
            $seats_num=User_booking::where('trip_main_id',$trip_id)->count();
            $available_seat=12-$seats_num;
            if($available_seat>0){
                $results['success'] = true;
                $results['data']['seats_available']=$available_seat;
                $results['message'] =  "We have available seats";
            }else{
                $results['success'] = false;
                $results['message'] = "Sorry, Bus Fully Booked.";
            }
        }else{
            $trip_mid=TripsMid::where('start_station_id',$start_station_id)
            ->where('end_station_id',$end_station_id)->get();
            if($trip_mid->count()>0){
                $trip_id=$trip_mid->first()->id;
                $trip_main_id=$trip_mid->first()->trip_main_id;
                $seats_num_main=User_booking::where('trip_main_id',$trip_main_id)
                ->whereNull('trip_mid_id')->count();
                $seats_num_mid=User_booking::whereHas('TripMid',function($query) use($end_station_id){
                    $query->where('end_station_id','>=',$end_station_id);
                })->count();
                $available_seat=12-($seats_num_main+$seats_num_mid);
                if($available_seat>0){
                    $results['success'] = true;
                    $results['data']['seats_available']=$available_seat;
                    $results['message'] =  "We have available seats";
                }else{
                    $results['success'] = false;
                    $results['message'] = "Sorry, Bus Fully Booked.";
                }

            }
        }
        return response()->json($results);
    }
}
