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
            $data['lastSyncAt'] = now()->toIso8601String(); // можно заменить на реальную дату
        }

        return response()->json(['data' => $data]);
    }
}
