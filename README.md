## **Stripe Integration with Laravel**

Laravel provides inbuilt library for Stripe. We can use stripe/stripe-php library in Laravel for Stripe integration.

Stripe is secure internet payment gateway as well as it is popular which acceept payment. Stripe provides free develop account.

So, let's start with Stripe.

**1) Create laravel project first**


    composer create-project --prefer-dist laravel/laravel laraStripe
    

**2) Install Stripe package in laravel**


    composer require stripe/stripe-php
    
    
**3) Add stripe keys to your env file.**

First, make your stripe account and take the API keys from it and add it in env file.

    STRIPE_KEY=pk_test_reFxwbsm9cdCKASdTfxAR
    
    STRIPE_SECRET=sk_test_oQMFWteJiPd4wj4AtgApY
    
    
**4) Create event table model and controller for add event.**

We will make one event controller, model and table for add event by below command.
 
    php artisan make:migration create_events_table
    
    php artisan make:model Event
    
    php artisan make:controller EventController
    
Now, add below code to migration file `database/migrations/2020_02_26_110034_create_new_event_table.php`.

    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    class CreateNewEventTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->text('address');
                $table->string('mobileno');
                $table->boolean('status')->default(0);
                $table->timestamps();
            });
        }
    
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('events');
        }
    }


And add below code to model file `app/Event.php`.

    <?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    
    class Event extends Model
    {
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name', 'email', 'mobileno', 'address', 'status'
        ];
    }

Now, we will make event registration form. So add blade file `resources/view/event.blade.php`.

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Laravel 5.7 JQuery Form Validation Example - ItSolutionStuff.com</title>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css"/>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    </head>
    <body>
    <div class="container">
        <h2>Add your detail here for event registration</h2><br/>
        @if (Session::has('success'))
            <div class="alert alert-success text-center" id="message_id">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <p>{{ Session::get('success') }}</p>
            </div>
        @endif
    
        <form method="post" action="eventPost" id="form">
            @csrf
    
            <div class="row">
                <div class="col-md-4"></div>
                <div class="form-group col-md-4">
                    <label for="Name">Name:</label>
                    <input type="text" class="form-control" name="name">
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-4"></div>
                <div class="form-group col-md-4">
                    <label for="Email">Email:</label>
                    <input type="email" class="form-control" name="email">
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-4"></div>
                <div class="form-group col-md-4">
                    <label for="Number">Mobile no:</label>
                    <input type="text" class="form-control" name="mobileno">
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-4"></div>
                <div class="form-group col-md-4">
                    <label for="Address">Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-4"></div>
                <div class="form-group col-md-4">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
    
        </form>
    
    </div>
    
    <script>
    
        $(document).ready(function () {
    
            $('#form').validate({ // initialize the plugin
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobileno: {
                        required: true,
                        digits: true,
                    },
                    address: {
                        required: true
                    },
                }
            });
        });
    
        $("document").ready(function(){
            setTimeout(function(){
                $("#message_id").remove();
            }, 3000 );
        });
    </script>
    
    </body>
    
    </html>


We will add code to controller for view and add event like below.

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
    
            //Add event registration data to database
            $event = Event::create($input);
            $event_id = $event->id;
            Session::put('event_id', $event_id);
            Session::put('user_add', "You have registered for an Event. Please add your card detail.");
            return Redirect::to('stripe');
        }
    
    }

Now, add routes for this controller function to `routes/web.php` file.

    Route::get('/home', 'EventController@index')->name('home');
    Route::post('/eventPost', 'EventController@eventPost');
    
    
So, our event registration page is ready, now, we will make payment through stripe. and after register, we need to add payment.

**5) Add Stripe payment**

Let's make controller and view for stripe. make controller by below command.

    php artisan make:controller StripePaymentController
    
Add below code to `StripePaymentController`.

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


Let,s make view file `resources/views/stripe.blade.php`.

    <!DOCTYPE html>
    <html>
    <head>
        <title>Laravel 5 - Stripe Payment Gateway Integration Example - ItSolutionStuff.com</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <style type="text/css">
            .panel-title {
                display: inline;
                font-weight: bold;
            }
            .display-table {
                display: table;
            }
            .display-tr {
                display: table-row;
            }
            .display-td {
                display: table-cell;
                vertical-align: middle;
                width: 61%;
            }
        </style>
    </head>
    <body>
    
    <div class="container">
    <?php //echo $eventId; ?>
        <h1>Stripe Payment Gateway</h1>
    
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default credit-card-box">
                    <div class="panel-heading display-table" >
                        <div class="row display-tr" >
                            <h3 class="panel-title display-td" >Payment Details</h3>
                            <div class="display-td" >
                                <img class="img-responsive pull-right" src="http://i76.imgup.net/accepted_c22e0.png">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
    
                        @if (Session::has('user_add'))
                            <div class="alert alert-success text-center" id="message_id">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('user_add') }}</p>
                            </div>
                        @endif
    
                        <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation"
                              data-cc-on-file="false"
                              data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                              id="payment-form">
                            @csrf
    
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required'>
                                    <label class='control-label'>Name on Card</label> <input
                                        class='form-control' size='4' type='text'>
                                </div>
                            </div>
    
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group card required'>
                                    <label class='control-label'>Card Number</label> <input
                                        autocomplete='off' class='form-control card-number' size='20'
                                        type='text'>
                                </div>
                            </div>
    
                            <div class='form-row row'>
                                <div class='col-xs-12 col-md-4 form-group cvc required'>
                                    <label class='control-label'>CVC</label> <input autocomplete='off'
                                                                                    class='form-control card-cvc' placeholder='ex. 311' size='4'
                                                                                    type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required'>
                                    <label class='control-label'>Expiration Month</label> <input
                                        class='form-control card-expiry-month' placeholder='MM' size='2'
                                        type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required'>
                                    <label class='control-label'>Expiration Year</label> <input
                                        class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                        type='text'>
                                </div>
                            </div>
    
                            <div class='form-row row'>
                                <div class='col-md-12 error form-group hide'>
                                    <div class='alert-danger alert'>Please correct the errors and try
                                        again.</div>
                                </div>
                            </div>
    
                            <input type="hidden" name="event_id" value="{{ Session::get('event_id') }}">
    
                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Pay Now ($100)</button>
                                </div>
                            </div>
    
                        </form>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    
    </body>
    
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    
    <script type="text/javascript">
        $(function() {
            var $form         = $(".require-validation");
            $('form.require-validation').bind('submit', function(e) {
                var $form         = $(".require-validation"),
                    inputSelector = ['input[type=email]', 'input[type=password]',
                        'input[type=text]', 'input[type=file]',
                        'textarea'].join(', '),
                    $inputs       = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid         = true;
                $errorMessage.addClass('hide');
    
                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault();
                    }
                });
    
                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
    
            });
    
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }
    
        });
        $("document").ready(function(){
            setTimeout(function(){
                $("#message_id").remove();
            }, 3000 );
        });
    </script>
    </html>

Let's add route to `routes/web.php` file.

    Route::get('stripe', 'StripePaymentController@stripe');
    Route::post('stripe', 'StripePaymentController@stripePost')->name('stripe.post');
    
Now, run below command to terminal

    php artisan serve
    
and run project to browser.

    http://127.0.0.1:8001/home
    
Let's Register for event and pay by Stripe payment.

    


    
    


