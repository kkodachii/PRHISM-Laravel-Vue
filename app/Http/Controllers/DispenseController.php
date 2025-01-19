<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Dispensations;
use App\Models\Midwife_inventories;
use App\Models\Dispense_inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DispenseController extends Controller
{
    public function index(Request $request){

        // Define the default sorting field and direction
        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        $mw_inventory = Midwife_inventories::with(['category'])
        ->where('barangay_id', Auth::user()->barangay_id)
        ->when($request->search, function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
        })
        ->where('barangay_id', Auth::user()->barangay_id)
        ->paginate(5);

return Inertia::render('Midwife/Dispense', [
            'mw_inventory' => $mw_inventory,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function submit(Request $request)
    {

        $validatedData = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            'provider_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'recipients_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'birthday' => 'nullable|date',
            'age' => 'nullable|integer',
            'dispense_date' => 'required|date',
            'medicine' => 'nullable|array',
            'medicine.*.id' => 'sometimes|exists:midwife_inventory,id',
            'medicine.*.quantity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required|string',
            'equipment.*.quantity' => 'required|integer|min:1',
            'medicalSupply' => 'nullable|array',
            'medicalSupply.*.name' => 'required|string',
            'medicalSupply.*.quantity' => 'required|integer|min:1',
        ]);

        if (!empty($validatedData['birthday'])) {
            try {
  
                $validatedData['birthday'] = Carbon::createFromFormat('d M Y', $validatedData['birthday'])->format('Y-m-d');
            } catch (\Exception $e) {
                
                $validatedData['birthday'] = Carbon::parse($validatedData['birthday'])->format('Y-m-d');
            }
        }

    
        // Create a new dispensation record
        $dispensation = Dispensations::create([
            'user_id' => Auth::id(),
            'barangay_id' => $validatedData['barangay_id'],
            'provider_name' => $validatedData['provider_name'],
            'position' => $validatedData['position'],
            'recipients_name' => $validatedData['recipients_name'],
            'address' => $validatedData['address'],
            'reason' => $validatedData['reason'],
            'birthday' => $validatedData['birthday'],
            'age' => $validatedData['age'],
            'dispense_date' => $validatedData['dispense_date'],
        ]);
    
        // Process and log the medicines
        foreach ($validatedData['medicine'] as $medicine) {
            $mwInventory = Midwife_inventories::find($medicine['id']);
            if ($mwInventory && $mwInventory->quantity >= $medicine['quantity']) {
                $originalData = $mwInventory->toArray(); // Store the original state

                Dispense_inventory::create([
                    'dispense_id'=>$dispensation->id,
                    'midwife_inventory_id' => $medicine['id'],
                    'type' => 'medicine',
                    'name' => $mwInventory->name,
                    'quantity' => $medicine['quantity'],
                ]);

                $mwInventory->decrement('quantity', $medicine['quantity']);
                $mwInventory->computeStatus();
                $mwInventory->save();
    
                if ($mwInventory->quantity == 0) {
                    $mwInventory->delete();
                }
    
                // Log the activity with the medicine name
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($mwInventory)
                    ->withProperties(['old' => $originalData, 'new' => $mwInventory->toArray(), 'type' => 'Dispense'])
                    ->log('Medicine: ' . $mwInventory->name);

            } else {
                return redirect()->back()->withErrors(['medicine' => 'Insufficient stock for ' . ($mwInventory ? $mwInventory->name : 'unknown medicine')]);
            }
        }

    
        // Process and log the equipment
        foreach ($validatedData['equipment'] ?? [] as $equipment) {
            $equipmentItem = Midwife_inventories::where('name', $equipment['name'])->first();
            if ($equipmentItem && $equipmentItem->quantity >= $equipment['quantity']) {
                $originalData = $equipmentItem->toArray(); // Store the original state

                Dispense_inventory::create([
                    'dispense_id'=>$dispensation->id,
                    'midwife_inventory_id' => $equipmentItem->id,
                    'type' => 'equipment',
                    'name' => $equipmentItem->name,
                    'quantity' => $equipment['quantity'],
                ]);
                $equipmentItem->decrement('quantity', $equipment['quantity']);
    
                if ($equipmentItem->quantity == 0) {
                    $equipmentItem->delete();
                }
    
                // Log the activity with the equipment name
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($equipmentItem)
                    ->withProperties(['old' => $originalData, 'new' => $equipmentItem->toArray(), 'type' => 'Dispense'])
                    ->log('Equipment: ' . $equipmentItem->name);

            } else {
                return redirect()->back()->withErrors(['equipment' => 'Insufficient stock for ' . ($equipmentItem ? $equipmentItem->name : 'unknown equipment')]);
            }
        }
    
        // Process and log the medical supplies
        foreach ($validatedData['medicalSupply'] ?? [] as $medicalSupply) {
            $medicalSupplyItem = Midwife_inventories::where('name', $medicalSupply['name'])->first();
            if ($medicalSupplyItem && $medicalSupplyItem->quantity >= $medicalSupply['quantity']) {
                $originalData = $medicalSupplyItem->toArray(); // Store the original state

                Dispense_inventory::create([
                    'dispense_id'=>$dispensation->id,
                    'midwife_inventory_id' => $medicalSupplyItem->id,
                    'type' => 'medical_supply',
                    'name' => $medicalSupplyItem->name,
                    'quantity' => $medicalSupply['quantity'],
                ]);
    
                $medicalSupplyItem->decrement('quantity', $medicalSupply['quantity']);
                $medicalSupplyItem->computeStatus();
                $medicalSupplyItem->save();
    
                if ($medicalSupplyItem->quantity == 0) {
                    $medicalSupplyItem->delete();
                }

                // Log the activity with the medical supply name
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($medicalSupplyItem)
                    ->withProperties(['old' => $originalData, 'new' => $medicalSupplyItem->toArray(),'type' => 'Dispense'])
                    ->log('Medical supply: ' . $medicalSupplyItem->name);

            } else {
                return redirect()->back()->withErrors(['medicalSupply' => 'Insufficient stock for ' . ($medicalSupplyItem ? $medicalSupplyItem->name : 'unknown supply')]);
            }
        }

        return redirect()->route('dispense_history.index')->with('toast', [
            'type' => 'success',
            'message' => 'Dispensation submitted successfully!',
        ]);
    }

}
