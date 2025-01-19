<?php

namespace App\Exports;

use App\Models\Equipments; // Import the equipments model
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements FromQuery, WithHeadings, WithCustomStartCell, WithStyles
{
    use Exportable;

    protected $equipments;

    public function __construct($equipments)
    {
        $this->equipments = $equipments;
    }

    public function query()
    {
        return equipments::query()
            ->join('batches', 'equipments.batch_id', '=', 'batches.id') // Join with batches table
            ->whereKey($this->equipments)
            ->select('equipments.id', 'batches.batch_number', 'equipments.equipment_name', 'equipments.quantity', 'equipments.provider', 'equipments.date_acquired', 'equipments.status');
    }


    public function headings(): array
    {
        return [
            'ID',
            'Batch no.',
            'Equipment Name',
            'Quantity',
            'Provider',
            'Date acquired',
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
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'HEALTH FACILITY MONTHLY UTILIZATION/EQUIPMENT INVENTORY REPORT FORM');

        // Style the title
        $titleStyle = $sheet->getStyle('A1');
        $titleStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $titleStyle->getFont()->setBold(true);
        $titleStyle->getFont()->setSize(14); // Optional: Increase font size for the title

        // Style the headers
        $headerStyle = $sheet->getStyle('A2:G2');
        $headerStyle->getAlignment()->setWrapText(true);
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FFDAEEF3'); // Light blue color

        // Center align the content in each cell horizontally and vertically
        $contentStyle = $sheet->getStyle('A2:G1000');
        $contentStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $contentStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Auto-size columns to fit content
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


}
