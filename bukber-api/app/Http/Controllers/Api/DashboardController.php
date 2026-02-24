<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardRespondenRequest;
use App\Http\Resources\DashboardStatsResource;
use App\Http\Resources\PesertaResource;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function stats()
    {
        $data = $this->dashboardService->getStats();

        return $this->success(DashboardStatsResource::make($data), 'Statistik dashboard berhasil diambil.');
    }

    public function chartHari()
    {
        return $this->success($this->dashboardService->getChartHari(), 'Chart hari berhasil diambil.');
    }

    public function chartMinggu()
    {
        return $this->success($this->dashboardService->getChartMinggu(), 'Chart minggu berhasil diambil.');
    }

    public function chartBudget()
    {
        return $this->success($this->dashboardService->getChartBudget(), 'Chart budget berhasil diambil.');
    }

    public function responden(DashboardRespondenRequest $request)
    {
        $perPage = (int) $request->integer('per_page', 20);
        $availability = $request->string('availability')->toString() ?: null;
        $paginator = $this->dashboardService->getResponden($perPage, $availability);

        return $this->paginated(
            $paginator,
            PesertaResource::collection($paginator->getCollection()),
            'Daftar responden berhasil diambil.'
        );
    }
}
