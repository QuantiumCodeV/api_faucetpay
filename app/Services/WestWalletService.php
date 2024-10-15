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
        $publicKey = config('services.westwallet.public_key');
        $privateKey = config('services.westwallet.private_key');
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
        try {
            $ipn_url = route('westwallet.ipn');
            Log::info($ipn_url);
            Log::info($currency);
            Log::info($user_id);
            $data = array();
            $data['currency'] = $currency;
            $data['ipn_url'] = $ipn_url;
            $data['label'] = $user_id;
            Log::info($data);
            $address = $this->client->generateAddress($currency, $ipn_url, $user_id);
            Log::info($address);
            return $address['address'];
        } catch (CurrencyNotFoundException $e) {
            Log::error('Валюта не найдена: ' . $e->getMessage());
            return "Эта валюта не существует!";
        } catch (\Exception $e) {
            Log::error('Ошибка при генерации адреса: ' . $e);
            return null;
        }
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

        foreach ($currencies as $currency) {
            $addresses[$currency] = $this->generateAddress($currency, $user_id);
        }

        return $addresses;
    }

    public function checkTransaction($transaction_id)
    {
        return $this->client->transactionInfo($transaction_id)['status'] == 'completed';
    }
}
