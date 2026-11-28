<?php

namespace App\Support;

use App\Models\Borrowing;
use Illuminate\Support\Collection;

class BorrowingReportCsv
{
    /**
     * @param  Collection<int, Borrowing>  $borrowings
     */
    public static function render(Collection $borrowings): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return '';
        }

        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'No',
            'Peminjam',
            'Email Peminjam',
            'Judul Buku',
            'Tgl Pinjam',
            'Batas Kembali',
            'Tgl Kembali',
            'Status',
            'Hari Telat',
        ], ';');

        foreach ($borrowings as $index => $row) {
            fputcsv($handle, [
                $index + 1,
                $row->member->name ?? '-',
                $row->member->email ?? '-',
                $row->book->title ?? '-',
                $row->borrow_date?->format('d-m-Y') ?? '-',
                $row->due_date?->format('d-m-Y') ?? '-',
                $row->return_date?->format('d-m-Y') ?? '-',
                $row->status->label(),
                $row->late_days ?? 0,
            ], ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv !== false ? $csv : '';
    }
}
