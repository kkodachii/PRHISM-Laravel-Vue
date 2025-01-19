<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requests;
use App\Models\Barangays;
use App\Models\Medicines;
use App\Models\Deliveries;
use App\Models\Equipments;
use App\Notifications\RequestClaimedNotification;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Models\Return_reasons;
use App\Models\Medical_supplies;
use App\Models\Delivery_problems;
use App\Models\Delivery_medicines;
use Illuminate\Support\Facades\DB;
use App\Events\DeliveryReportEvent;
use App\Events\DeliveryReturnEvent;
use App\Events\RequestClaimedEvent;
use App\Models\Delivery_equipments;
use App\Models\Midwife_inventories;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\RequestDeliveredEvent;
use App\Models\Delivery_med_supplies;
use App\Events\RequestOnDeliveryEvent;
use App\Models\Midwife_medical_supplies;
use App\Notifications\DeliveryReportNotification;
use App\Notifications\DeliveryReturnNotification;
use App\Notifications\RequestDeliveredNotification;
use App\Notifications\RequestOnDeliveryNotification;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userRhuId = Auth::user()->rhu_id;
        $user = Auth::user();
        $userRole = $user->role_id;
        $sortField = $request->input('sort', 'status');
        $sortDirection = $request->input('direction', 'asc');

        // Fetch requests with conditions and sorting
        $requests = Requests::with('requester_user', 'barangay')
            ->where('rhu_id', $userRhuId)
            ->when($request->input('status'), function ($query) use ($request) {
                $status = $request->input('status');
                $query->where(function ($query) use ($status) {
                    if ($status === 'pending') {
                        $query->orWhere('status', 'Pending');
                    }
                    if ($status === 'ongoing') {
                        $query->orWhere('status', 'Ongoing');
                    }
                    if ($status === 'approved') {
                        $query->orWhere('status', 'like', '%Approved');
                    }
                    if ($status === 'delivered') {
                        $query->orWhere('status', 'Delivered');
                    }
                    if ($status === 'delivered with report') {
                        $query->orWhere('status', 'Delivered with Report');
                    }
                    if ($status === 'rejected') {
                        $query->orWhere('status', 'Rejected');
                    }
                    if ($status === 'returned') {
                        $query->orWhere('status', 'Returned');
                    }
                    if ($status === 'report') {
                        $query->orWhere('status', 'Delivered with Report');
                    }
                    if ($status === 'claimed') {
                        $query->orWhere('status', 'Claimed');
                    }
                });
            })
            ->when($sortField == 'requested_at', function ($query) use ($sortDirection) {
                $query->orderBy('requested_at', $sortDirection);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // Attach requested and delivered items to each request
        $requests->each(function ($req) {
            $req->requestedItems = $req->getRequestedItems();

            if ($req->status === 'Delivered' || $req->status === 'Returned' || $req->status === 'Claimed' || $req->status === 'Delivered with Report') {
                $delivery = $req->delivery;

                if ($delivery) {
                    $req->deliveredItems = [
                        'medicines' => $delivery->medicineDeliveries->map(function ($medicine) {
                            return [
                                'name' => $medicine->generic_name,
                                'quantity' => $medicine->quantity,
                            ];
                        })->toArray(),

                        'equipments' => $delivery->equipmentDeliveries->map(function ($equipment) {
                            return [
                                'name' => $equipment->equipment_name,
                                'quantity' => $equipment->quantity,
                            ];
                        })->toArray(),

                        'supplies' => $delivery->supplyDeliveries->map(function ($supply) {
                            return [
                                'name' => $supply->med_sup_name,
                                'quantity' => $supply->quantity,
                            ];
                        })->toArray(),
                    ];
                    // Check for a related delivery problem
                    $deliveryProblem = Delivery_problems::where('delivery_id', $delivery->id)->first();
                    if ($deliveryProblem) {
                        $req->deliveryProblem = [
                            'incorrect_quantity' => json_decode($deliveryProblem->incorrect_quantity),
                            'broken_equipment' => json_decode($deliveryProblem->broken_equipment),
                            'wrong_supply_text' => $deliveryProblem->wrong_supply_text,
                            'other_text' => $deliveryProblem->other_text,
                            'package_issue' => $deliveryProblem->package_issue,
                        ];
                    }
                }
            }

            // Insert return reason if the status is "Returned"
            if ($req->status === 'Returned') {
                $returnReason = Return_reasons::where('request_id', $req->id)->first();
                $req->returnReason = $returnReason ? $returnReason->reason : null;
                $req->returnDate = $returnReason ? $returnReason->created_at : null;
            }
        });

        $ongoingRequests = Requests::with('requester_user', 'barangay')->whereIn('status', ['Delivery Approved', 'Pickup Approved', 'Ongoing'])->get();
        $ongoingRequests->each(function ($req) {
            $req->requestedItems = $req->getRequestedItems();

            $delivery = $req->delivery;

                if ($delivery) {
                    $req->deliveredItems = [
                        'medicines' => $delivery->medicineDeliveries->map(function ($medicine) {
                            return [
                                'name' => $medicine->generic_name,
                                'quantity' => $medicine->quantity,
                            ];
                        })->toArray(),

                        'equipments' => $delivery->equipmentDeliveries->map(function ($equipment) {
                            return [
                                'name' => $equipment->equipment_name,
                                'quantity' => $equipment->quantity,
                            ];
                        })->toArray(),

                        'supplies' => $delivery->supplyDeliveries->map(function ($supply) {
                            return [
                                'name' => $supply->med_sup_name,
                                'quantity' => $supply->quantity,
                            ];
                        })->toArray(),
                    ];
                }

        });

        // Fetch ongoing deliveries excluding requests marked as Delivered
        $deliveries = Deliveries::with(['barangay', 'medicineDeliveries', 'equipmentDeliveries', 'supplyDeliveries'])
            ->whereHas('request', function ($query) {
                $query->whereNotIn('status', ['Delivered', 'Returned', 'Delivered with Report', 'Claimed']);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('delivery_name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('date_delivery', 'desc')
            ->paginate(1, ['*'], 'ongoing_delivery');

        $deliveries->each(function ($delivery) {
            $delivery->deliveryItems = [
                'medicines' => $delivery->medicineDeliveries->map(function ($medicine) {
                    return [
                        'name' => $medicine->generic_name,
                        'quantity' => $medicine->quantity,
                    ];
                })->toArray(),

                'equipments' => $delivery->equipmentDeliveries->map(function ($equipment) {
                    return [
                        'name' => $equipment->equipment_name,
                        'quantity' => $equipment->quantity,
                    ];
                })->toArray(),

                'supplies' => $delivery->supplyDeliveries->map(function ($supply) {
                    return [
                        'name' => $supply->med_sup_name,
                        'quantity' => $supply->quantity,
                    ];
                })->toArray(),
            ];
        });

        $statusCounts = null;

        if ($userRole == 3) { // Super Admin: Show all counts
            $statusCounts = Requests::selectRaw("SUM(status = 'Ongoing') as ongoing_count")
                ->selectRaw("SUM(status = 'Pending') as pending_count")
                ->selectRaw("SUM(status = 'Rejected') as rejected_count")
                ->selectRaw("SUM(status = 'Delivered') as delivered_count")
                ->selectRaw("SUM(status = 'Delivered with Report') as reported_count")
                ->first();
        } elseif ($userRole == 2) { // RHU Admin: Show counts based on rhu_id
            $statusCounts = Requests::where('rhu_id', $userRhuId)
                ->selectRaw("SUM(status = 'Ongoing') as ongoing_count")
                ->selectRaw("SUM(status = 'Pending') as pending_count")
                ->selectRaw("SUM(status = 'Rejected') as rejected_count")
                ->selectRaw("SUM(status = 'Delivered') as delivered_count")
                ->selectRaw("SUM(status = 'Delivered with Report') as reported_count")
                ->first();
        }

        return inertia('Delivery', [
            'requests' => $requests,
            'ongoingDeliveries' => $deliveries,
            'ongoingRequests' => $ongoingRequests,
            'statusCounts' => $userRole != 1 ? $statusCounts : null,
        ]);
    }

    public function update(Request $request, $requestId)
    {
        // Find the request and update its status to "Delivered"
        $requestEntry = requests::findOrFail($requestId);
        $requestEntry->status = 'Ongoing';
        $requestEntry->save();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($requestEntry)
            ->withProperties(['Request' => $requestEntry, 'type' => 'Request'])
            ->log('Mark request On Delivery');

        $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

        $deliverId = $delivery->id;

        $requester = User::where('id', $requestEntry->requester_user_id)->get()->first();

        $requester->notify(new RequestOnDeliveryNotification($requestEntry, $deliverId));
        $notification = $requester->notifications()->latest()->first();
        broadcast(new RequestOnDeliveryEvent($notification, $requestEntry, $deliverId));


        return redirect()->back()->with('toast', ['message' => 'Request has been confirmed to be On Delivery']);
    }

    public function returnDelivery(Request $request, $requestId)
    {
        $returnReason = $request->input('returnReason');

        // Find the request and update its status to "Delivered"
        $requestEntry = requests::findOrFail($requestId);

        // Find the delivery entry using the request_id
        $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

        $requestEntry->status = 'Returned';
        $requestEntry->save();
        $delivery->date_delivered = now();
        $delivery->save();

        Return_reasons::create([
            'request_id' => $requestId,
            'reason' => $returnReason,
        ]);

        $deliveryMedicines = delivery_medicines::where('delivery_id', $delivery->id)
            ->where('status', null)->get();

        $deliveryEquipments = delivery_equipments::where('delivery_id', $delivery->id)
            ->where('status', null)->get();

        $deliveryMedSupplies = delivery_med_supplies::where('delivery_id', $delivery->id)
            ->where('status', null)->get();

        if ($deliveryMedicines->isNotEmpty()) {
            $this->processReturnMedicines($requestEntry, $deliveryMedicines);
        }

        if ($deliveryEquipments->isNotEmpty()) {
            $this->processReturnEquipments($requestEntry, $deliveryEquipments);
        }

        if ($deliveryMedSupplies->isNotEmpty()) {
            $this->processReturnMedicalSupplies($requestEntry, $deliveryMedSupplies);
        }

        $deliverId = $delivery->id;

        $rhuAdminsAndSuperAdmins = User::whereIn('role_id', [2, 3])
            ->where('rhu_id', Auth::user()->rhu_id)
            ->get();

        $barangay_user = User::find(Auth::user()->id);
        $barangay = Barangays::find($barangay_user->barangay_id);
        $barangay_name = $barangay ? $barangay->barangay_name : 'Unknown Barangay';

        foreach ($rhuAdminsAndSuperAdmins as $rhuUser) {
            $rhuUser->notify(new DeliveryReturnNotification($requestEntry,  $barangay_name, $returnReason));
            $notification = $rhuUser->notifications()->latest()->first();
            broadcast(new DeliveryReturnEvent($notification, $requestEntry,  $barangay_name, $returnReason));
        }


        return redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => 'Delivery has been marked as Returned',
        ]);
    }

    public function reportDelivery(Request $request, $requestId)
    {
        $userRequest = Requests::findOrFail($requestId);

        if ($userRequest->status == "Delivered") {
            return redirect()->route('dashboard')->with('toast', [
                'type' => 'warning',
                'message' => 'Delivery has already been confirmed.',
            ]);
        }

        $inventories = Midwife_inventories::where('request_id', $requestId)->get();

        $medicines = Midwife_inventories::where('request_id', $requestId)->where('type', 'medicine')->get();
        $equipments = Midwife_inventories::where('request_id', $requestId)->where('type', 'equipment')->get();
        $medical_supplies = Midwife_inventories::where('request_id', $requestId)->where('type', 'medical_supply')->get();

        return inertia('Requests/ReportDelivery', [
            'requests' => $userRequest,
            'inventories' => $inventories,
            'medicines' => $medicines,
            'equipments' => $equipments,
            'medical_supplies' => $medical_supplies,
        ]);
    }

    public function saveToInventory(Request $request, $requestId)
    {
        // Find the request and update its status to "Delivered"
        $requestEntry = requests::findOrFail($requestId);

        // Find the delivery entry using the request_id
        $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

        $deliveryMedicines = delivery_medicines::where('delivery_id', $delivery->id)->where('status', null)->get();
        $deliveryEquipments = delivery_equipments::where('delivery_id', $delivery->id)->where('status', null)->get();
        $deliveryMedSupplies = delivery_med_supplies::where('delivery_id', $delivery->id)->where('status', null)->get();

        if ($deliveryMedicines->isNotEmpty()) {
            $this->processMedicines($requestEntry, $deliveryMedicines);
        }

        if ($deliveryEquipments->isNotEmpty()) {
            $this->processEquipments($requestEntry, $deliveryEquipments);
        }

        if ($deliveryMedSupplies->isNotEmpty()) {
            $this->processMedicalSupplies($requestEntry, $deliveryMedSupplies);
        }

        return redirect()->route('report-delivery', ['id' => $requestId]);
    }

    public function submitReport(Request $request)
{
    // Validate the incoming request
    $validated = $request->validate([
        'reports' => 'required|array',
    ]);

    $requestId = $request->input('request_id');
    $requestEntry = requests::findOrFail($requestId);
    $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

    // Begin a transaction to ensure atomicity
    DB::beginTransaction();

    try {
        $incorrectQuantities = [];

        // Filter broken equipment entries directly from the request and reformat them as required
        $filteredBrokenEquipment = array_values(array_filter($request->broken_equipment ?? [], function ($item) {
            return !empty($item['equipment']) && !empty($item['quantity']);
        }));

        // Reformat $filteredBrokenEquipment into the desired JSON structure
        $formattedBrokenEquipment = array_map(function ($item) {
            return [
                'equipment' => $item['equipment'],
                'quantity' => $item['quantity'],
            ];
        }, $filteredBrokenEquipment);

        // Process the reports and update inventory accordingly
        foreach ($request->reports as $report) {
            // Skip processing if supply_id is missing or empty
            if (empty($report['supply_id'])) {
                continue;
            }

            $supply = Midwife_inventories::find($report['supply_id']);
            if ($supply) {
                $incorrectQuantities[] = [
                    'supply' => $supply->name,
                    'quantity' => $report['quantity'],
                ];

                // Update the inventory for medicine or supplies
                $supply->quantity -= $report['quantity']; // Reduce the quantity
                $supply->save();
            }
        }

        // Initialize the data to store in the 'delivery_problems' table
        $deliveryProblemData = [
            'delivery_id' => $delivery->id,
            'rhu_id' => Auth::user()->rhu_id,
            'barangay_id' => Auth::user()->barangay_id,
            'broken_equipment' => json_encode($formattedBrokenEquipment), // Store formatted broken equipment as JSON
            'incorrect_quantity' => json_encode($incorrectQuantities), // Store incorrect quantities as JSON
            'wrong_supply_text' => $request->wrong_supply_text ?? null,
            'other_text' => $request->other_text ?? null,
        ];


            // Save the delivery problem data
            $deliveryProblem = Delivery_problems::create($deliveryProblemData);

            // Commit the transaction
            DB::commit();

            // Send notifications
            $deliverId = $delivery->id;
            $rhuAdminsAndSuperAdmins = User::whereIn('role_id', [2, 3])
                ->where('rhu_id', Auth::user()->rhu_id)
                ->get();
            $barangay_user = User::find(Auth::user()->id);
            $barangay = Barangays::find($barangay_user->barangay_id);
            $barangay_name = $barangay ? $barangay->barangay_name : 'Unknown Barangay';

            foreach ($rhuAdminsAndSuperAdmins as $rhuUser) {
                $rhuUser->notify(new DeliveryReportNotification($requestEntry, $deliverId, $barangay_user, $barangay_name));
                $notification = $rhuUser->notifications()->latest()->first();
                broadcast(new DeliveryReportEvent($notification, $requestEntry, $deliverId, $rhuUser, $barangay_user, $barangay_name));
            }

            $requestEntry->status = 'Delivered with Report';
            $requestEntry->save();
            $delivery->date_delivered = now();
            $delivery->save();

            return  redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => 'Delivery has been reported',]);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to submit report: ' . $e->getMessage()]);
        }
    }

    public function markAsDelivered(Request $request, $requestId)
    {
        // Find the request and update its status to "Delivered"
        $requestEntry = requests::findOrFail($requestId);

        // Find the delivery entry using the request_id
        $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

        $deliveryMedicines = delivery_medicines::where('delivery_id', $delivery->id)->get();
        $deliveryEquipments = delivery_equipments::where('delivery_id', $delivery->id)->get();
        $deliveryMedSupplies = delivery_med_supplies::where('delivery_id', $delivery->id)->get();

        if ($deliveryMedicines->isNotEmpty()) {
            $this->processMedicines($requestEntry, $deliveryMedicines);
        }

        if ($deliveryEquipments->isNotEmpty()) {
            $this->processEquipments($requestEntry, $deliveryEquipments);
        }

        if ($deliveryMedSupplies->isNotEmpty()) {
            $this->processMedicalSupplies($requestEntry, $deliveryMedSupplies);
        }

        $requestEntry->status = 'Delivered';
        $requestEntry->save();
        $delivery->date_delivered = now();
        $delivery->save();

        $deliverId = $delivery->id;

        $rhuAdminsAndSuperAdmins = User::whereIn('role_id', [2, 3])
            ->where('rhu_id', Auth::user()->rhu_id)
            ->get();

        $barangay_user = User::find(Auth::user()->id);
        $barangay = Barangays::find($barangay_user->barangay_id);
        $barangay_name = $barangay ? $barangay->barangay_name : 'Unknown Barangay';

        foreach ($rhuAdminsAndSuperAdmins as $rhuUser) {
            $rhuUser->notify(new RequestDeliveredNotification($requestEntry, $deliverId, $barangay_user, $barangay_name));
            $notification = $rhuUser->notifications()->latest()->first();
            broadcast(new RequestDeliveredEvent($notification, $requestEntry, $deliverId, $rhuUser,  $barangay_user, $barangay_name));
        }

        return redirect()->back()->with('toast', ['message' => 'Delivery marked as Delivered and inventory updated.']);
    }

    public function markAsClaimed(Request $request, $requestId)
    {
        // Find the request and update its status to "Delivered"
        $requestEntry = requests::findOrFail($requestId);

        // Find the delivery entry using the request_id
        $delivery = deliveries::where('request_id', $requestId)->firstOrFail();

        $deliveryMedicines = delivery_medicines::where('delivery_id', $delivery->id)->get();
        $deliveryEquipments = delivery_equipments::where('delivery_id', $delivery->id)->get();
        $deliveryMedSupplies = delivery_med_supplies::where('delivery_id', $delivery->id)->get();

        if ($deliveryMedicines->isNotEmpty()) {
            $this->processMedicines($requestEntry, $deliveryMedicines);
        }

        if ($deliveryEquipments->isNotEmpty()) {
            $this->processEquipments($requestEntry, $deliveryEquipments);
        }

        if ($deliveryMedSupplies->isNotEmpty()) {
            $this->processMedicalSupplies($requestEntry, $deliveryMedSupplies);
        }

        $requestEntry->status = 'Claimed';
        $requestEntry->save();
        $delivery->date_delivered = now();
        $delivery->save();

        $deliverId = $delivery->id;
        $requester_user = User::find($requestEntry->requester_user_id);

        $requester_user->notify(new RequestClaimedNotification($requestEntry, $deliverId, $requester_user));
        $notification = $requester_user->notifications()->latest()->first();
        broadcast(new RequestClaimedEvent($notification, $requestEntry, $deliverId, $requester_user));

        return redirect()->back()->with('toast', ['type' => 'success','message' => 'Supplies Pickup mark as Claimed.']);
    }

    private function processMedicines($requestEntry, $deliveryMedicines)
    {

        if ($deliveryMedicines->isEmpty()) {
            return;
        }

        $originalMedicineData = null;

        // Process medicines
        foreach ($deliveryMedicines as $deliveryMedicine) {

            Log::info('Processing medicine', ['medicine_id' => $deliveryMedicine->medicine_id, 'quantity' => $deliveryMedicine->quantity]);

            $adminMedicine = medicines::where('id', $deliveryMedicine->medicine_id)->firstOrFail();
            $genericName = generic_names::where('id', $adminMedicine->generic_name_id)->firstOrFail();

            $originalMedicineData = $adminMedicine->toArray();
            // Deduct the quantity and remove if zero
            $adminMedicine->quantity -= $deliveryMedicine->quantity;
            $adminMedicine->reserved = 0;

            if ($adminMedicine->quantity <= 0) {
                $adminMedicine->delete();
            } else {
                // Recompute status based on quantity and expiry date
                $statuses = [];
                $currentDate = \Carbon\Carbon::now();
                $expiryDate = \Carbon\Carbon::parse($adminMedicine->expiration_date);

                if ($expiryDate < $currentDate) {
                    $statuses[] = "Expired";
                } elseif ($adminMedicine->quantity === 0) {
                    $statuses[] = "Out of stock";
                } elseif ($adminMedicine->quantity <= 20) {
                    $statuses[] = "Low on stock";
                } else {
                    $statuses[] = "On stock";
                }

                $threeMonthsFromAcquiredDate = \Carbon\Carbon::parse($adminMedicine->date_acquired)->addMonths(3);
                if ($expiryDate <= $threeMonthsFromAcquiredDate) {
                    $statuses[] = "Expiring";
                }

                // Join statuses and update the medicine's status
                $adminMedicine->status = implode(', ', $statuses);
                $adminMedicine->save();
            }

            if (in_array($requestEntry->barangay_id, [2, 3])) {
                medicines::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'batch_id' => $adminMedicine->batch_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'generic_name_id' => $adminMedicine->generic_name_id,
                    'quantity' => $deliveryMedicine->quantity,
                    'reserved' => 0,
                    'provider' => $adminMedicine->provider,
                    'brand' => $adminMedicine->brand,
                    'category_id' => $adminMedicine->category_id,
                    'expiration_date' => $adminMedicine->expiration_date,
                    'status' => $adminMedicine->status,
                    'date_acquired' => \Carbon\Carbon::now(),
                ]);
            } else {
                // Compute status for midwife inventory based on quantity and expiration date
                $midwifeStatuses = [];
                $currentDate = \Carbon\Carbon::now();
                $expiryDate = \Carbon\Carbon::parse($adminMedicine->expiration_date);

                if ($expiryDate < $currentDate) {
                    $midwifeStatuses[] = "Expired";
                } elseif ($adminMedicine->quantity === 0) {
                    $midwifeStatuses[] = "Out of stock";
                } elseif ($adminMedicine->quantity <= 20) {
                    $midwifeStatuses[] = "Low on stock";
                } else {
                    $midwifeStatuses[] = "On stock";
                }

                $threeMonthsFromAcquiredDate = \Carbon\Carbon::parse($adminMedicine->date_acquired)->addMonths(3);
                if ($expiryDate <= $threeMonthsFromAcquiredDate) {
                    $midwifeStatuses[] = "Expiring";
                }

                // Join statuses for midwife inventory
                $midwifeStatusString = implode(', ', $midwifeStatuses);

                // Add to midwife inventory
                $newMidwifeInventory = midwife_inventories::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'rhu_id' => $requestEntry->rhu_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'type' => 'medicine',
                    'name' => $genericName->generic_name,
                    'quantity' => $deliveryMedicine->quantity,
                    'description' => $adminMedicine->description ?? 'N/A',
                    'brand' => $adminMedicine->brand,
                    'category_id' => $adminMedicine->category_id,
                    'expiration_date' => $adminMedicine->expiration_date,
                    'request_id' => $requestEntry->id,
                    'status' => $midwifeStatusString,
                ]);

            activity()
            ->causedBy(Auth::user())
            ->performedOn($requestEntry)
            ->withProperties(['old' => $originalMedicineData, 'new' => $newMidwifeInventory->toArray(), 'type' => 'Delivery'])
            ->log('Medicine request processed successfully.');

            }

            $deliveryMedicine->status = 'Delivered';
            $deliveryMedicine->save();
        }
    }

    private function processEquipments($requestEntry, $deliveryEquipments)
    {

        if ($deliveryEquipments->isEmpty()) {
            return;
        }

        $originalEquipmentData = null;
        // Process equipment
        foreach ($deliveryEquipments as $deliveryEquipment) {
            $adminEquipment = equipments::where('id', $deliveryEquipment->equipment_id)->firstOrFail();
            $originalEquipmentData = $adminEquipment->toArray();
            $adminEquipment->quantity -= $deliveryEquipment->quantity;
            $adminEquipment->reserved = 0;

            if ($adminEquipment->quantity <= 0) {
                $adminEquipment->delete();
            } else {
                $adminEquipment->save();
            }


            if (in_array($requestEntry->barangay_id, [2, 3])) {
                equipments::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'batch_id' => $adminEquipment->batch_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'equipment_name' => $adminEquipment->equipment_name,
                    'quantity' => $deliveryEquipment->quantity,
                    'reserved' => $adminEquipment->reserved,
                    'description' => $adminEquipment->description,
                    'provider' => $adminEquipment->provider,
                    'status' => $adminEquipment->status,
                    'date_acquired' => \Carbon\Carbon::now(),
                    'accountable' => "",
                ]);
            } else {
                // Add to midwife inventory
                $newMidwifeInventory = midwife_inventories::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'rhu_id' => $requestEntry->rhu_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'type' => 'equipment',
                    'name' => $adminEquipment->equipment_name,
                    'quantity' => $deliveryEquipment->quantity,
                    'description' => $adminEquipment->description,
                    'status' => $adminEquipment->status,
                    'request_id' => $requestEntry->id,
                ]);


            activity()
            ->causedBy(Auth::user())
            ->performedOn($requestEntry)
            ->withProperties(['old' => $originalEquipmentData, 'new' => $newMidwifeInventory->toArray(), 'type' => 'Delivery'])
            ->log('Equipment request processed successfully.');

            }

            $deliveryEquipment->status = 'Delivered';
            $deliveryEquipment->save();
        }
    }

    private function processMedicalSupplies($requestEntry, $deliveryMedSupplies)
    {

        if ($deliveryMedSupplies->isEmpty()) {
            return;
        }

        $originalMedSupplyData = null;
        // Process medical supplies
        foreach ($deliveryMedSupplies as $deliveryMedSupply) {
            $adminMedSupply = medical_supplies::where('id', $deliveryMedSupply->med_sup_id)->first();
            $originalMedSupplyData = $adminMedSupply->toArray();

            // Adjust quantity and reset reserved amount
            $adminMedSupply->quantity -= $deliveryMedSupply->quantity;
            $adminMedSupply->reserved = 0;

            // Compute status for the main inventory (medical_supplies)
            $mainInventoryStatuses = [];
            if ($adminMedSupply->quantity === 0) {
                $mainInventoryStatuses[] = "Out of stock";
            } elseif ($adminMedSupply->quantity <= 20) {
                $mainInventoryStatuses[] = "Low on stock";
            } else {
                $mainInventoryStatuses[] = "On stock";
            }
            $mainInventoryStatusString = implode(', ', $mainInventoryStatuses);

            if ($adminMedSupply->quantity <= 0) {
                $adminMedSupply->delete();
            } else {
                // Update the main inventory with the computed status
                $adminMedSupply->status = $mainInventoryStatusString;
                $adminMedSupply->save();
            }

            // Determine if the delivery is for barangay or midwife inventory
            if (in_array($requestEntry->barangay_id, [2, 3])) {
                // For Barangay Inventory
                medical_supplies::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'batch_id' => $adminMedSupply->batch_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'equipment_name' => $adminMedSupply->equipment_name,
                    'quantity' => $deliveryMedSupply->quantity,
                    'reserved' => $adminMedSupply->reserved,
                    'description' => $adminMedSupply->description,
                    'provider' => $adminMedSupply->provider,
                    'status' => $mainInventoryStatusString, // Use the main inventory status
                    'date_acquired' => \Carbon\Carbon::now(),
                ]);
            } else {
                // Compute status specifically for midwife inventory
                $midwifeInventoryStatuses = [];
                if ($deliveryMedSupply->quantity === 0) {
                    $midwifeInventoryStatuses[] = "Out of stock";
                } elseif ($deliveryMedSupply->quantity <= 20) {  // Lower threshold for midwife inventory
                    $midwifeInventoryStatuses[] = "Low on stock";
                } else {
                    $midwifeInventoryStatuses[] = "On stock";
                }
                $midwifeInventoryStatusString = implode(', ', $midwifeInventoryStatuses);

                // Add to midwife inventory with the separate computed status
                $newMidwifeInventory =  Midwife_inventories::create([
                    'user_id' => $requestEntry->requester_user_id,
                    'rhu_id' => $requestEntry->rhu_id,
                    'barangay_id' => $requestEntry->barangay_id,
                    'type' => 'medical_supply',
                    'name' => $adminMedSupply->med_sup_name,
                    'quantity' => $deliveryMedSupply->quantity,
                    'description' => $adminMedSupply->description,
                    'status' => $midwifeInventoryStatusString,
                    'request_id' => $requestEntry->id,
                ]);


            activity()
            ->causedBy(Auth::user())
            ->performedOn($requestEntry)
            ->withProperties(['old' => $originalMedSupplyData, 'new' => $newMidwifeInventory->toArray(), 'type' => 'Delivery'])
            ->log('Medical supply request processed successfully.');

            }

            $deliveryMedSupply->status = 'Delivered';
            $deliveryMedSupply->save();
        }
    }

    private function processReturnMedicines($requestEntry, $deliveryMedicines)
    {

        if ($deliveryMedicines->isEmpty()) {
            return;
        }

        // Process returned medicines
        foreach ($deliveryMedicines as $deliveryMedicine) {
            Log::info('Processing returned medicine', ['medicine_id' => $deliveryMedicine->medicine_id, 'quantity' => $deliveryMedicine->quantity]);

            // Retrieve the original medicine from the admin's inventory
            $adminMedicine = medicines::where('id', $deliveryMedicine->medicine_id)->firstOrFail();
            $originalMedicineData = $adminMedicine->toArray();

            // Reset reserved status without adjusting quantity
            $adminMedicine->reserved = 0;
            $adminMedicine->save();

            // Log the return activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($requestEntry)
                ->withProperties(['old' => $originalMedicineData, 'new' => $adminMedicine->toArray(), 'type' => 'Return'])
                ->log('Processed return of medicine.');

            // Mark the delivery medicine as "Returned"
            $deliveryMedicine->status = 'Returned';
            $deliveryMedicine->save();
        }
    }

    private function processReturnEquipments($requestEntry, $deliveryEquipments)
    {
        if ($deliveryEquipments->isEmpty()) {
            return;
        }

        // Process returned equipment
        foreach ($deliveryEquipments as $deliveryEquipment) {
            // Retrieve the admin's original equipment
            $adminEquipment = equipments::where('id', $deliveryEquipment->equipment_id)->firstOrFail();
            $originalEquipmentData = $adminEquipment->toArray();

            // Reset the reserved status
            $adminEquipment->reserved = 0;
            $adminEquipment->save();

            // Log the return activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($requestEntry)
                ->withProperties(['old' => $originalEquipmentData, 'new' => $adminEquipment->toArray(), 'type' => 'Return'])
                ->log('Processed return of equipment.');

            // Mark the delivery equipment as "Returned"
            $deliveryEquipment->status = 'Returned';
            $deliveryEquipment->save();
        }
    }

    private function processReturnMedicalSupplies($requestEntry, $deliveryMedSupplies)
    {
        if ($deliveryMedSupplies->isEmpty()) {
            return;
        }

        // Process returned medical supplies
        foreach ($deliveryMedSupplies as $deliveryMedSupply) {
            // Retrieve the admin's original medical supply
            $adminMedSupply = medical_supplies::where('id', $deliveryMedSupply->med_sup_id)->first();
            $originalMedSupplyData = $adminMedSupply->toArray();

            // Reset the reserved status without adjusting quantity
            $adminMedSupply->reserved = 0;
            $adminMedSupply->save();

            // Log the return activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($requestEntry)
                ->withProperties(['old' => $originalMedSupplyData, 'new' => $adminMedSupply->toArray(), 'type' => 'Return'])
                ->log('Processed return of medical supply.');

            // Mark the delivery medical supply as "Returned"
            $deliveryMedSupply->status = 'Returned';
            $deliveryMedSupply->save();
        }
    }
}
