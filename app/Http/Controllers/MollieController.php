<?php

namespace App\Http\Controllers;
use Mollie\Laravel\Facades\Mollie;
use Auth;
use App\User;
class MollieController extends Controller
{   

    public function  __construct() {
        Mollie::api()->setApiKey('test_aUVzeaA8VsSmEVbUaNQwRx3C2Jb6P3'); // your mollie test api key
    }

    /**
     * Redirect the user to the Payment Gateway.
     * @param  int  $id
     * @return Response
     */
    public function preparePayment(int $id)
    {   
        
        $id == 1 ? $value = '2.00' : $value = '4.00';
        $payment = Mollie::api()->payments()->create([
        'amount' => [
            'currency' => 'EUR', // Type of currency you want to send
            'value' => $value, // You must send the correct number of decimals, thus we enforce the use of strings
        ],
        'description' => 'Payment By codehunger', 
        'redirectUrl' => route('payment.success'), // after the payment completion where you to redirect
        ]);
        session()->put('p_id', $payment->id);
        $payment = Mollie::api()->payments()->get($payment->id);
    
        // redirect customer to Mollie checkout page
        return redirect($payment->getCheckoutUrl(), 303);
    }

    /**
     * Page redirection after the successfull payment
     *
     * @return Response
     */
    public function paymentSuccess() {
       // echo 'payment has been received';
       // dd(Mollie::api()->payments()->get(session()->get('p_id')));
        $payment = Mollie::api()->payments()->get(session()->get('p_id'));
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        //$user->trial_ends_at = now()->addDays(15);
       // $user->save();
        $user->newSubscription('main', 'premium')->trialDays(10)->create();
    }
}
