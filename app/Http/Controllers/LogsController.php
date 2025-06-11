<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    // public function getLogs(Request $request)
    // {
    //     $logs = ActivityLog::with(['user:id,fname,lname']) 
    //         ->select('user_id', 'action', 'created_at')    
    //         ->latest()
    //         ->paginate(10);

    //     $transformedLogs = $logs->map(function ($log) {
    //         return [
    //             'first_name' => $log->user->fname,  
    //             'last_name'  => $log->user->lname,
    //             'action'     => $log->action,
    //             'timestamp'  => $log->created_at->toDateTimeString(),
    //         ];
    //     });

    //     return response()->json($transformedLogs);
    // }

    public function getLogs(Request $request)
    {
        // bali dito 100 data lang 
        $logsToDelete = ActivityLog::orderBy('created_at', 'asc') 
            ->skip(100)
            ->take(PHP_INT_MAX) 
            ->get();

        foreach ($logsToDelete as $log) {
            $log->delete(); // dedelete nya yung old logs renze kapag sumobra na ng 100
        }

        $logs = ActivityLog::with(['user:id,fname,lname,role'])
            ->select('user_id', 'action', 'created_at')
            ->latest()
            ->paginate();

        $transformedLogs = $logs->map(function ($log) {
            return [
                'first_name' => $log->user->fname,
                'last_name'  => $log->user->lname,
                'role'       => $log->user->role, 
                'action'     => $log->action,
                'timestamp'  => $log->created_at->toDateTimeString(),
            ];
        });

        return response()->json($transformedLogs);
    }

}

