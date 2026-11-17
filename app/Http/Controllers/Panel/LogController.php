<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        return view('panel.log', [
            'log' => ActivityLog::with('user')
                ->when($request->query('cari'), fn ($q, $c) => $q->where('aksi', 'like', "%{$c}%")
                    ->orWhere('subjek', 'like', "%{$c}%")
                    ->orWhere('deskripsi', 'like', "%{$c}%"))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
            'filter' => $request->only('cari'),
        ]);
    }
}
