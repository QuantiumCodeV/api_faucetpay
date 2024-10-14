<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBalance;
use App\Services\WestWalletService;
class UserBalanceController extends Controller
{
    public function ipn(Request $request)
    {
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
        
        return response()->json(['success' => 'Баланс успешно обновлен'], 200);
    }
}
