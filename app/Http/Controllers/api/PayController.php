<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Repositories\Contracts\PlansRepositoryInterface;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class PayController extends Controller
{
    protected $user;
    protected $plansRepo;

    private $accessToken;

    public function __construct(PlansRepositoryInterface $plansRepo)
    {
        $this->user = Auth()->guard('api')->user();
        $this->accessToken = env('MP_ACCESS_TOKEN');
        $this->plansRepo = $plansRepo;
    }

    public function plans()
    {
        $plans = $this->plansRepo->getAll();

        return compact('plans');
    }

    public function card(Request $request)
    {
        $plan = $this->plansRepo->findById($request->plan_id);
        if (empty($plan)) {
            return $this->error('Plano não encontrado.');
        }

        $payment = Curl::to('https://api.mercadopago.com/v1/payments')
                        ->withAuthorization("Bearer {$this->accessToken}")
                        ->withData([
                            'external_reference' => $this->user->id,
                            'transaction_amount' => (float)$plan->price,
                            'token' => $request->token,
                            'description' => $plan->title,
                            'instalments' => 1,
                            'payment_method_id' => $request->payment_method_id,
                            'payer' => (object)[
                                'email' => $request->email,
                                'identification' => (object)[
                                    'type' => 'CPF',
                                    'number' => $request->cpf,
                                ],
                            ],
                            'metadata' => (object)[
                                'user_id' => $this->user->id,
                                'item' => $plan,
                            ],
                            'notification_url' => env('MP_URL_NOTIFICATION'),
                        ])
                        ->asJson()
                        ->post();

        if (isset($payment->id)) {
            $transaction = Transactions::create([
                'user_id' => $this->user->id,
                'item' => $payment->metadata->item,
                'transaction_id' => $payment->id,
                'transaction_amount' => $payment->transaction_amount,
                'payment_method_id' => $payment->payment_method_id,
                'payment_type_id' => $payment->payment_type_id,
                'last_four_digits' => $payment->card->last_four_digits,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
            ]);

            if ($transaction->id) {
                return response()->json([
                    'success' => 200,
                    'id' => $payment->id,
                ], 200);
            }
        }
        return $this->error('Erro ao processar o pagamento.');

        // return response()->json(['payment' => $payment], 200);
    }

    public function pec(Request $request)
    {
        $plan = $this->plansRepo->findById($request->plan_id);
        if (empty($plan)) {
            return $this->error('Plano não encontrado.');
        }

        $payment = Curl::to('https://api.mercadopago.com/v1/payments')
                        ->withAuthorization("Bearer {$this->accessToken}")
                        ->withData([
                            'external_reference' => $this->user->id,
                            'transaction_amount' => (float)$plan->price,
                            'description' => $plan->title,
                            'payment_method_id' => $request->payment_method_id,
                            'payer' => (object)[
                                'first_name' => $request->first_name,
                                'last_name' => $request->last_name,
                                'email' => $request->email,
                                'identification' => (object)[
                                    'type' => 'CPF',
                                    'number' => $request->cpf,
                                ],
                            ],
                            'metadata' => (object)[
                                'user_id' => $this->user->id,
                                'item' => $plan,
                            ],
                            'notification_url' => env('MP_URL_NOTIFICATION'),
                        ])
                        ->asJson()
                        ->post();


        // return response()->json(['payment' => $payment], 200);

        if (isset($payment->id)) {
            $transaction = Transactions::create([
                'user_id' => $this->user->id,
                'item' => $payment->metadata->item,
                'transaction_id' => $payment->id,
                'transaction_amount' => $payment->transaction_amount,
                'barcode' => $payment->barcode->content,
                'external_source_url' => $payment->transaction_details->external_resource_url,
                'payment_method_id' => $payment->payment_method_id,
                'payment_type_id' => $payment->payment_type_id,
                'last_four_digits' => $payment->card->last_four_digits,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
            ]);

            if ($transaction->id) {
                return response()->json([
                    'success' => 200,
                    'id' => $payment->id,
                ], 200);
            }
        }
        return $this->error('Erro ao processar o pagamento.');
    }
}
