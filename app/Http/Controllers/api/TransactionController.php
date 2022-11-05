<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->guard('api')->user();
    }

    public function index()
    {
        $transactions = Transactions::where('user_id', $this->user->id)
            ->orderBy('id', 'DESC')
            ->paginate(env('APP_PAGINATE'));

        return compact('transacitons');
    }

    public function show($id)
    {
        $transactions = Transactions::where('user_id', $this->user->id)
            ->where('transaction_id', $id)
            ->first();

        $transactions->transform(function ($transaction) {
            $transaction->status_pt_br = __('mercadoPago.' . $transaction->status);
            $transaction->status_detail = __('mercadoPago.' . $transaction->status_detail, [
                'statement_decriptor' => $transaction->description,
                'payment_method_id' => $transaction->payment_method_id,
            ]);
            return $transaction;
        });

        return compact('transactions');
    }
}
