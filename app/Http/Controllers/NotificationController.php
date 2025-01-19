<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medicines;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {

        // Fetch notifications with the user relation (if exists)
        $notifications = Notifications::with('receiver')
        ->where('notifiable_id', Auth::user()->id)
        ->when($request->search, function($query) use ($request) {
            $query->whereHas('receiver', function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(5);


        // Loop through the notifications to decode the data field and attach user info
        foreach ($notifications as $notification) {
            // Decode the JSON data field
            $notificationData = json_decode($notification->data, true);

            if (isset($notificationData['user_id'])) {
                // Retrieve user based on user_id in notification data
                $user = User::select('name', 'email', 'profile_picture', 'role_id', 'barangay_id')
                            ->with(['role:id,name', 'barangay:id,barangay_name'])
                            ->where('id', $notificationData['user_id'])
                            ->first();

                // Attach user info to the notification for access in the view
                $notification->user_info = $user;
            }

            if (isset($notificationData['medicine_id'])) {

                $medicine = Medicines::select('generic_name_id', 'quantity', 'expiration_date')
                ->with('generic_name')
                ->where('id', $notificationData['medicine_id'])
                ->first();

                if ($medicine) {
                // Attach generic_name, quantity, and expiration_date to the notification
                $notification->medicine_info = [
                'generic_name' => $medicine->generic_name->generic_name,
                'quantity' => $medicine->quantity,
                'expiration_date' => $medicine->expiration_date,
                ];
                }

            }


        }

        // dd($notifications);



        return inertia('Notification', [
            'notifications_list' => $notifications
        ]);
    }
    public function markAsRead($id, Request $request)
    {
        // Find the notification by ID for the authenticated user
        $notification = $request->user()->notifications()->find($id);

        // Check if the notification exists
        if ($notification) {
            $notification->markAsRead();
            return redirect()->route('notifications.index');
        }
        else{
            return redirect()->back()->with('toast', [
                'type' => 'danger',
                'message' => 'Notification not found',
            ]); // If not found, return 404
        }
    }

    public function markAllAsRead()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Mark all unread notifications as read
        $user->unreadNotifications->markAsRead();

        // Optionally, return a response or redirect back
        return redirect()->back()->with('toast', ['message' => 'All notifications marked as read!', 'type' => 'success']);
    }

}
