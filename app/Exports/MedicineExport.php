<?php

namespace App\Exports;

use App\Models\Medicines;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MedicineExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    protected $medicines;

    public function __construct(array $medicines)
    {
        $this->medicines = $medicines;
    }

    public function collection()
    {
        return Medicines::with(['generic_name', 'category', 'batch'])
            ->whereIn('id', $this->medicines)
            ->get();
    }

    public function map($medicine): array
    {
        return [
            $medicine->id,
            $medicine->batch->batch_number,
            $medicine->generic_name->generic_name, // Fetches the generic name instead of ID
            $medicine->brand,
            $medicine->quantity,
            $medicine->provider,
            $medicine->category->category, // Fetches the category name instead of ID
            $medicine->expiration_date,
            $medicine->date_acquired,
            $medicine->status,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Batch no.',
            'Generic Name',
            'Brand',
            'Quantity',
            'Provider',
            'Category',
            'Expiration Date',
            'Date Acquired',
            'Status',
        ];
    }


    public function startCell(): string
    {
        return 'A2'; // Start headings from A2
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for the title and set its value
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'HEALTH FACILITY MONTHLY UTILIZATION/MEDICINE INVENTORY REPORT FORM');

        // Center and bold the title
        $titleStyle = $sheet->getStyle('A1');
        $titleStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $titleStyle->getFont()->setBold(true);
        $titleStyle->getFont()->setSize(14); // Optional: Increase font size for the title

        // Style the headers
        $headerStyle = $sheet->getStyle('A2:J2');
        $headerStyle->getAlignment()->setWrapText(true);
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FFDAEEF3'); // Light blue color

        // Center align the content in each cell horizontally and vertically
        $contentStyle = $sheet->getStyle('A2:J1000');
        $contentStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $contentStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Auto-size columns to fit content
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


}
