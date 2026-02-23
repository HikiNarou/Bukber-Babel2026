<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $allDays     = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $registrations = Registration::all();
        $total       = $registrations->count();

        // Average budget
        $avgBudget   = $total > 0 ? $registrations->avg('budget') : 0;
        $avgBudgetFmt = 'Rp ' . number_format($avgBudget / 1000, 0, ',', '.') . 'rb';

        // Favorite week
        $weekCounts  = [];
        foreach ($registrations as $reg) {
            foreach ($reg->weeks as $week) {
                $weekCounts[$week] = ($weekCounts[$week] ?? 0) + 1;
            }
        }
        arsort($weekCounts);
        $favoriteWeek      = !empty($weekCounts) ? array_key_first($weekCounts) : 1;
        $favoriteWeekCount = $weekCounts[$favoriteWeek] ?? 0;
        $favoriteWeekPct   = $total > 0 ? round($favoriteWeekCount / $total * 100) : 0;

        // Day counts
        $dayCounts = array_fill_keys($allDays, 0);
        foreach ($registrations as $reg) {
            foreach ($reg->days as $day) {
                if (isset($dayCounts[$day])) {
                    $dayCounts[$day]++;
                }
            }
        }
        $maxDayCount  = max(array_values($dayCounts)) ?: 1;
        $majorityDay  = array_search(max($dayCounts), $dayCounts);

        // Majority budget formatted as Xrb
        $majorityBudgetFmt = round($avgBudget / 1000) . 'rb';

        // Recent respondents (last 10)
        $recent = Registration::latest()->take(10)->get();

        // Hours ago (from last registration)
        $lastReg   = Registration::latest()->first();
        $hoursAgo  = $lastReg ? max(0, (int) $lastReg->created_at->diffInHours(now())) : 0;

        // KPI percentages
        $totalPct       = $total > 0 ? min(100, $total * 4) : 0;
        $budgetPct      = $total > 0 ? min(100, round(($avgBudget - 50000) / 450000 * 100)) : 0;
        $weekPct        = $favoriteWeekPct;

        return view('dashboard.index', compact(
            'total', 'avgBudgetFmt', 'favoriteWeek', 'favoriteWeekCount', 'favoriteWeekPct',
            'dayCounts', 'maxDayCount', 'majorityDay', 'majorityBudgetFmt',
            'recent', 'hoursAgo', 'totalPct', 'budgetPct', 'weekPct', 'allDays'
        ));
    }
}
