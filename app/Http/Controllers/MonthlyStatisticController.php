<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyStatistic;
use App\Models\User;
class MonthlyStatisticController extends Controller
{
    public function addMonthlyStatistics(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'coin' => 'required|string',
            'date' => 'required|date',
            'value' => 'required|numeric',
        ]);


        // Создаем новую запись в таблице monthly_statistics
        MonthlyStatistic::create([
            'user_id' => $user->id, // Сохраняем user_id
            'coin' => $validatedData['coin'],
            'date' => $validatedData['date'],
            'value' => $validatedData['value'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Статистика успешно добавлена.',
        ]);
    }

    public function getMonthlyStatistics(Request $request)
    {
        $validatedData = $request->validate([
            'coin' => 'required|string', // Валидация входящего параметра coin
        ]);

        $coin = $validatedData['coin'];

        // Получаем текущую дату и дату месяц назад
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subMonth()->format('Y-m-d');

        // Создаем массив для хранения статистики
        $monthlyStatistics = [];

        // Цикл для каждого дня в последнем месяце
        for ($date = $startDate; $date <= $endDate; $date = (new \DateTime($date))->modify('+1 day')->format('Y-m-d')) {
            // Ищем статистику для конкретного дня
            $stat = MonthlyStatistic::where('coin', $coin)
                ->where('date', $date)
                ->first();

            // Если статистика не найдена, создаем пустую запись
            if (!$stat) {
                $monthlyStatistics[] = [
                    'date' => $date,
                    'value' => '0.00000000',
                ];
            } else {
                // Если статистика найдена, добавляем ее в массив
                $monthlyStatistics[] = [
                    'date' => $stat->date,
                    'value' => number_format($stat->value, 8, '.', ''),
                ];
            }
        }

        // Подсчитываем агрегированное значение
        $aggregate = array_sum(array_column($monthlyStatistics, 'value'));

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'monthly_statistics' => $monthlyStatistics,
                'aggregate' => number_format($aggregate, 8, '.', ''),
            ],
        ]);
    }
}
