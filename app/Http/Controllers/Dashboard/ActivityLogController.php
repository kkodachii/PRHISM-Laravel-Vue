<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $activities = Activity::with('causer')
            ->when($user->role_id === 3, function ($query) {
                return $query;
            })
            ->when($user->role_id === 2, function ($query) use ($user) {
                return $query->whereHas('causer', function ($query) use ($user) {
                    $query->where('rhu_id', $user->rhu_id)
                        ->where('role_id', '<>', 3);
                });
            })
            ->when($user->role_id === 1, function ($query) use ($user) {
                return $query->whereHas('causer', function ($query) use ($user) {
                    $query->where('barangay_id', $user->barangay_id);
                });
            })
            ->when($request->search, function ($query) use ($request) {
                return $query->where('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // Map the results to the desired structure
        $activities->getCollection()->transform(function ($activity) {
            $properties = json_decode($activity->properties, true);


            return [
                'name' => $activity->causer ? $activity->causer->name : 'Unknown User',
                'role_id' => $activity->causer ? $activity->causer->role_id : 'N/A',
                'rhu_id' => $activity->causer ? $activity->causer->rhu_id : 'N/A',
                'description' => $activity->description,
                'updated_at' => $activity->updated_at->format('M j, Y g:i a'),
                'type' => $properties['type'] ?? 'N/A',
            ];
        });

        return inertia('DashboardPages/ActivityLogPage', [
            'activitylog' => $activities, // Return the entire paginated object
        ]);
    }
}
