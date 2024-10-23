<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WestWalletService;

class CurrencyController extends Controller
{
    public function getCurrencies()
    {
        $westWalletService = new WestWalletService();
        $westWalletData = $westWalletService->getCurrenciesData();

        // Получаем цены криптовалют с CoinGecko API
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://api.coingecko.com/api/v3/simple/price', [
            'query' => [
                'ids' => implode(',', array_column($westWalletData, 'name')),
                'vs_currencies' => 'usd'
            ]
        ]);
        $prices = json_decode($response->getBody(), true);

        $coins = [];
        foreach ($westWalletData as $currency) {
            $coinId = strtolower($currency['name']);
            $price = isset($prices[$coinId]) ? $prices[$coinId]['usd'] : '0.00000000';

            $coins[] = [
                'symbol' => $currency['tickers'][0],
                'name' => $currency['name'],
                'price' => (string)$price,
                'image' => '', // Изображение отсутствует в ответе WestWallet
                'normal_withdrawal_fee' => '0.00000000', // Комиссия отсутствует в ответе WestWallet
                'priority_withdrawal_fee' => '0.00000000', // Приоритетная комиссия отсутствует в ответе WestWallet
                'normal_minimum_withdrawal' => (string)$currency['min_withdraw'],
                'priority_minimum_withdrawal' => (string)$currency['min_withdraw'],
                'minimum_deposit_amount' => (string)$currency['min_receive'],
                'confirmations_required' => '0', // Количество подтверждений отсутствует в ответе WestWallet
                'address_regex' => $currency['address_regex'],
                'require_dest_tag' => $currency['require_dest_tag'],
                'max_withdraw_per_transaction' => $currency['max_withdraw_per_transaction'],
                'max_withdraw_transactions_per_day' => $currency['max_withdraw_transactions_per_day'],
                'active' => $currency['active'],
                'send_active' => $currency['send_active'],
                'receive_active' => $currency['receive_active']
            ];
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'coins' => $coins
        ]);
    }

    
}
