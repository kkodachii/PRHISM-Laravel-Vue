<?php

namespace App\Console\Commands;

use App\Events\MedicineExpiringAlertEvent;
use App\Events\MedicineLowAlertEvent;
use App\Models\Generic_names;
use App\Models\Medicines;
use App\Notifications\MedicineAlert;
use App\Notifications\MedicineExpiringAlert;
use App\Notifications\MedicineLowAlert;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class CheckMedicineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-medicine-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Check for expiring or low-stock medicines and notify users';

    /**
     * Execute the console command.
     */
    public function handle()
{
    // Set thresholds
    $lowStockThreshold = 20;
    $expiryThreshold = Carbon::now()->addDays(30); // 30 days until expiration

    // Get low-stock medicines
    $lowStockMedicines = Medicines::where('quantity', '<=', $lowStockThreshold)->get();

    // Get expiring medicines
    $expiringMedicines = Medicines::where('expiration_date', '<=', $expiryThreshold)->get();

    // Notify for low stock medicines
    foreach ($lowStockMedicines as $medicine) {
        // Find users with the same barangay_id and role_id 2 or 3
        $users = \App\Models\User::where('barangay_id', $medicine->barangay_id)
                    ->whereIn('role_id', [2, 3])
                    ->get();

        // Get generic name for the medicine
        $generic_object = Generic_names::find($medicine->generic_name_id);
        $generic_name = $generic_object->generic_name;

        // Notify each user for low stock
        foreach ($users as $user) {
            $user->notify(new MedicineLowAlert($medicine, $generic_name));
            $notification = $user->notifications()->latest()->first();
            broadcast(new MedicineLowAlertEvent($notification,$user,$medicine, $generic_name));
        }
    }

    // Notify for expiring medicines
    foreach ($expiringMedicines as $medicine) {
        // Find users with the same barangay_id and role_id 2 or 3
        $users = \App\Models\User::where('barangay_id', $medicine->barangay_id)
                    ->whereIn('role_id', [2, 3])
                    ->get();

        // Get generic name for the medicine
        $generic_object = Generic_names::find($medicine->generic_name_id);
        $generic_name = $generic_object->generic_name;

        // Notify each user for expiring soon
        foreach ($users as $user) {
            $user->notify(new MedicineExpiringAlert($medicine, $generic_name));
            $notification = $user->notifications()->latest()->first();
            broadcast(new MedicineExpiringAlertEvent($notification,$user,$medicine, $generic_name));
        }
    }
}

}
