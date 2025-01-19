<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class RecentlyController extends Controller
{
    public function index(Request $request)
    {
        // Get the barangay_id of the authenticated user
        $barangayId = Auth::user()->barangay_id;
    
        // Fetch only "create" type logs from activity log including soft-deleted records
        $recentlylog = Activity::with('causer', 'subject')
            ->whereIn('subject_type', [
                'App\\Models\\Medicines',
                'App\\Models\\Equipments',
                'App\\Models\\Medical_supplies',
            ])
            ->whereHas('causer', function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId); // Filter based on the authenticated user's barangay_id
            })
            ->where('description', 'like', '%Added%') // Only include "create" type logs
            ->with(['subject' => function ($query) {
                $query->withTrashed(); // Include soft-deleted items in related subjects
            }])
            ->orderBy('updated_at', 'desc')
            ->when($request->search, function ($query) use ($request) {
                $search = '%' . $request->search . '%';
    
                $query->where(function ($query) use ($search) {
                    $query->orWhereHas('subject', function ($subQuery) use ($search) {
                        if ($subQuery->getModel() instanceof \App\Models\Medicines) {
                            $subQuery->whereHas('generic_name', function ($query) use ($search) {
                                $query->where('generic_name', 'like', $search);
                            });
                        }
    
                        if ($subQuery->getModel() instanceof \App\Models\Equipments) {
                            $subQuery->where('equipment_name', 'like', $search);
                        }
    
                        if ($subQuery->getModel() instanceof \App\Models\Medical_supplies) {
                            $subQuery->where('med_sup_name', 'like', $search);
                        }
                    });
                });
            })
            ->paginate(10); // Pagination setup
    
        // Deduplicate logs by subject_id and subject_type
        $uniqueLogs = $recentlylog->getCollection()->unique(function ($activity) {
            return $activity->subject_type . '-' . $activity->subject_id;
        });
    
        // Format the recently fetched logs for display
        $formattedRecentlyLog = $uniqueLogs->map(function ($activity) {
            $type = match ($activity->subject_type) {
                'App\\Models\\Medicines' => 'Medicine',
                'App\\Models\\Equipments' => 'Equipment',
                'App\\Models\\Medical_supplies' => 'Medical Supply',
                default => 'Unknown',
            };
    
            $name = match ($type) {
                'Medicine' => $activity->subject->generic_name->generic_name ?? 'Unknown',
                'Equipment' => $activity->subject->equipment_name ?? 'Unknown',
                'Medical Supply' => $activity->subject->med_sup_name ?? 'Unknown',
                default => 'Unknown',
            };
    
            return [
                'type' => $type,
                'name' => $name,
                'date_added' => $activity->updated_at->format('M j, Y g:i a'),
            ];
        });
    
        // Set the newly formatted collection back to the paginated logs
        $recentlylog->setCollection($formattedRecentlyLog);
    
        return inertia('DashboardPages/RecentlyLogPage', [
            'recentlylog' => $recentlylog,
        ]);
    }
    
    
}
