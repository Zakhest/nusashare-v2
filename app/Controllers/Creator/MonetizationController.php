<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\CreditModel;

class MonetizationController extends BaseController
{
    public function index()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');
        $username = session()->get('username');

        $userModel = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        $creditModel = new CreditModel();

        // Get user and creator data
        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);
        
        // Get balance
        $credit = $creditModel->find($userId);
        $balance = $credit['balance'] ?? 0;

        // Get real transaction history
        $transactionModel = new \App\Models\TransactionModel();
        $transactions = $transactionModel->where('user_id', $userId)
                                         ->orderBy('created_at', 'DESC')
                                         ->findAll();

        $history = [];
        foreach ($transactions as $tr) {
            $history[] = [
                'date'        => $tr['created_at'],
                'amount'      => $tr['amount'],
                'type'        => $tr['type'],
                'category'    => $tr['category'],
                'description' => $tr['description'],
                'status'      => 'Selesai' // Currently all transactions recorded are completed
            ];
        }

        // Calculate Monthly Earnings (CC x 10)
        $startOfMonth = date('Y-m-01 00:00:00');
        $monthlyTransactions = $transactionModel->where('user_id', $userId)
                                                ->where('type', 'in')
                                                ->where('created_at >=', $startOfMonth)
                                                ->findAll();
        
        $totalMonthlyCC = 0;
        $breakdown = [];
        foreach ($monthlyTransactions as $mtr) {
            $totalMonthlyCC += $mtr['amount'];
            $catLabel = ucfirst($mtr['category']);
            $breakdown[$catLabel] = ($breakdown[$catLabel] ?? 0) + ($mtr['amount'] * 10);
        }

        $monthlyEarnings = [
            'total'     => $totalMonthlyCC * 10,
            'total_cc'  => $totalMonthlyCC,
            'growth'    => 0, // Placeholder
            'breakdown' => $breakdown
        ];

        if (empty($breakdown)) {
            $monthlyEarnings['breakdown'] = ['Belum ada pendapatan' => 0];
        }

        $data = [
            'title'           => 'Monetisasi Kreator - NusaShare',
            'username'        => $username,
            'user'            => $user,
            'creatorProfile'  => $creatorProfile,
            'balance'         => $balance,
            'history'         => $history,
            'monthlyEarnings' => $monthlyEarnings,
            'activePage'      => 'monetization'
        ];

        return view('creator/monetization/index', $data);
    }
}
