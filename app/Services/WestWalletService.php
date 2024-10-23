<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use WestWallet\WestWallet\Client;
use WestWallet\WestWallet\InsufficientFundsException;
use WestWallet\WestWallet\CurrencyNotFoundException;

class WestWalletService
{
    protected $client;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiKey = env('WESTWALLET_PUBLIC_KEY');
        $this->secretKey = env('WESTWALLET_PRIVATE_KEY');
        $this->client = new Client($this->apiKey, $this->secretKey);
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

    public function getCurrenciesData() {
        $data = [];
        $timestamp = time();
        if (empty($data)) {
            $body = "";
        } else {
            $body = json_encode($data);
        }
        $requestData = json_encode($data, JSON_UNESCAPED_SLASHES);
        
        $request = curl_init("https://api.westwallet.io/wallet/currencies_data");
        
        if ($requestData != "[]") {
        	$hmacMessage = $timestamp.$requestData;
        } else {
        	$hmacMessage = $timestamp;
        }

        $signature = hash_hmac("sha256", $hmacMessage, $this->secretKey);
        curl_setopt($request, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        $headers = array(
            'X-API-KEY: '.$this->apiKey,
            'Content-Type: application/json',
            'X-ACCESS-SIGN: '.$signature,
            'X-ACCESS-TIMESTAMP: '.$timestamp
        );
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($request);
        $responseJson = json_decode($response, TRUE);
        //$this->checkErrors($request, $responseJson);
        curl_close($request);
        if ($responseJson !== FALSE) {
            return $responseJson;
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
            $addresses[$currency] = $this->generateAddress($currency, $user_id);
        }

        return $addresses;
    }

    public function checkTransaction($transaction_id)
    {
        return $this->client->transactionInfo($transaction_id)['status'] == 'completed';
    }
}
