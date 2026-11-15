<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private StatisticsService $statisticsService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month');
        $data = $this->statisticsService->dashboard($request->user(), $period);

        return $this->success([
            'summary' => $data['summary'],
            'trend' => $data['trend'],
            'recent_bills' => TransactionResource::collection($data['recent_bills']),
        ]);
    }
}
