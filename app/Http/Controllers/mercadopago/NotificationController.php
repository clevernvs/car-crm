<?php

namespace App\Http\Controllers\mercadopago;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class NotificationController extends Controller
{
    private $accessToken;

    public function __construct()
    {
        $this->accessToken = env('MP_ACCESS_TOKEN');
    }

    public function notification(Request $request)
    {
        Log::info([
            'payment' => $request->all(),
        ]);

        if (isset($request->type, $request->data['id'])) {
            return response()->json(400);
        }

        if ($request->type == 'payment') {
            $payment = Curl::to('https://api.mercadopago.com/v1/payments/'.$request->data['id'])
                            ->withAuthorization('Bearer '.$this->accessToken)
                            ->asJson()
                            ->get();

            if (isset($payment->id)) {
                $transaction = Transactions::where('transaction_id', $payment->id)
                                ->where('user_id', $payment->metadata->user_id)
                                ->first();
                if ($transaction->id) {
                    $transaction->update([
                        'status'        => $payment->status,
                        'status_detail' => $payment->status_detail,
                    ]);
                }

                if ($payment->status === 'approved') {
                    $user = User::find($payment->metadata->user_id);
                    $plan = Plans::find($payment->metadata->item->id);

                    $dateNextExpiration = new Carbon($user->next_expiration);
                    $dateNextExpiration = $dateNextExpiration->addMonths($plan->period);

                    // if (empty($user)) {

                    // }

                    $user->status           = 2;
                    $user->plan_id          = $plan->id;
                    $user->disabled_account = null;
                    $user->delete_account   = null;
                    $user->next_expiration  = $dateNextExpiration;

                    // if (!$user->save()) {
                    //     return response()->json(400);
                    // }

                    // return response()->json(200);

                    if ($user->save()) {
                        return response()->json(200);
                    }
                }
            } else {
                return response()->json(400);
            }
        }
    }
}
