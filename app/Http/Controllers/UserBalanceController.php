<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBalance;
use App\Services\WestWalletService;
use App\Models\Deposit;
use App\Models\Currency;
use Illuminate\Support\Facades\Log;
class UserBalanceController extends Controller
{
    public function ipn(Request $request)
    {
        // Проверяем IP-адрес запроса
        /*if ($request->ip() !== '5.189.219.250') {
            return response()->json(['error' => 'Недопустимый IP-адрес'], 403);
        }
*/
/*
        // Получаем данные из POST-запроса, так как используется application/x-www-form-urlencoded
        $data = [
            'id' => $request->input('id'),
            'status' => $request->input('status'), 
            'label' => $request->input('label'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'blockchain_confirmations' => $request->input('blockchain_confirmations'),
            'blockchain_hash' => $request->input('blockchain_hash')
        ];

        // Проверяем наличие всех необходимых параметров
        $requiredParams = ['id', 'status', 'label', 'currency', 'amount', 'blockchain_confirmations', 'blockchain_hash'];
        foreach ($requiredParams as $param) {
            if (!isset($data[$param])) {
                return response()->json(['error' => "Отсутствует обязательный параметр: {$param}"], 400);
            }
        }

        $client = new WestWalletService();
        // Проверяем транзакцию
        /*if (!$client->checkTransaction($data['id'])) {
            return response()->json(['error' => 'Недействительная транзакция'], 400);
        }*/
/*
        // Проверяем статус транзакции
        if ($data['status'] !== 'completed') {
            return response()->json(['error' => 'Транзакция не завершена'], 400);
        }
        
        // Получаем пользователя по label (id пользователя)
        $user = User::find($data['label']);
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }
        
        // Находим или создаем баланс пользователя для данной валюты
        $currency = Currency::where('tickers', 'like', '%' . $data['currency'] . '%')->first();
        $userBalance = UserBalance::firstOrCreate(
            ['user_id' => $user->id, 'currency_id' => $currency->id],
            ['balance' => 0]
        );
        
        // Начисляем сумму на баланс пользователя
        $userBalance->balance += $data['amount'];
        $userBalance->save();

        // Логируем данные депозита
        Log::info('Deposit received', $data);

        $requestStatistic = new Request();
        $requestStatistic->merge([
            'coin' => $data['currency'],
            'date' => date('Y-m-d'),
            'value' => $data['amount']
        ]);
        $monthlyStatisticController = new MonthlyStatisticController();
        $monthlyStatisticController->addMonthlyStatistics($requestStatistic, $user);

        $deposit = new Deposit();
        $deposit->user_id = $data['label'];
        $deposit->amount = $data['amount'];
        $deposit->coin = $data['currency'];
        $deposit->confirmations = $data['blockchain_confirmations'];
        $deposit->credited = 'credited';
        $deposit->datetime = date('Y-m-d H:i:s');
        $deposit->transaction_id = $data['blockchain_hash'];
        $deposit->save();*/
        
        return response()->json(['success' => 'Баланс успешно обновлен'], 200);
    }
}
