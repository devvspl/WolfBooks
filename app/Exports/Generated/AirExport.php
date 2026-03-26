<?php
namespace App\Exports\Generated;

use App\Models\Generated\Air;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AirExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected array $columns = ['mode', 'agent_name', 'p_n_r_number', 'date_of_booking', 'journey_date', 'air_line', 'ticket_number', 'journey_from', 'journey_upto', 'travel_class', 'location', 'items'];
    protected array $headingLabels = ['Mode', 'Agent Name', 'P N R Number', 'Date Of Booking', 'Journey Date', 'Air Line', 'Ticket Number', 'Journey From', 'Journey Upto', 'Travel Class', 'Location', 'Items'];

    public function collection()
    {
        return Air::all();
    }

    public function headings(): array
    {
        return [$this->headingLabels];
    }

    public function map($row): array
    {
        return array_map(fn($col) => $row->{$col} ?? '', $this->columns);
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headingLabels));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B91C1C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $colCount  = count($this->headingLabels);
                $lastCol   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $totalRows = $sheet->getHighestRow();
                for ($row = 2; $row <= $totalRows; $row++) {
                    $isEven = $row % 2 === 1;
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $isEven ? 'FEF2F2' : 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
                    ]);
                }
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '7F1D1D']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(20);
            },
        ];
    }
}