<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use App\Models\Perusahaan;

class LaporanExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $jenis;
    protected $title;
    protected $startDate;
    protected $endDate;
    protected $periodType;
    protected $useWaktuReviewDirut;
    protected $disposisiDateLabel;
    protected $useDirutStatus;

    public function __construct($data, $jenis, $title, $startDate = null, $endDate = null, $periodType = 'custom', $useWaktuReviewDirut = true, $disposisiDateLabel = 'Tanggal Disposisi', $useDirutStatus = true)
    {
        $this->data = $data;
        $this->jenis = $jenis;
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->periodType = $periodType;
        $this->useWaktuReviewDirut = $useWaktuReviewDirut;
        $this->disposisiDateLabel = $disposisiDateLabel;
        $this->useDirutStatus = $useDirutStatus;
    }

    public function collection()
    {
        $collection = new Collection();
        
        if ($this->jenis === 'surat_keluar') {
            foreach ($this->data as $index => $item) {
                $tujuan = [];
                if ($item->disposisi && $item->disposisi->tujuan) {
                    foreach ($item->disposisi->tujuan as $user) {
                        $tujuan[] = $user->name;
                    }
                }
                
                // Get perusahaan name from the relationship if available
                $perusahaanName = $item->nama_perusahaan;
                
                $collection->push([
                    'no' => $index + 1,
                    'disposisi_id' => $item->disposisi ? $item->disposisi->id : '-',
                    'tanggal_disposisi' => $item->disposisi && $item->disposisi->waktu_review_dirut ? 
                        $item->disposisi->waktu_review_dirut : '-',
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal' => $item->tanggal_surat,
                    'perihal' => $item->perihal,
                    'perusahaan' => $perusahaanName,
                    'pembuat' => $item->creator ? $item->creator->name : '-',
                    'tujuan_disposisi' => !empty($tujuan) ? implode(', ', $tujuan) : '-',
                    'status_disposisi' => $item->disposisi ? $item->disposisi->status_dirut : 'Belum Disposisi'
                ]);
            }
        } elseif ($this->jenis === 'disposisi') {
            foreach ($this->data as $index => $item) {
                $tujuan = [];
                if ($item->tujuan) {
                    foreach ($item->tujuan as $user) {
                        $tujuan[] = $user->name;
                    }
                }
                
                $collection->push([
                    'no' => $index + 1,
                    'nomor_surat' => $item->suratKeluar ? $item->suratKeluar->nomor_surat : '-',
                    'perihal' => $item->suratKeluar ? $item->suratKeluar->perihal : '-',
                    'tanggal_disposisi' => $item->created_at,
                    'pembuat' => $item->creator ? $item->creator->name : '-',
                    'tujuan' => implode(', ', $tujuan),
                    'status_sekretaris' => $item->status_sekretaris,
                    'status_dirut' => $item->status_dirut
                ]);
            }
        } elseif ($this->jenis === 'surat_masuk') {
            foreach ($this->data as $index => $item) {
                $collection->push([
                    'no' => $index + 1,
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal' => $item->tanggal_surat,
                    'perihal' => $item->perihal,
                    'perusahaan' => $item->perusahaan,
                    'pengirim' => $item->disposisi && $item->disposisi->creator ? $item->disposisi->creator->name : '-',
                    'status_dibaca' => $item->disposisi && isset($item->disposisi->pivot) && $item->disposisi->pivot->dibaca ? 'Dibaca' : 'Belum Dibaca'
                ]);
            }
        }
        
        return $collection;
    }

    public function headings(): array
    {
        if ($this->jenis === 'surat_keluar') {
            return [
                'No',
                'No. Disposisi',
                $this->disposisiDateLabel,
                'Nomor Surat',
                'Tanggal',
                'Perihal',
                'Perusahaan',
                'Pembuat',
                'Tujuan Disposisi',
                'Status Direktur'
            ];
        } elseif ($this->jenis === 'disposisi') {
            return [
                'No',
                'Nomor Surat',
                'Perihal',
                'Tanggal Disposisi',
                'Pembuat',
                'Tujuan',
                'Status Sekretaris',
                'Status Direktur'
            ];
        } elseif ($this->jenis === 'surat_masuk') {
            return [
                'No',
                'Nomor Surat',
                'Tanggal',
                'Perihal',
                'Perusahaan',
                'Disposisi Dari',
                'Status Dibaca'
            ];
        }
        
        return [];
    }

    public function title(): string
    {
        $periodInfo = '';
        
        if ($this->periodType === 'weekly') {
            $periodInfo = ' - Laporan Mingguan';
        } elseif ($this->periodType === 'monthly') {
            $periodInfo = ' - Laporan Bulanan';
        } elseif ($this->startDate && $this->endDate) {
            $periodInfo = " ({$this->startDate} s/d {$this->endDate})";
        }
        
        return $this->title . $periodInfo;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 