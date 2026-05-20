<?php

namespace App\Controllers\Api;

use App\Models\CreditModel;
use App\Models\UserModel;

class TopupApiController extends BaseApiController
{
    public function getBalance()
    {
        $userId = session()->get('userId');
        $creditModel = new CreditModel();
        
        $credit = $creditModel->where('user_id', $userId)->first();
        $balance = $credit ? $credit['balance'] : 0;

        return $this->respondSuccess(['balance' => $balance], 'Balance retrieved successfully.');
    }

    public function checkout()
    {
        $userId = session()->get('userId');
        $amount = $this->request->getPost('amount');

        if (!$amount || !is_numeric($amount) || $amount <= 0) {
            return $this->respondError('Valid amount is required.', 400);
        }

        // Simulating a checkout process for Midtrans or similar
        // In a real app, this would return a snap token or similar
        return $this->respondSuccess([
            'checkout_url' => base_url("topup/process?amount=$amount"),
            'amount'       => $amount,
            'status'       => 'pending'
        ], 'Checkout initiated.');
    }
}
