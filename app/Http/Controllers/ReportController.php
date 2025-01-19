<?php

namespace App\Http\Controllers;

use App\Models\Generic_names;
use Carbon\Carbon;
use App\Models\Rhu;
use Inertia\Inertia;
use App\Models\Requests;
use App\Models\Barangays;
use App\Models\Medicines;
use App\Models\Deliveries;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Models\Dispensations;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Medical_supplies;
use App\Models\Dispense_inventory;
use Illuminate\Support\Facades\DB;
use App\Models\Midwife_inventories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(){
        $threshold = 20;
        $medicines = Medicines::where('quantity', '<=', $threshold)->get();
        $equipments = Equipments::where('quantity', '<=', $threshold)->get();
        $medicalSupplies = Medical_supplies::where('quantity', '<=', $threshold)->get();

        $data = [
            'name' => 'Jericho Nomat',
            'email' => 'jericho@gmail.com',
            'threshold' => $threshold,
            'medicines' => $medicines,
            'equipments' => $equipments,
            'medicalSupplies' => $medicalSupplies
        ];

        return view('reports/low-stock-report',$data);
    }
    public function showLowStockPage()
    {
        return inertia('Reports/LowStockReport');
    }

    public function generateLowStockReport(Request $request)
    {
        $type = $request->input('type', 'medicine');
        $threshold = $request->input('threshold', 20);
        $dateRange = $request->input('dateRange');

        $currentDate = Carbon::now()->format('F j, Y');

        // Define an empty date range for displaying in the report
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');
        }

        if ($type === "All") {
            $medicines = Medicines::with(['batch', 'category', 'generic_name'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            $equipments = Equipments::with(['batch'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            $medicalSupplies = Medical_supplies::with(['batch'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            if (!empty($dateRange)) {
                $medicines->whereBetween('created_at', [$startDate, $endDate]);
                $equipments->whereBetween('created_at', [$startDate, $endDate]);
                $medicalSupplies->whereBetween('created_at', [$startDate, $endDate]);
            }

            $medicines = $medicines->get();
            $equipments = $equipments->get();
            $medicalSupplies = $medicalSupplies->get();

        } elseif ($type === "medicine") {
            $medicines = Medicines::with(['batch', 'category', 'generic_name'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            if (!empty($dateRange)) {
                $medicines->whereBetween('created_at', [$startDate, $endDate]);
            }

            $medicines = $medicines->get();
        } elseif ($type === "equipment") {
            $equipments = Equipments::with(['batch'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            if (!empty($dateRange)) {
                $equipments->whereBetween('created_at', [$startDate, $endDate]);
            }

            $equipments = $equipments->get();
        } elseif ($type === "medical_supply") {
            $medicalSupplies = Medical_supplies::with(['batch'])
                ->where('barangay_id', Auth::user()->barangay_id)
                ->where('quantity', '<=', $threshold);

            if (!empty($dateRange)) {
                $medicalSupplies->whereBetween('created_at', [$startDate, $endDate]);
            }

            $medicalSupplies = $medicalSupplies->get();
        }

        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'date' => $currentDate,
            'dateRange' => $formattedDateRange ?? 'All',
            'threshold' => $threshold,
            'medicines' => $medicines ?? '',
            'equipments' => $equipments ?? '',
            'medicalSupplies' => $medicalSupplies ?? ''
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.low-stock-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/low-stock-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('low-stock-report.pdf');
    }

    public function showMedicineExpiringPage(){
        return inertia('Reports/MedicineExpiringReport');
    }

    public function generateMedicineExpiringReport(Request $request)
    {
        $days = $request->input('days', 30);
        $dateRange = $request->input('dateRange');

        $days = (int) $days;
        $expirationDate = Carbon::now()->addDays($days);
        $currentDate = Carbon::now()->format('F j, Y');

        $medicines = Medicines::with(['batch', 'category', 'generic_name'])
            ->where('barangay_id', Auth::user()->barangay_id)
            ->where('expiration_date', '<=', $expirationDate);

        // Apply date range filter if provided
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');

            $medicines->whereBetween('created_at', [$startDate, $endDate]);
        }

        $medicines = $medicines->get();

        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'days' => $days,
            'date' => $currentDate,
            'dateRange' => $formattedDateRange ?? 'All',
            'medicines' => $medicines,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.medicine-expiring-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/medicine-expiring-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('medicine-expiring-report.pdf');
    }

    public function showBarangayInventoryPage(){
        return inertia('Reports/BarangayReport');
    }

    public function generateBarangayInventoryReport(Request $request)
    {
        $rhu_id = $request->input('rhu_id', 1); // Filter by RHU
        $type = $request->input('type', 'All'); // Filter by type (default is 'all')
        $dateRange = $request->input('dateRange');

        // Fetch barangay inventories
        $query = Midwife_inventories::with(['barangay', 'category'])
            ->where('rhu_id', $rhu_id);

        // Apply type filter
        if ($type !== 'All') {
            $query->where('type', $type);
        }

        // Apply date range filter if provided
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $inventories = $query->get()->groupBy('barangay.barangay_name');

        $currentDate = Carbon::now()->format('F j, Y');

        if ($rhu_id == 1) {
            $rhu_name = 'RHU Main';
        } elseif ($rhu_id == 2) {
            $rhu_name = 'RHU 2';
        } else {
            $rhu_name = 'RHU 3';
        }

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'rhu_name' => $rhu_name ?? 'Unknown RHU',
            'rhu_id' => $rhu_id,
            'type' => $type,
            'dateRange' => $formattedDateRange ?? 'All',
            'inventories' => $inventories,
            'date' => $currentDate,
        ];


       // Load the view for the PDF
       $pdf = PDF::loadView('reports.barangay-inventory-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/barangay-inventory-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('barangay-inventory-report.pdf');
    }

    public function showEquipmentStatusPage(){
        return inertia('Reports/EquipmentStatusReport');
    }

    public function generateEquipmentStatusReport(Request $request)
    {
        $status = $request->input('status', 'Good Condition'); // Filter by Status
        $dateRange = $request->input('dateRange');

        // Fetch barangay inventories
        $equipments = Equipments::with(['batch'])
            ->where('barangay_id', Auth::user()->barangay_id)
            ->where('status', $status);

        // Apply date range filter if provided
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');

            $equipments->whereBetween('created_at', [$startDate, $endDate]);
        }

        $equipments = $equipments->get();

        $currentDate = Carbon::now()->format('F j, Y');

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'status' => $status,
            'dateRange' => $formattedDateRange ?? 'All',
            'equipments' => $equipments,
            'date' => $currentDate,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.equipment-status-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/equipment-status-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('equipment-status-report.pdf');
    }

    public function showRequestPage(){
        return inertia('Reports/RequestReport');
    }

    public function generateRequestReport(Request $request)
    {
        $userRhuId = Auth::user()->rhu_id;
        $status = $request->input('status', 'Delivered'); // Filter by Status
        $dateRange = $request->input('dateRange');

        // Fetch barangay inventories
        $query = Requests::with(['requester_user', 'barangay', 'medicineRequests', 'equipmentRequests', 'supplyRequests'])
            ->when($userRhuId != 1, function ($query) use ($userRhuId) {
                $query->where('rhu_id', $userRhuId);
            });

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        // Apply date range filter if provided
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $requests = $query->orderBy('status', 'asc')->get();

        // Add requested items to each request
        $requests->each(function ($req) {
            $req->requestedItems = $req->getRequestedItems();
        });

        $currentDate = Carbon::now()->format('F j, Y');

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'status' => $status,
            'dateRange' => $formattedDateRange ?? 'All',
            'requests' => $requests,
            'date' => $currentDate,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.request-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/request-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('request-report.pdf');
    }

    public function showDeliveryPage(){
        return inertia('Reports/DeliveryReport', ['barangays'=>Barangays::all()]);
    }

    public function generateDeliveryReport(Request $request){

        $barangay = $request->input('barangay'); // Filter by Status
        $dateRange = $request->input('dateRange');

        // Fetch barangay inventories
        $query  = Deliveries::with(['barangay','request','medicineDeliveries', 'equipmentDeliveries', 'supplyDeliveries']);

        if ($barangay !== 'All') {
            $query->where('destination_id', $barangay);
            $barangay_name = Barangays::find($barangay)->barangay_name;
        }

        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $query->whereBetween('updated_at', [$startDate, $endDate]);
            $formattedDateRange = Carbon::parse($startDate)->format('F j, Y') . ' - ' . Carbon::parse($endDate)->format('F j, Y');
        }

        $deliveries = $query->orderBy('date_delivery', 'desc')->get()->groupBy('barangay.barangay_name');

        $currentDate = Carbon::now()->format('F j, Y');

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'barangay' => $barangay_name ?? 'All',
            'dateRange' => $formattedDateRange ?? 'All',
            'Barangaydeliveries' => $deliveries,
            'date' => $currentDate,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.delivery-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/delivery-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('delivery-report.pdf');
    }

    public function showDispensePage(){
        return inertia('Reports/DispenseReport', ['barangays'=>Barangays::all()]);
    }

    public function generateDispenseReport(Request $request){

        $barangay = $request->input('barangay'); // Filter by Status
        $dateRange = $request->input('dateRange');
        $reason = $request->input('reason');


        // Fetch barangay inventories
        $query  = Dispensations::with(['barangay','user','dispenseItems']);

        if ($barangay !== 'All') {
            $query->where('barangay_id', $barangay);
            $barangay_name = Barangays::find($barangay)->barangay_name;
        }

        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $query->whereBetween('created_at', [$startDate, $endDate]);
            $formattedDateRange = Carbon::parse($startDate)->format('F j, Y') . ' - ' . Carbon::parse($endDate)->format('F j, Y');
        }

        $dispenses = $query->orderBy('created_at', 'desc')->get()->groupBy('barangay.barangay_name');

        $currentDate = Carbon::now()->format('F j, Y');

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'barangay' => $barangay_name ?? 'All',
            'dateRange' => $formattedDateRange ?? 'All',
            'Barangaydispenses' => $dispenses,
            'date' => $currentDate,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.dispense-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/dispense-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('dispense-report.pdf');
    }

    public function showMostRequestedPage(){
        return inertia('Reports/MostRequestedReport');
    }

    public function generateMostRequestedReport(Request $request)
    {
        $type = $request->input('type', 'All'); // Default is 'All'
        $limit = $request->input('limit', 20); // Default is 20
        $dateRange = $request->input('dateRange');

        $mostRequestedItems = collect(); // Initialize empty collection for most requested items

        // Parse the date range if provided
        if (!empty($dateRange)) {
            [$startDate, $endDate] = explode(' ~ ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $formattedDateRange = $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y');
        }

        // Fetch data based on the selected type
        if ($type === 'All' || $type === 'medicines') {
            $medicinesQuery = DB::table('medicine_request_names')
                ->select('generic_name_id', DB::raw('COUNT(*) as total_requests'))
                ->groupBy('generic_name_id')
                ->orderBy('total_requests', 'desc')
                ->limit($limit);

            // Apply date range filter if provided
            if (!empty($dateRange)) {
                $medicinesQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $medicines = $medicinesQuery->get();

            // Attach generic name to each medicine
            foreach ($medicines as $medicine) {
                $medicine->name = Generic_names::find($medicine->generic_name_id)->generic_name;
                $medicine->type = 'medicine';
                $mostRequestedItems->push($medicine);
            }
        }

        if ($type === 'All' || $type === 'equipments') {
            $equipmentsQuery = DB::table('equipment_request_names')
                ->select('equipment_name', DB::raw('COUNT(*) as total_requests'))
                ->groupBy('equipment_name')
                ->orderBy('total_requests', 'desc')
                ->limit($limit);

            // Apply date range filter if provided
            if (!empty($dateRange)) {
                $equipmentsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $equipments = $equipmentsQuery->get();

            // Add equipment type
            foreach ($equipments as $equipment) {
                $equipment->type = 'equipment';
                $equipment->name = $equipment->equipment_name;
                $mostRequestedItems->push($equipment);
            }
        }

        if ($type === 'All' || $type === 'medical_supplies') {
            $suppliesQuery = DB::table('supply_request_names')
                ->select('medical_supply_name', DB::raw('COUNT(*) as total_requests'))
                ->groupBy('medical_supply_name')
                ->orderBy('total_requests', 'desc')
                ->limit($limit);

            // Apply date range filter if provided
            if (!empty($dateRange)) {
                $suppliesQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $supplies = $suppliesQuery->get();

            // Add medical supplies type
            foreach ($supplies as $supply) {
                $supply->type = 'medical_supply';
                $supply->name = $supply->medical_supply_name;
                $mostRequestedItems->push($supply);
            }
        }

        // Limit the results overall and sort by total requests
        $mostRequestedItems = $mostRequestedItems->sortByDesc('total_requests')->take($limit);

        $currentDate = Carbon::now()->format('F j, Y');

        // Prepare data for the PDF
        $data = [
            'name' => Auth::user()->name,
            'barangay_name' => Auth::user()->barangay_name,
            'type' => $type,
            'limit' => $limit,
            'dateRange' => $formattedDateRange ?? 'All',
            'mostRequestedItems' => $mostRequestedItems,
            'date' => $currentDate,
        ];

       // Load the view for the PDF
       $pdf = PDF::loadView('reports.most-requested-report', $data);

       // Save or stream the PDF file
       $pdfPath = storage_path('app/public/reports/most-requested-report.pdf');
       $pdf->setOption([
                    'defaultFont' => 'Helvetica',
                    ]);
       $pdf->setPaper('a4', 'landscape')->save($pdfPath);

       // Return the generated PDF URL for the front-end
       return $pdf->stream('most-requested-report.pdf');
    }


}
