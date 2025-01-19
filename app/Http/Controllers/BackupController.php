<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessDbBackup;
use App\Jobs\ProcessDbRestore;
use App\Models\Database_backups;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $backups = Database_backups::when($request->search, function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->paginate(5);

        return inertia('SuperAdmin/Backups', [
            'backups' => $backups,
        ]);
    }

    public function backup()
    {
        $user_id = Auth::id();
        ProcessDbBackup::dispatch($user_id);

        return redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => 'Database backup process has been started. You will be notified when it completes.',
        ]);
    }

    public function restore($filePath)
    {

        $user_id = Auth::id();

        ProcessDbRestore::dispatch($filePath, $user_id);

        return redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => 'Database restore process has been started. You will be notified when it completes.',
        ]);
    }

    // public function download($filePath)
    // {
    //     // dd($filePath);

    //     $storagePath = "app/backups/" . $filePath;

    //     if (!file_exists($storagePath)) {
    //         abort(404, 'File not found.');
    //     }

    //     // dd($storagePath);

    //     return response()->download($storagePath);
    // }
}
