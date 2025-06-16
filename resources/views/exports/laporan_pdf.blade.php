<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin-bottom: 5px;
            font-size: 18px;
        }
        .meta {
            margin-bottom: 15px;
        }
        .meta p {
            margin: 2px 0;
        }
        .meta-label {
            display: inline-block;
            width: 100px;
        }
        .meta-colon {
            display: inline-block;
            width: 8px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        table, th, td {
            border: 0.5px solid #333;
        }
        th {
            background-color: #16a34a;
            color: white;
            text-align: left;
            padding: 5px;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            padding: 5px;
            word-wrap: break-word;
            font-size: 9px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
        .text-wrap {
            word-break: break-word;
        }
        .status-pill {
            padding: 2px 4px;
            border-radius: 10px;
            display: inline-block;
        }
        .tujuan-pill {
            background-color: #e6f0ff;
            color: #0055b3;
            padding: 1px 3px;
            border-radius: 8px;
            display: inline-block;
            margin: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistem Informasi Surat Menyurat Azra</p>
    </div>

    <div class="meta">
        <p><span class="meta-label"><strong>Tanggal</strong></span><span class="meta-colon">:</span> {{ $startDate }} s/d {{ $endDate }}</p>
        <p><span class="meta-label"><strong>Jenis Periode</strong></span><span class="meta-colon">:</span> 
        @if ($periodType == 'monthly')
            Bulanan
        @elseif ($periodType == 'weekly')
            Mingguan
        @elseif ($periodType == 'custom')
            Kustom
        @else
            -
        @endif
        </p>
        <p><span class="meta-label"><strong>Dibuat Oleh</strong></span><span class="meta-colon">:</span> {{ auth()->user()->name }}</p>
    </div>

    @if($jenis === 'surat_keluar')
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="4%">No. Disposisi</th>
                    <th width="7%">Tanggal Disposisi</th>
                    <th width="14%">Nomor Surat</th>
                    <th width="7%">Tanggal</th>
                    <th width="18%">Perihal</th>
                    <th width="8%">Perusahaan</th>
                    <th width="10%">Pembuat</th>
                    <th width="20%">Tujuan Disposisi</th>
                    <th width="9%">Status Direktur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->disposisi ? $item->disposisi->id : '-' }}</td>
                        <td>
                            @if($item->disposisi && $item->disposisi->waktu_review_dirut)
                                {{ \Carbon\Carbon::parse($item->disposisi->waktu_review_dirut)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-wrap">{{ $item->nomor_surat }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                        <td class="text-wrap">{{ $item->perihal }}</td>
                        <td>{{ $item->perusahaan }}</td>
                        <td>{{ $item->creator ? $item->creator->name : '-' }}</td>
                        <td>
                            @if($item->disposisi && $item->disposisi->tujuan && $item->disposisi->tujuan->count() > 0)
                                @foreach($item->disposisi->tujuan as $user)
                                    <span class="tujuan-pill">{{ $user->name }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->disposisi)
                                <span class="status-pill" style="
                                    @if($item->disposisi->status_dirut === 'approved') 
                                        background-color: #d1fae5; color: #047857;
                                    @elseif($item->disposisi->status_dirut === 'rejected')
                                        background-color: #fee2e2; color: #b91c1c;
                                    @elseif($item->disposisi->status_dirut === 'review')
                                        background-color: #dbeafe; color: #1e40af;
                                    @else
                                        background-color: #fef3c7; color: #92400e;
                                    @endif
                                ">
                                    {{ $item->disposisi->status_dirut }}
                                </span>
                            @else
                                <span>Belum Disposisi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($jenis === 'disposisi')
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">Nomor Surat</th>
                    <th width="20%">Perihal</th>
                    <th width="10%">Tanggal</th>
                    <th width="14%">Pembuat</th>
                    <th width="20%">Tujuan</th>
                    <th width="9%">Status Sekretaris</th>
                    <th width="9%">Status Direktur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-wrap">{{ $item->suratKeluar ? $item->suratKeluar->nomor_surat : '-' }}</td>
                        <td class="text-wrap">{{ $item->suratKeluar ? $item->suratKeluar->perihal : '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $item->creator ? $item->creator->name : '-' }}</td>
                        <td>
                            @if($item->tujuan->count() > 0)
                                @foreach($item->tujuan as $user)
                                    <span class="tujuan-pill">{{ $user->name }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="status-pill" style="
                                @if($item->status_sekretaris === 'approved') 
                                    background-color: #d1fae5; color: #047857;
                                @elseif($item->status_sekretaris === 'rejected')
                                    background-color: #fee2e2; color: #b91c1c;
                                @elseif($item->status_sekretaris === 'review')
                                    background-color: #dbeafe; color: #1e40af;
                                @else
                                    background-color: #fef3c7; color: #92400e;
                                @endif
                            ">
                                {{ $item->status_sekretaris }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill" style="
                                @if($item->status_dirut === 'approved') 
                                    background-color: #d1fae5; color: #047857;
                                @elseif($item->status_dirut === 'rejected')
                                    background-color: #fee2e2; color: #b91c1c;
                                @elseif($item->status_dirut === 'review')
                                    background-color: #dbeafe; color: #1e40af;
                                @else
                                    background-color: #fef3c7; color: #92400e;
                                @endif
                            ">
                                {{ $item->status_dirut }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($jenis === 'surat_masuk')
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="17%">Nomor Surat</th>
                    <th width="10%">Tanggal</th>
                    <th width="30%">Perihal</th>
                    <th width="12%">Perusahaan</th>
                    <th width="15%">Disposisi Dari</th>
                    <th width="13%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-wrap">{{ $item->nomor_surat }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                        <td class="text-wrap">{{ $item->perihal }}</td>
                        <td>{{ $item->perusahaan }}</td>
                        <td>{{ $item->disposisi && $item->disposisi->creator ? $item->disposisi->creator->name : '-' }}</td>
                        <td>
                            <span class="status-pill" style="
                                @if($item->disposisi && isset($item->disposisi->pivot) && $item->disposisi->pivot->dibaca)
                                    background-color: #d1fae5; color: #047857;
                                @else
                                    background-color: #fef3c7; color: #92400e; 
                                @endif
                            ">
                                {{ $item->disposisi && isset($item->disposisi->pivot) && $item->disposisi->pivot->dibaca ? 'Dibaca' : 'Belum Dibaca' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>{{ now()->format('d F Y') }}</p>
        <br>
        <br>
        <br>
        <p>{{ auth()->user()->name }}</p>
        <p>{{ auth()->user()->jabatan ? auth()->user()->jabatan->nama_jabatan : '' }}</p>
    </div>
</body>
</html> 