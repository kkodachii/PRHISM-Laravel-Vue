<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DispenseController;
use App\Http\Controllers\DispenselogController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedSummaryController;
use App\Http\Controllers\EquipSummaryController;
use App\Http\Controllers\MSupplySummaryController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\BarangayListController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BatchInventoryController;
use App\Http\Controllers\DashboardFilterController;
use App\Http\Controllers\BarangayInventoryController;
use App\Http\Controllers\Category\BarangayController;
use App\Http\Controllers\Dashboard\RecentlyController;
use App\Http\Controllers\Inventory\MedicineController;
use App\Http\Controllers\Inventory\MedicineArchiveController;
use App\Http\Controllers\Inventory\MedSupplyArchiveController;
use App\Http\Controllers\Inventory\EquipmentArchiveController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inventory\EquipmentController;
use App\Http\Controllers\Category\GenericNameController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Inventory\BatchSupplyController;
use App\Http\Controllers\Category\BatchcategoryController;
use App\Http\Controllers\Dashboard\MedicineUsageController;
use App\Http\Controllers\Inventory\BatchMedicineController;
use App\Http\Controllers\Inventory\MedicalSupplyController;
use App\Http\Controllers\Category\MedicalCategoryController;
use App\Http\Controllers\Dashboard\EquipmentUsageController;
use App\Http\Controllers\Inventory\BatchEquipmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Dashboard\InventoryForeCastController;
use App\Http\Controllers\Dashboard\MedicalSupplyUsageController;

//Guest Routes
Route::middleware('guest')->group(function(){
    Route::inertia('/','Auth/Login')->name('home');

});


Broadcast::routes(['middleware' => ['auth:sanctum']]);

