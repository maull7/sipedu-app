<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background-color: #ffffff;
            color: #000;
            padding: 20px;
        }
        .page {
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header-left {
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-right {
            font-size: 12px;
            line-height: 1.4;
            text-align: left;
        }
        .title {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
        .ttd {
            margin-top: 60px;
            text-align: center;
        }
        .ttd img {
            width: 100px;
            margin-bottom: 5px;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>

@php
    function terbilang($angka)
    {
        $angka = floatval(str_replace(',', '.', $angka));
        $angka = number_format($angka, 2, '.', '');
        $bilangan = explode('.', $angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];

        $hasil = '';
        foreach (str_split($bilangan[0]) as $digit) {
            $hasil .= $huruf[(int)$digit] . ' ';
        }

        if (isset($bilangan[1])) {
            $hasil .= 'Koma ';
            foreach (str_split($bilangan[1]) as $digit) {
                $hasil .= $huruf[(int)$digit] . ' ';
            }
        }

        return trim($hasil);
    }
@endphp

@foreach ($laporan as $siswa)
<div class="page">
    <div class="header">
        <div class="header-left">KEPOLISIAN NEGARA REPUBLIK INDONESIA</div>
        <div class="header-right">
            <strong>LAMPIRAN II</strong><br>
            No. Sertifikat : <strong>2501222201</strong><br>
            Nama Siswa : {{ $siswa['nama_siswa'] }}<br>
            Pangkat/NRP/NIP : {{ $siswa['nip'] }}<br>
            Nama Dik : {{$siswa['jurusan']}}<br>
            PNS POLRI {{$siswa['kelas']}}<br>
            POLRI {{ $siswa['kelas']}} - {{$siswa['tahun']}} 
        </div>
    </div>

    <div class="title">Rekapitulasi Nilai</div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO.</th>
                <th rowspan="2">ASPEK YANG DINILAI</th>
                <th colspan="2">NILAI</th>
            </tr>
            <tr>
                <th>ANGKA</th>
                <th>HURUF</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>I.</td>
                <td>KEPRIBADIAN</td>
                <td>{{ number_format(floatval(str_replace(',', '.', $siswa['kepribadian'])), 2, ',', '.') }}</td>
                <td>{{ ucwords(terbilang($siswa['kepribadian'])) }}</td>
            </tr>
            <tr>
                <td>II.</td>
                <td>INTELEK</td>
                <td>{{ number_format(floatval(str_replace(',', '.', $siswa['intelek'])), 2, ',', '.') }}</td>
                <td>{{ ucwords(terbilang($siswa['intelek'])) }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>NILAI AKHIR</strong></td>
                <td><strong>{{ number_format(floatval(str_replace(',', '.', $siswa['rata_rata'])), 2, ',', '.') }}</strong></td>
                <td><strong>{{ ucwords(terbilang($siswa['rata_rata'])) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>KEPALA SEKOLAH BAHASA LEMDIKLAT POLRI</p>
        <div class="ttd">
            {{-- <img src="{{ public_path('ttd.png') }}"> --}} <!-- tanda tangan jika ada -->
            <p><strong>JONI GETAMALA, S.H.</strong></p>
            <p>KOMISARIS BESAR POLISI NRP 70080447</p>
        </div>
    </div>
</div>

@if (!$loop->last)
<div style="page-break-after: always;"></div>
@endif
@endforeach

</body>
</html>
