<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function index(Request $request)
    {
        $source = $request->input('source', 'remote');
        $data = [
            'status' => 'ok',
            'source' => $source,
            'checkedAt' => now()->toIso8601String(),
        ];

        if ($source === 'local') {
            // Можно хранить время последней синхронизации в кеше или БД
            $lastSync = cache('last_sync_time', now()->subDay()->toIso8601String());
            $data['lastSyncAt'] = $lastSync;
        }

        return response()->json(['data' => $data]);
    }
}