//Auth Routes
Route::middleware(['auth', 'verified'])->group(function () {

    Broadcast::routes();


    //Dashboard yie
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::inertia('/mobile-settings','Mobile/SettingPanel')->name('mobile.settings');
    Route::inertia('/mobile-search','Mobile/SearchPanel')->name('mobile.search');

    //Notification
    Route::resource('/notifications', NotificationController::class);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    //Profile Routes
    Route::resource('/profile', ProfileController::class);

    Route::post('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
    Route::delete('/profile/picture/delete', [ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');
    Route::put('/profile/status/update', [ProfileController::class, 'updateStatus'])->name('profile.status.update');



    // Midwife Inventory
    Route::resource('/inventory', InventoryController::class);

    //dashboard filter
    Route::resource('/inventory_filter', DashboardFilterController::class);
    Route::resource('/medicine_summary', MedSummaryController::class);
    Route::resource('/equipment_summary', EquipSummaryController::class);
    Route::resource('/supply_summary', MSupplySummaryController::class);


    //batchie
    // Route::inertia('/batches-inventory','BatchInventory')->name('batchesinventory');
    Route::resource('/batches-inventory', BatchInventoryController::class);
    //  // Batch Equipment Routes
    Route::resource('/batch-equipment', BatchEquipmentController::class);
    // Batch Medsup Routes
    Route::resource('/batch-msupply', BatchSupplyController::class);
    // Batch Medicine Routes
    Route::resource('/batch-medicine', BatchMedicineController::class);

    //Requests
    Route::resource('/requests', RequestController::class);
    Route::resource('/dispense', DispenseController::class);
    Route::resource('/dispense_history', DispenselogController::class);
    Route::post('/dispense/submit', [DispenseController::class, 'submit'])->name('dispense.submit');




    //Export to excel
     Route::get('/medicines/export/{medicines}', [MedicineController::class, 'export']);
     Route::get('/equipments/export/{equipments}', [EquipmentController::class, 'export']);
     Route::get('/medical_supplies/export/{medical_supplies}', [MedicalSupplyController::class, 'export']);


     Route::get('send-mail', [MailController::class, 'index']);

});



//Roles
Route::middleware('auth:sanctum')->get('/user', [AuthenticatedSessionController::class, 'getUser']);

Route::group(['middleware' => ['roles:SuperAdmin']], function () {
    Route::resource('/users', UserController::class);
    Route::put('/users/status/update/{id}', [UserController::class, 'updateStatus'])->name('users.status.update');
    Route::put('/profile/status/update/{id}', [UserController::class, 'updateStatusreact'])->name('users.status.react');
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.changePass');

    Route::resource('/backups', BackupController::class);
    Route::post('/backup', [BackupController::class, 'backup']);
    Route::post('/restore/{filePath}', [BackupController::class, 'restore'])->name('backup.restore');
    Route::get('/backup/download/{filePath}', [BackupController::class, 'download'])->name('backup.download');

    Route::resource('/recentlylog', RecentlyController::class);
    Route::resource('/activitylog', ActivityLogController::class);
    Route::resource('/equipmentlog', EquipmentUsageController::class);
    Route::resource('/medicineusagelog', MedicineUsageController::class);
    Route::resource('/medicalsupplylog', MedicalSupplyUsageController::class);
    Route::resource('/predictions', PredictionController::class);



});

Route::group(['middleware' => ['roles:Admin,SuperAdmin']], function () {
    // //Inventory Index and Adding new entry
    Route::resource('/medicines', MedicineController::class);
    Route::resource('/archived-medicine', MedicineArchiveController::class);
    Route::put('/archived-medicine/{id}/restore', [MedicineArchiveController::class, 'restore'])->name('medicines.restore');
    Route::resource('/archived-equipment', EquipmentArchiveController::class);
    Route::put('/archived-equipment/{id}/restore', [EquipmentArchiveController::class, 'restore'])->name('equipments.restore');
    Route::resource('/archived-supply', MedSupplyArchiveController::class);
    Route::put('/archived-supply/{id}/restore', [MedSupplyArchiveController::class, 'restore'])->name('supplies.restore');
    Route::resource('/equipments', EquipmentController::class);
    Route::resource('/medical_supplies', MedicalSupplyController::class);
    Route::resource('/batches', BatchController::class);
    Route::resource('/barangay_list', BarangayListController::class);
    Route::resource('/barangay_inventory', BarangayInventoryController::class);
    Route::post('/equipments/{id}/update-status', [EquipmentController::class, 'updateStatus'])->name('equipments.updateStatus');

    //Add Edit
    Route::resource('/generic_name', GenericNameController::class);
    Route::resource('/medical_category', MedicalCategoryController::class);
    Route::resource('/barangay', BarangayController::class);
    Route::resource('/batch', BatchcategoryController::class);



    //Request
    Route::get('/approve-request/{id}', [RequestController::class, 'approveRequest'])->name('approve-request');
    Route::inertia('/approved','Requests/Approved')->name('approved');
    Route::post('/request/reject-request', [RequestController::class, 'rejectRequest'])->name('request.rejectRequest');
    Route::post('/delivery/{requestId}/mark-as-claimed', [DeliveryController::class, 'markAsClaimed'])->name('delivery.markAsClaimed');


    //Deliveryiee
    Route::resource('/delivery', DeliveryController::class);
    Route::get('/find-delivery/{deliveryId}', [DeliveryController::class, 'findDeliveryPage']);

    // Route::inertia('/batch','SuperAdmin/Category/Batch')->name('batch');

    Route::inertia('/reports','Reports')->name('reports');

    //Reports Generation
    Route::get('/reports/low-stock', [ReportController::class, 'showLowStockPage'])->name('reports.low-stock');
    Route::get('/reports/low-stock/generate/', [ReportController::class, 'generateLowStockReport'])->name('reports.low-stock.generate');

    Route::get('/reports/medicine-expiring', [ReportController::class, 'showMedicineExpiringPage'])->name('reports.medicine-expiring');
    Route::get('/reports/medicine-expiring/generate/', [ReportController::class, 'generateMedicineExpiringReport'])->name('reports.medicine-expiring.generate');

    Route::get('/reports/barangay-inventory', [ReportController::class, 'showBarangayInventoryPage'])->name('reports.barangay-inventory');
    Route::get('/reports/barangay-inventory/generate', [ReportController::class, 'generateBarangayInventoryReport'])->name('reports.barangay-inventory.generate');

    Route::get('/reports/equipment-status', [ReportController::class, 'showEquipmentStatusPage'])->name('reports.equipment-status');
    Route::get('/reports/equipment-status/generate', [ReportController::class, 'generateEquipmentStatusReport'])->name('reports.equipment-status.generate');

    Route::get('/reports/request', [ReportController::class, 'showRequestPage'])->name('reports.request');
    Route::get('/reports/request/generate', [ReportController::class, 'generateRequestReport'])->name('reports.request.generate');

    Route::get('/reports/delivery', [ReportController::class, 'showDeliveryPage'])->name('reports.delivery');
    Route::get('/reports/delivery/generate', [ReportController::class, 'generateDeliveryReport'])->name('reports.delivery.generate');

    Route::get('/reports/dispense', [ReportController::class, 'showDispensePage'])->name('reports.dispense');
    Route::get('/reports/dispense/generate', [ReportController::class, 'generateDispenseReport'])->name('reports.dispense.generate');

    Route::get('/reports/most-requested', [ReportController::class, 'showMostRequestedPage'])->name('reports.most-requested');
    Route::get('/reports/most-requested/generate', [ReportController::class, 'generateMostRequestedReport'])->name('reports.most-requested.generate');

});

Route::group(['middleware' => ['roles:Midwife,Admin']], function () {
    Route::inertia('/pending-request','Requests/PendingRequest')->name('pending-request');
    Route::post('/delivery/{requestId}/mark-as-delivered', [DeliveryController::class, 'markAsDelivered'])->name('delivery.markAsDelivered');
    Route::post('/delivery/{requestId}/save-to-inventory', [DeliveryController::class, 'saveToInventory'])->name('delivery.saveToInventory');
    Route::post('/delivery/{requestId}/return-delivery', [DeliveryController::class, 'returnDelivery'])->name('delivery.returnDelivery');
    Route::get('/report-delivery/{id}', [DeliveryController::class, 'reportDelivery'])->name('report-delivery');
    Route::post('/delivery/report', [DeliveryController::class, 'submitReport'])->name('delivery.storeReport');
});

require __DIR__.'/auth.php';
