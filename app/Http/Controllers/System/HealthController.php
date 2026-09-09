<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Lightweight liveness endpoint used by smoke tests, orchestration and
 * monitoring. Verifies the application and database connection in one call.
 */
class HealthController extends Controller
{
    public function __invoke()
    {
        $database = true;

        try {
            DB::select('SELECT 1');
        } catch (\Throwable) {
            $database = false;
        }

        return response()->json([
            'status' => $database ? 'ok' : 'degraded',
            'service' => config('app.name'),
            'database' => $database ? 'up' : 'down',
            'time' => now()->toIso8601String(),
        ], $database ? 200 : 503);
    }
}
