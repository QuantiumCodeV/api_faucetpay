<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Currency;
use App\Models\UserBalance;
use Illuminate\Support\Facades\Hash;
use App\Services\WestWalletService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateUserWallets;
use App\Models\MonthlyStatistic;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:8',
            'user_email' => 'required|email|unique:users,email',
            'user_name' => 'required|string|max:255|unique:users,name',
        ]);

        // Генерация токена верификации
        $verificationToken = bin2hex(random_bytes(16)); // Генерация случайного токена

        $user = User::create([
            'name' => $validatedData['user_name'],
            'email' => $validatedData['user_email'],
            'password' => bcrypt($validatedData['password']),
            'email_verified_at' => null, // Установите в null, так как пользователь еще не подтвержден
            'verification_token' => $verificationToken,
        ]);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать пользователя.',
            ], 200);
        }

        // Запускаем задачу на генерацию кошельков асинхронно
        Queue::push(new GenerateUserWallets($user->id));

        // Отправляем письмо с подтверждением
        try {
            Mail::to($user->email)->send(new VerificationEmail($user));
        } catch (\Exception $e) {
            Log::error('Ошибка отправки письма: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Ваш аккаунт создан. Пожалуйста, проверьте вашу электронную почту для активации аккаунта.',
        ], 200);
    }

    public function getBalances(Request $request)
    {
        $user = $request->user();
        $currencies = Currency::all();
        $coin_balances = [];

        foreach ($currencies as $currency) {
            $balance = UserBalance::firstOrCreate(
                ['user_id' => $user->id, 'currency_id' => $currency->id],
                ['balance' => 0]
            );

            $coin_balances[] = [
                'name' => $currency->name,
                'coin' => json_decode($currency->tickers)[0], // Предполагается, что первый тикер основной
                'image' => strtolower(json_decode($currency->tickers, true)[0]) . ".png",
                'balance' => number_format($balance->balance, 8, '.', ''),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'coins' => $coin_balances,
        ]);
    }


    public function getUserInformation(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'User information successfully retrieved.',
            'user_information' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'referrer' => $user->referrer ?? 0,
                'tfa_authenticated' => $user->tfa_authenticated ?? 1
            ]
        ]);
    }



    public function getSocketToken(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => '',
                'socket_token' => $user->createToken('SocketToken')->plainTextToken
            ]);
        }
    }

    public function get2FADetails(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Успешно получены детали 2FA.',
                'tfa_details' => [
                    'tfa_enabled' => 0,
                    'tfa_key' => ''
                ]
            ]);
        }
    }

    public function changeEmail(Request $request)
    {
        $validatedData = $request->validate([
            'new_email' => 'required|email|unique:users,email',
        ]);

        $user = $request->user();
        if ($user) {
            $user->email = $validatedData['new_email'];
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Email successfully changed.',
            ]);
        }
    }


    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'new_password' => 'required|string|min:8',
            'old_password' => 'required|string|min:8',
        ]);

        $user = $request->user();
        if ($user) {
            if (Hash::check($validatedData['old_password'], $user->password)) {
                $user->password = bcrypt($validatedData['new_password']);
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Password successfully changed.',
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Invalid old password.',
            ]);
        }
    }


    public function createAddress(Request $request)
    {
        $validatedData = $request->validate([
            'currency' => 'required|string',
        ]);

        $westWalletService = new WestWalletService();
        $address = $westWalletService->generateAddress($validatedData['currency'], 1);

        return response()->json([
            'success' => true,
            'message' => 'Address successfully created.',
            'address' => $address,
        ]);
    }


    public function createAddresses(Request $request)
    {
        $user = $request->user();
        $westWalletService = new WestWalletService();
        $addresses = $westWalletService->generateAllAdresses($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Addresses successfully created.',
            'addresses' => $addresses,
        ]);
    }


    public function getInformation(Request $request)
    {
        $user = $request->user();
        $currencies = Currency::all();
        $userBalances = UserBalance::where('user_id', $user->id)->get();

        $coinBalances = [];
        foreach ($currencies as $currency) {
            $balance = $userBalances->where('currency_id', $currency->id)->first();
            $coinBalances[] = [
                'name' => $currency->name,
                'coin' => json_decode($currency->tickers)[0],
                'image' => strtolower(json_decode($currency->tickers, true)[0]) . ".png",
                'price' => "2.00000000",
                'balance' => $balance ? $balance->balance : '0.00000000'
            ];
        }

        $portfolioValue = $userBalances->sum(function ($balance) use ($currencies) {
            $currency = $currencies->where('id', $balance->currency_id)->first();
            return $balance->balance * $currency->price;
        });

        // Эта строка кода получает все платежи пользователя за сегодняшний день.
        // $user->payments() - это отношение, которое возвращает все платежи пользователя.
        // whereDate('created_at', today()) - фильтрует платежи, оставляя только те, которые были созданы сегодня.
        // get() - выполняет запрос и возвращает коллекцию результатов.
        //$todayPayments = $user->payments()->whereDate('created_at', today())->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'coin_balances' => $coinBalances,
                'reward_points' => $user->reward_points,
                'statistics' => [
                    'portfolio_value' => number_format($portfolioValue, 8, '.', ''),
                    'today_usd_payments_received' => number_format(0, 8, '.', ''),
                    'today_payments_received' => 0
                ]
            ]
        ]);
    }

    public function claimDailyRewardPoints(Request $request)
    {
        $user = $request->user();
        if ($user) {
            if ($user->last_reward_claim_date === now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы уже получили ежедневное вознаграждение сегодня! Пожалуйста, возвращайтесь завтра!'
                ]);
            }

            $percentInvestment = env('PROCENT_INVESTION', 0);

            $userBalances = UserBalance::where('user_id', $user->id)->get();
            foreach ($userBalances as $balance) {
                $investmentAmount = $balance->balance * ($percentInvestment / 100);
                $balance->balance += $investmentAmount;
                $balance->save();
            }
            $user->last_reward_claim_date = now()->toDateString();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Вы успешно получили ежедневное вознаграждение и инвестиционный процент!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Пользователь не найден.'
        ]);
    }


    public function getWagerMiningInformation(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => '',
            'pending_fey' => '0.00000000',
            'credited_fey' => '0.09781741',
            'records' => [
                [
                    'datetime' => now()->format('Y-m-d H:i:s'),
                    'total_wagered_usd' => '1.39739',
                    'credited_fey' => '0.09781741'
                ]
            ]
        ]);
    }


    public function getDepositAddress(Request $request)
    {
        $user = $request->user();
        $currency = Currency::where('tickers', 'like', '%' . $request->input('coin') . '%')->first();

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Валюта не найдена.',
                'data' => []
            ]);
        }
        $userBalance = UserBalance::where('user_id', $user->id)->where('currency_id', $currency->id)->first();

        if (!$userBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Баланс пользователя не найден.',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'address' => $userBalance->address,
                'requires_payment_tag' => 0
            ]
        ]);
    }


    public function confirmAccount(Request $request)
    {
        try {
            $token = $request->post('activation_hash');
            $user = User::where('verification_token', $token)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Invalid token.'], 404);
            }

            // Confirm account
            $user->email_verified_at = now(); // Set confirmation date
            $user->verification_token = null; // Remove token after confirmation
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Your account has been activated.',
                'data' => [
                    'hash' => $token
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Account confirmation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred. Please try again.'], 500);
        }
    }
}
