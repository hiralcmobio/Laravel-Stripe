<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('event');
    }
    public function eventPost(Request $request)
    {
        $input = $request->all();
        //Validate requested data
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'mobileno' => 'required|min:10|numeric',
            'address' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $event = Event::create($input);
        $event_id = $event->id;
        Session::put('event_id', $event_id);
        Session::put('user_add', "You have registered for an Event. Please add your card detail.");
        return Redirect::to('stripe');
    }

}
