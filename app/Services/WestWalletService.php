<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use WestWallet\WestWallet\Client;
use WestWallet\WestWallet\InsufficientFundsException;
use WestWallet\WestWallet\CurrencyNotFoundException;

class WestWalletService
{
    protected $client;

    public function __construct()
    {
        $publicKey = env('WESTWALLET_PUBLIC_KEY');
        $privateKey = env('WESTWALLET_PRIVATE_KEY');
        $this->client = new Client($publicKey, $privateKey);
    }

    public function createWithdrawal($currency, $amount, $address)
    {
        try {
            $tx = $this->client->createWithdrawal($currency, $amount, $address);
            return implode("|", $tx);
        } catch (InsufficientFundsException $e) {
            Log::error('Недостаточно средств для вывода: ' . $e->getMessage());
            return "У вас недостаточно средств для этого вывода";
        } catch (\Exception $e) {
            Log::error('Ошибка при создании вывода: ' . $e->getMessage());
            return null;
        }
    }

    public function generateAddress($currency, $user_id)
    {

        $ipn_url = route('westwallet.ipn');

        $address = $this->client->generateAddress($currency, $ipn_url, strval($user_id));

        return $address['address'];
    }

    public function generateAllAdresses($user_id)
    {

        $currencies = [
            'BTC',
            'ETH',
            'USDT',
            'TRX',
            'USDTTRC',
            'TON',
            'USDTTON',
            'BNB20',
            'USDTBEP',
            'XRP',
            'SOL',
            'LTC',
            'DOGE',
            'XMR',
            'ADA',
            'DASH',
            'BCH',
            'ZEC',
            'NOT',
            'ETC',
            'EOS',
            'XLM',
            'SHIB'
        ];

        $addresses = [];

        foreach ($currencies as $key => $currency) {
            sleep(1);
            $addresses[$key] = $this->generateAddress($currency, $user_id);
        }

        return $addresses;
    }

    public function checkTransaction($transaction_id)
    {
        return $this->client->transactionInfo($transaction_id)['status'] == 'completed';
    }
}
