<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class HealthController extends Controller
{
    public function index()
    {
        $configuredSource = config('app.api_source', 'remote');
        $source = in_array($configuredSource, ['remote', 'local'], true) ? $configuredSource : 'remote';
        $data = [
            'status' => 'ok',
            'source' => $source,
            'checkedAt' => now()->toIso8601String(),
        ];

        if ($source === 'local') {
            $lastSync = cache('last_sync_time');

            if (is_string($lastSync)) {
                $data['lastSyncAt'] = $lastSync;
            }
        }

        return response()->json(['data' => $data]);
    }
}
