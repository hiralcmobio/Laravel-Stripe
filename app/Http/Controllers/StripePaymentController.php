<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Stripe;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
            "amount" => 50,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Test payment."
        ]);
        Event::where('id', $request->event_id)->update(['status'=>1]);

        Session::flush('event_id');
        Session::put('success', 'Payment successful!');

        return Redirect::to('home');
    }
}
