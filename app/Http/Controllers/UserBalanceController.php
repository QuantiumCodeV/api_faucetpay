<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBalance;
use App\Services\WestWalletService;
use App\Models\Deposit;
class UserBalanceController extends Controller
{
    public function ipn(Request $request)
    {
        // Проверяем IP-адрес запроса
        /*if ($request->ip() !== '5.189.219.250') {
            return response()->json(['error' => 'Недопустимый IP-адрес'], 403);
        }
*/
        $data = $request->input();
        $client = new WestWalletService();
        // Проверяем транзакцию
        if (!$client->checkTransaction($data['id'])) {
            return response()->json(['error' => 'Недействительная транзакция'], 400);
        }
        
        // Получаем пользователя по label (id пользователя)
        $user = User::find($data['label']);
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }
        
        // Находим или создаем баланс пользователя для данной валюты
        $userBalance = UserBalance::firstOrCreate(
            ['user_id' => $user->id, 'currency' => $data['currency']],
            ['balance' => 0]
        );
        
        // Начисляем сумму на баланс пользователя
        $userBalance->balance += $data['amount'];
        $userBalance->save();


        $requestStatistic = new Request();
        $requestStatistic->merge([
            'coin' => $data['currency'],
            'date' => date('Y-m-d'),
            'value' => $data['amount']
        ]);
        $monthlyStatisticController = new MonthlyStatisticController();
        $monthlyStatisticController->addMonthlyStatistics($requestStatistic);

        $deposit = new Deposit();
        $deposit->user_id = $data['label'];
        $deposit->amount = $data['amount'];
        $deposit->coin = $data['currency'];
        $deposit->confirmations = $data['blockchain_confirmations'];
        $deposit->credited = 'credited';
        $deposit->datetime = date('Y-m-d H:i:s');
        $deposit->transaction_id = $data['blockchain_hash'];
        $deposit->save();
        
        
        return response()->json(['success' => 'Баланс успешно обновлен'], 200);
    }
}
