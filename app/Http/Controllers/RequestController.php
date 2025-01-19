<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rhu;
use App\Models\User;
use App\Models\Requests;
use App\Models\Barangays;
use App\Models\Medicines;
use App\Models\Deliveries;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Events\RequestReceived;
use App\Models\Medical_supplies;
use App\Events\RequestRejectEvent;
use App\Models\Delivery_medicines;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery_equipments;
use App\Models\Midwife_inventories;
use App\Models\Supply_request_name;
use Illuminate\Support\Facades\Log;
use App\Events\RequestApprovedEvent;
use Illuminate\Support\Facades\Auth;
use App\Events\RequestDeliveredEvent;
use App\Models\Delivery_med_supplies;
use App\Models\Medicine_request_name;
use App\Models\Equipment_request_name;
use App\Notifications\RequestRejectNotification;
use App\Notifications\MedicineRequestNotification;
use App\Notifications\RequestApprovedNotification;
use App\Notifications\RequestDeliveredNotification;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = Barangays::find($user->barangay_id);
        $barangayId = $barangay->rhu_id;

        if($user->role_id == 2 || $user->role_id == 3){
            if($user->rhu_id == 2 || $user->rhu_id == 3){
                $barangayId = 1;
            }
        };

        $genericNames = DB::table('medicines')
        ->join('generic_names', 'medicines.generic_name_id', '=', 'generic_names.id')
        ->select('generic_names.id as id', 'generic_names.generic_name', DB::raw('SUM(medicines.quantity) as quantity'))
        ->where('medicines.barangay_id', $barangayId)
        ->groupBy('medicines.generic_name_id', 'generic_names.generic_name', 'generic_names.id')
        ->get();

        // dd($medicines);

        $equipmentNames = Equipments::select('equipment_name', DB::raw('COALESCE(SUM(quantity), 0) as quantity'))
            ->where('barangay_id', '=', $barangayId)
            ->groupBy('equipment_name')
            ->get();

        $MsupplyNames = Medical_supplies::select('med_sup_name', DB::raw('COALESCE(SUM(quantity), 0) as quantity'))
            ->where('barangay_id', '=', $barangayId)
            ->groupBy('med_sup_name')
            ->get();

        // Fetch requests with optional search functionality
        $requests = Requests::orderBy('updated_at', 'desc')
            ->when($request->search, function ($query) use ($request) {
                $query->where('description', 'like', '%' . $request->search . '%');
            })
            ->paginate(5);


        $requestAgain = $request->input('requestAgain');

        $statuses = ['Pending', 'Delivery Approved', 'Pickup Approved', 'Ongoing'];

        // Check if the user has any pending or ongoing requests
        $hasRequest = Requests::where('requester_user_id', Auth::user()->id)
            ->whereIn('status', $statuses)
            ->first();

        // Redirect to PendingRequest page if there's a pending or ongoing request
        if ($hasRequest) {
            if ($requestAgain) {
                return inertia('Requests/Request', [
                    'requests' => $requests,
                    'genericNames' => $genericNames,
                    'equipNames' => $equipmentNames,
                    'MsupplyNames' => $MsupplyNames,
                ]);
            } else {
                $userRequest = Requests::where('requester_user_id', Auth::user()->id)
                    ->where('status', 'Ongoing')
                    ->get();

                $userRequest->each(function ($req) {
                    $req->requestedItems = $req->getRequestedItems();

                    if ($req->status === 'Ongoing') {
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
                    }

                    if ($req->status === 'Ongoing') {
                        $delivery = $req->delivery;
                    }
                });
                $pickup_delivery = null;

                $pickup_requests = Requests::where('status', 'Pickup Approved')->first();
                if($pickup_requests){
                $pickup_delivery = Deliveries::find($pickup_requests->id);
                }
                return inertia('Requests/PendingRequest', [
                    'message' => $hasRequest->status,
                    'requests' => $userRequest,
                    'pickup_date' =>$pickup_delivery,
                ]);
            }
        }

        return inertia('Requests/Request', [
            'requests' => $requests,
            'genericNames' => $genericNames,
            'equipNames' => $equipmentNames,
            'MsupplyNames' => $MsupplyNames,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',

            'medicine' => 'required|array|min:1',
            'medicine.*.generic_name_id' => 'required|exists:generic_names,id',
            'medicine.*.quantity' => 'required|integer|min:1',

            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required|string',
            'equipment.*.quantity' => 'required|integer|min:1',

            'medicalSupply' => 'nullable|array',
            'medicalSupply.*.name' => 'required|string',
            'medicalSupply.*.quantity' => 'required|integer|min:1',
        ]);

        // Create the main request record
        $barangayId = $validatedData['barangay_id'];
        $user = Auth::user();
        $rhuId = $user->rhu_id;

        if($user->role_id == 2 || $user->role_id == 3){
            if($user->rhu_id == 2 || $user->rhu_id == 3){
                $rhuId = 1;
            }
        };

        $medicineRequest = Requests::create([
            'rhu_id' => $rhuId,
            'requester_user_id' => Auth::user()->id,
            'status' => 'Pending',
            'barangay_id' => $barangayId,
            'requested_at' => now(),
        ]);

        // Insert medicine requests
        $medicinesLogged = [];
        foreach ($validatedData['medicine'] as $medicine) {
            $medicinesLogged[] = medicine_request_name::create([
                'request_id' => $medicineRequest->id,
                'generic_name_id' => $medicine['generic_name_id'],
                'quantity' => $medicine['quantity'],
            ]);
        }

        // Insert equipment requests if any
        $equipmentLogged = [];
        if (!empty($validatedData['equipment'])) {
            foreach ($validatedData['equipment'] as $equipment) {
                $equipmentLogged[] = equipment_request_name::create([
                    'request_id' => $medicineRequest->id,
                    'equipment_name' => $equipment['name'],
                    'quantity' => $equipment['quantity'],
                ]);
            }
        }

        // Insert medical supply requests if any
        $suppliesLogged = [];
        if (!empty($validatedData['medicalSupply'])) {
            foreach ($validatedData['medicalSupply'] as $supply) {
                $suppliesLogged[] = supply_request_name::create([
                    'request_id' => $medicineRequest->id,
                    'medical_supply_name' => $supply['name'],
                    'quantity' => $supply['quantity'],
                ]);
            }
        }

        // Log the activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($medicineRequest)
            ->withProperties([
                'medicines' => $medicinesLogged,
                'equipment' => $equipmentLogged,
                'supplies' => $suppliesLogged,
                'type' => 'Request'
            ])
            ->log('Created a new request');

        // Notify the RHU admin about the new request
        $rhuAdminsAndSuperAdmins = User::whereIn('role_id', [2, 3])
            ->where('rhu_id', Auth::user()->rhu_id)
            ->get();

        $Requestid = requests::find($medicineRequest->id);

        $barangay = Barangays::find($medicineRequest->barangay_id);
        $barangay_name = $barangay ? $barangay->barangay_name : 'Unknown Barangay';

        $userID = User::find(Auth::user()->id);
        $userName = $userID->name;

        foreach ($rhuAdminsAndSuperAdmins as $rhuUser) {
            $rhuUser->notify(new MedicineRequestNotification($medicineRequest, $userName, $barangay_name));
            $notification = $rhuUser->notifications()->latest()->first();
            broadcast(new RequestReceived($notification, $Requestid, $rhuUser, $userID, $barangay_name));
        }


        Log::info('Toast being set');

        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Request submitted successfully!',
        ]);
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'delivery_medicines' => 'required|array',
            'delivery_medicines.*.id' => 'required|integer',
            'delivery_medicines.*.requestedQuantity' => 'required|integer',
            'delivery_equipments' => 'sometimes|array',
            'delivery_equipments.*.id' => 'required|integer',
            'delivery_equipments.*.requestedQuantity' => 'required|integer',
            'delivery_med_supplies' => 'sometimes|array',
            'delivery_med_supplies.*.id' => 'required|integer',
            'delivery_med_supplies.*.requestedQuantity' => 'required|integer',
        ]);


        $pickup_date=null;
        $for_pickup = false;
        $status = 'Delivery Approved';

        if(!$request->delivery_name){
            $for_pickup = true;
            $status = 'Pickup Approved';
        }
        if ($request->pickup_date) {
            $for_pickup = true;
            $status = 'Pickup Approved';
            $pickup_date = Carbon::createFromFormat('d M Y', $request->pickup_date)->format('Y-m-d');
        }

        DB::transaction(function () use ($request, $id, $status) {
            // Update the request with new data
            $pendingRequest = requests::findOrFail($id);
            $pendingRequest->status = $status;
            $pendingRequest->date_approved = now();
            $pendingRequest->approver_user_id = Auth::user()->id;
            $pendingRequest->approver_name = $request->approver_name;
            $pendingRequest->approver_rhu = $request->approver_rhu;
            $pendingRequest->approver_position = $request->approver_position;
            $pendingRequest->updated_at = now();
            $pendingRequest->save();
        });



        // Create a new delivery entry
        $delivery = deliveries::create([
            'request_id' => $id,
            'user_id' => Auth::user()->id,
            'rhu_id' => $request->approver_rhu,
            'for_pickup'=> $for_pickup,
            'pickup_date' => $pickup_date,
            'delivery_name' => $request->delivery_name,
            'delivery_number' => $request->delivery_number,
            'delivery_address' => $request->delivery_address,
            'date_delivery' => now(),
            'destination_id' => $request->destination_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // Process and insert medicines into delivery_medicines table
        if (!empty($validated['delivery_medicines'])) {
            foreach ($validated['delivery_medicines'] as $medicine) {
                // Get the medicine record from the database
                $medicineRecord = Medicines::find($medicine['id']);

                if ($medicineRecord) {
                    // Retrieve the generic name from the generic_names table
                    $genericName = generic_names::find($medicineRecord->generic_name_id)->generic_name;

                    // Create the delivery_medicines entry with the generic_name
                    delivery_medicines::create([
                        'delivery_id' => $delivery->id,
                        'medicine_id' => $medicineRecord->id,
                        'generic_name' => $genericName, // Store the generic_name
                        'quantity' => $medicine['requestedQuantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update the reserved column with requestedQuantity
                    $medicineRecord->reserved = $medicine['requestedQuantity'];
                    $medicineRecord->save();
                }
            }
        }

        // Process and insert equipment into delivery_equipments table
        if (!empty($validated['delivery_equipments'])) {
            foreach ($validated['delivery_equipments'] as $equipment) {
                $equipmentRecord = equipments::find($equipment['id']);

                if ($equipmentRecord) {
                    // Retrieve the equipment name
                    $equipmentName = $equipmentRecord->equipment_name;

                    // Create the delivery_equipments entry
                    delivery_equipments::create([
                        'delivery_id' => $delivery->id,
                        'equipment_id' => $equipment['id'],
                        'equipment_name' => $equipmentName,
                        'quantity' => $equipment['requestedQuantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update the reserved column with requestedQuantity
                    $equipmentRecord->reserved = $equipment['requestedQuantity'];
                    $equipmentRecord->save();
                }
            }
        }

        // Process and insert medical supplies into delivery_med_supplies table
        if (!empty($validated['delivery_med_supplies'])) {
            foreach ($validated['delivery_med_supplies'] as $medSupply) {
                $medSupRecord = medical_supplies::find($medSupply['id']);

                if ($medSupRecord) {
                    // Retrieve the medical supply name
                    $medSupName = $medSupRecord->med_sup_name;

                    // Create the delivery_med_supplies entry
                    delivery_med_supplies::create([
                        'delivery_id' => $delivery->id,
                        'med_sup_id' => $medSupply['id'],
                        'med_sup_name' => $medSupName,
                        'quantity' => $medSupply['requestedQuantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update the reserved column with requestedQuantity
                    $medSupRecord->reserved = $medSupply['requestedQuantity'];
                    $medSupRecord->save();
                }
            }
        }

        $approvedMode = 'will be on-delivery soon.';
        if($for_pickup == true){
            $approvedMode = 'will be ready for Pickup.';
        }

        $request = requests::findOrFail($id);
        $requester = User::where('id', $request->requester_user_id)->get()->first();
        $barangay_id = Barangays::find(Auth::user()->barangay_id);
        $barangay_name = $barangay_id->barangay_name;

        $requester->notify(new RequestApprovedNotification($request, $barangay_name, $approvedMode));
        $notification = $requester->notifications()->latest()->first();
        broadcast(new RequestApprovedEvent($notification, $request, $barangay_name, $approvedMode));


        return redirect()->route('approved')->with('toast', [
            'type' => 'success',
            'message' => 'Request approved successfully!',
        ]);
    }

    public function approveRequest($id, Request $request)
    {
        // Fetch the pending request with related user and barangay data
        $pendingRequest = requests::with(['requester_user', 'barangay'])->findOrFail($id);


        if($pendingRequest->status !=='Pending'){
            return redirect()->route('delivery.index')->with('toast', [
                'type' => 'warning',
                'message' => 'Request already been Approved/Delivered!',
            ]);
        }

        $pendingRequest->getRequestedItems();

        // Define the default sorting field and direction
        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        // Filter and sort for medicines
        $medicines = medicines::with(['category', 'user', 'generic_name'])
            ->when($request->input('searchMedicine'), function ($query) use ($request) {
                $query->whereHas('generic_name', function ($q) use ($request) {
                    $q->where('generic_name', 'like', '%' . $request->input('searchMedicine') . '%');
                });
            })
            ->where('barangay_id', Auth::user()->barangay_id)
            ->when($request->input('status_medicine'), function ($query) use ($request) {
                $status = explode(',', $request->input('status_medicine'));

                $query->where(function ($query) use ($status) {
                    $query->where(function ($query) use ($status) {
                        if (in_array('onStock', $status)) {
                            $query->orWhere('status', 'On stock');
                        }
                        if (in_array('lowOnStock', $status)) {
                            $query->orWhere('status', 'LIKE', '%Low on stock%');
                        }
                        if (in_array('outOfStock', $status)) {
                            $query->orWhere('status', 'LIKE', '%Out of stock%');
                        }
                    });
                    if (in_array('expiring', $status)) {
                        $query->orWhere('status', 'LIKE', '%Expiring%');
                    }
                    if (in_array('expired', $status)) {
                        $query->orWhere('status', 'Expired');
                    }
                });
            })

            ->when($sortField == 'generic_name', function ($query) use ($sortDirection) {
                // Sort by generic_name alphabetically
                $query->orderByRaw("(SELECT generic_names.generic_name FROM generic_names WHERE generic_names.id = medicines.generic_name_id) $sortDirection");
            })
            ->when($sortField == 'status', function ($query) use ($sortDirection) {
                // Sort by status
                $query->orderByRaw("FIELD(medicines.status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired') $sortDirection");
            })
            ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                // Sort by quantity
                $query->orderBy('quantity', $sortDirection);
            })
            ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                // Sort by date_acquired
                $query->orderBy('date_acquired', $sortDirection);
            })
            ->when($sortField == 'expiration_date', function ($query) use ($sortDirection) {
                // Sort by expiration_date
                $query->orderBy('expiration_date', $sortDirection);
            })
            ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                // Sort by batch_id
                $query->orderBy('batch_id', $sortDirection);
            })
            ->paginate(5, ['*'], 'medicines_page');

        // Filter and sort for equipment
        $equipments = equipments::with('user')
            ->when($request->input('searchEquipment'), function ($query) use ($request) {
                $query->where('equipment_name', 'like', '%' . $request->input('searchEquipment') . '%')
                    ->orWhere('description', 'like', '%' . $request->input('searchEquipment') . '%');
            })
            ->where('barangay_id', Auth::user()->barangay_id)
            ->when($request->input('status_equipment'), function ($query) use ($request) {
                $status = explode(',', $request->input('status_equipment'));

                $query->where(function ($query) use ($status) {
                    if (in_array('new', $status)) {
                        $query->orWhere('status', 'New');
                    }
                    if (in_array('goodCondition', $status)) {
                        $query->orWhere('status', 'Good condition');
                    }
                    if (in_array('fairCondition', $status)) {
                        $query->orWhere('status', 'Fair condition');
                    }
                    if (in_array('poorCondition', $status)) {
                        $query->orWhere('status', 'Poor condition');
                    }
                    if (in_array('condemned', $status)) {
                        $query->orWhere('status', 'Condemned');
                    }
                });
            })
            ->when($sortField == 'equipment_name', function ($query) use ($sortDirection) {
                // Sort by equipment_name alphabetically
                $query->orderBy('equipment_name', $sortDirection);
            })
            ->when($sortField == 'status', function ($query) use ($sortDirection) {
                // Sort by status
                $query->orderByRaw("FIELD(equipments.status, 'New', 'Good condition', 'Fair condition', 'Poor condition') $sortDirection");
            })
            ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                // Sort by quantity
                $query->orderBy('quantity', $sortDirection);
            })
            ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                // Sort by date_acquired
                $query->orderBy('date_acquired', $sortDirection);
            })
            ->when($sortField == 'expiration_date', function ($query) use ($sortDirection) {
                // Sort by expiration_date
                $query->orderBy('expiration_date', $sortDirection);
            })
            ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                // Sort by batch_id
                $query->orderBy('batch_id', $sortDirection);
            })
            ->paginate(5, ['*'], 'equipment_page');

        // Filter and sort for medical supplies
        $medsup = medical_supplies::with('user')
            ->when($request->input('searchSupply'), function ($query) use ($request) {
                $query->where('med_sup_name', 'like', '%' . $request->input('searchSupply') . '%')
                    ->orWhere('description', 'like', '%' . $request->input('searchSupply') . '%');
            })
            ->where('barangay_id', Auth::user()->barangay_id)
            ->when($request->input('status_supply'), function ($query) use ($request) {
                $status = explode(',', $request->input('status_supply'));

                $query->where(function ($query) use ($status) {
                    if (in_array('onStock', $status)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $status)) {
                        $query->orWhere('status', 'Low on stock');
                    }
                    if (in_array('outOfStock', $status)) {
                        $query->orWhere('status', 'Out of stock');
                    }
                });
            })
            ->when($sortField == 'med_sup_name', function ($query) use ($sortDirection) {
                // Sort by equipment_name alphabetically
                $query->orderBy('med_sup_name', $sortDirection);
            })
            ->when($sortField == 'status', function ($query) use ($sortDirection) {
                // Sort by status
                $query->orderByRaw("FIELD(medical_supplies.status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired') $sortDirection");
            })
            ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                // Sort by quantity
                $query->orderBy('quantity', $sortDirection);
            })
            ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                // Sort by date_acquired
                $query->orderBy('date_acquired', $sortDirection);
            })
            ->when($sortField == 'expiration_date', function ($query) use ($sortDirection) {
                // Sort by expiration_date
                $query->orderBy('expiration_date', $sortDirection);
            })
            ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                // Sort by batch_id
                $query->orderBy('batch_id', $sortDirection);
            })
            ->paginate(5, ['*'], 'supplies_page');

        return inertia('Requests/ApproveRequest', [
            'pendingRequest' => $pendingRequest,
            'medicines' => $medicines,
            'equipments' => $equipments,
            'med_supplies' => $medsup,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function rejectRequest(Request $request,){
        $id = $request->input("request_id");
        $reason = $request->input('rejectReason');

        $requestEntry = requests::findOrFail($id);

        $requestEntry->status = 'Rejected';
        $requestEntry->reject_reason = $reason;
        $requestEntry->save();

        // Notify the RHU admin about the new request
        $requester = User::where('id', $requestEntry->requester_user_id)->get()->first();
        $barangay_id = Barangays::find(Auth::user()->barangay_id);
        $barangay_name = $barangay_id->barangay_name;

        $requester->notify(new RequestRejectNotification($requestEntry, $barangay_name,$reason));
        $notification = $requester->notifications()->latest()->first();
        broadcast(new RequestRejectEvent($notification, $requestEntry, $barangay_name,$reason));


        activity()
            ->causedBy(Auth::user())
            ->performedOn($requestEntry)
            ->withProperties(['type' => 'Reject'])
            ->log('rejected the request');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Request has been rejected',
            'type' => 'success'
        ]);
    }

    public function destroy(string $id)
    {

    }
}
