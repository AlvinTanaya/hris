<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

use App\Imports\EmployeesImport;
use App\Models\User;
use App\Models\notification;

use function Laravel\Prompts\note;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('users_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5);
    
        // Get makers with their department and position relationships
        $notificationMakers = User::with(['department', 'position'])
            ->whereIn('id', $notifications->pluck('maker_id'))
            ->get()
            ->keyBy('id');
    
        // Prepare notification data with formatted display titles
        $notifications->transform(function ($notification) use ($notificationMakers) {
            $maker = $notificationMakers[$notification->maker_id] ?? null;
            
            $notification->from_name = $maker->name ?? 'System';
            $notification->from_title = $this->getMakerTitle($maker);
            
            return $notification;
        });
    
        return view('notification.index', compact('notifications', 'notificationMakers'));
    }
    
    protected function getMakerTitle($maker)
    {
        if (!$maker) {
            return 'System';
        }
    
        $positionName = $maker->position_name ?? 'System';
        $departmentName = $maker->department_name ?? 'System';
    
        return ($positionName == $departmentName) ? $positionName : "$positionName - $departmentName";
    }
    public function markAsRead($id)
    {
        $notification = notification::findOrFail($id);

        if ($notification->users_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->status = 'Read';
        $notification->updated_at = now();
        $notification->save();

        return response()->json(['success' => true]);
    }



    public function markAllRead()
    {
        notification::where('users_id', Auth::id())
            ->where('status', 'Unread')
            ->update(['status' => 'Read', 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }
}
