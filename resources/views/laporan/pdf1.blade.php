<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai</title>
    <style>
        /* Umum */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .page {
            page-break-inside: avoid;
            margin-bottom: 10px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-left {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .header-right {
            font-size: 12px;
            line-height: 1.35;
            text-align: left;
        }

        /* Judul */
        .title {
            text-align: center;
            margin: 8px 0 4px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: bold;
        }

        /* Tabel transkrip */
        table.transcript-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 18px;
        }

        table.transcript-table th,
        table.transcript-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
            text-align: center;
        }

        table.transcript-table th {
            background: #eee;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        /* Tanda tangan: gunakan tabel agar stabil di PDF */
        .ttd-table {
            width: 100%;
            border: 0;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }

        .ttd-table td {
            vertical-align: top;
            width: 50%;
            padding: 0 10px;
        }

        .ttd-block {
            display: inline-block;
            width: 100%;
        }

        .ttd-title {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .ttd-sub {
            margin: 0;
            line-height: 1.3;
        }

        .ttd-space {
            /* ruang kosong untuk tanda tangan (sesuaikan jika perlu) */
            display: block;
            height: 68px;
        }

        .underline {
            text-decoration: underline;
        }

        /* Footer kecil (jika diperlukan) */
        .footer-note {
            margin-top: 6px;
            font-size: 11px;
        }

        /* Small tweaks for PDF rendering */
        img {
            max-width: 100%;
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
                $hasil .= $huruf[(int) $digit] . ' ';
            }

            if (isset($bilangan[1])) {
                $hasil .= 'Koma ';
                foreach (str_split($bilangan[1]) as $digit) {
                    $hasil .= $huruf[(int) $digit] . ' ';
                }
            }

            return trim($hasil);
        }
    @endphp
    @php
        $no = 1;
    @endphp
    @foreach ($laporan as $siswa)
        <div class="page">
            <div class="header">
                <div class="header-left">
                    KEPOLISIAN NEGARA REPUBLIK INDONESIA
                </div>

                <div class="header-right">
                    <strong>LAMPIRAN I</strong><br>
                    No. Sertifikat : <strong>250122220{{ $no++ }}</strong><br>
                    Nama Siswa : {{ $siswa['nama_siswa'] }}<br>
                    Pangkat/NRP/NIP : {{ $siswa['nip'] }}<br>
                    Nama Dik : {{ $siswa['jurusan'] }}<br>
                    PNS POLRI {{ $siswa['kelas'] }}<br>
                    POLRI {{ $siswa['kelas'] }} - {{ $siswa['tahun'] }}<br>
                    WAKTU PENILAIAN : {{ $siswa['progress'] }}
                </div>
            </div>

            <!-- Judul -->
            <div class="title">Daftar Nilai Akhir Intelek</div>
            <div class="subtitle">( TRANSKRIP )</div>

            <!-- Tabel transkrip -->
            <table class="transcript-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:6%;">NO.</th>
                        <th rowspan="2" style="text-align:center;">MATA PELAJARAN</th>
                        <th colspan="2" style="width:28%;">NILAI</th>
                    </tr>
                    <tr>
                        <th style="width:14%;">ANGKA</th>
                        <th style="width:14%;">HURUF</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $totalNilai = 0;
                        $jumlahMapel = count($siswa['nilai_kategori']);
                    @endphp

                    @foreach ($siswa['nilai_kategori'] as $kategori => $nilai)
                        @php
                            $nilaiFormatted = number_format(floatval($nilai), 2, ',', '.');
                            $totalNilai += floatval($nilai);
                        @endphp
                        <tr>
                            <td>{{ $no }}.</td>
                            <td class="text-left"><em>{{ $kategori }}</em></td>
                            <td>{{ $nilaiFormatted }}</td>
                            <td class="text-left">{{ ucwords(terbilang($nilai)) }}</td>
                        </tr>
                        @php $no++; @endphp
                    @endforeach

                    <!-- Jumlah -->
                    <tr>
                        <td></td>
                        <td class="text-left"><strong>JUMLAH</strong></td>
                        <td><strong>{{ number_format($totalNilai, 2, ',', '.') }}</strong></td>
                        <td class="text-left"><strong>{{ ucwords(terbilang($totalNilai)) }}</strong></td>
                    </tr>

                    <!-- Rata-rata -->
                    @php
                        $rataRata = $jumlahMapel > 0 ? $totalNilai / $jumlahMapel : 0;
                    @endphp
                    <tr>
                        <td></td>
                        <td class="text-left"><strong>RATA-RATA</strong></td>
                        <td><strong>{{ number_format($rataRata, 2, ',', '.') }}</strong></td>
                        <td class="text-left"><strong>{{ ucwords(terbilang($rataRata)) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Tanda tangan: pakai tabel dua kolom agar stabil di PDF -->
            <table class="ttd-table">
                <tr>
                    <td>
                        <div class="ttd-block">
                            <div class="ttd-title">MENGETAHUI</div>
                            <p class="ttd-sub">KEPALA SEKOLAH BAHASA LEMDIKLAT POLRI</p>

                            <span class="ttd-space"></span>

                            <p class="ttd-sub underline"><strong>JONI GETAMALA, S.H.</strong></p>
                            <p class="ttd-sub">KOMISARIS BESAR POLISI
                                NRP 70080447</p>
                        </div>
                    </td>

                    <td>
                        <div class="ttd-block">
                            <div class="ttd-title">PANITIA UJIAN</div>
                            <p class="ttd-sub">K E T U A</p>

                            <span class="ttd-space"></span>

                            <p class="ttd-sub underline"><strong>SAIFUL ANWAR, S.Sos.I., M.A.</strong></p>
                            <p class="ttd-sub">PEMBINA NIP.
                                198003302006041002</p>
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach

</body>

</html>
