<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #e9f2ff; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
        h3 { margin: 0 0 10px 0; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php $fmt = function ($v) { return is_numeric($v) ? number_format((float)$v, 2, ',', '.') : ($v ?? '-'); }; ?>
    </head>
<body>
    <h3>Rekapitulasi PMFK &amp; Akademik (Rekap)</h3>
    <table>
        <thead>
            <tr>
                <th class="nowrap">NIP</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Mapel</th>
                <th>MENDENGAR</th>
                <th>MEMBACA</th>
                <th>MENULIS</th>
                <th>BERBICARA</th>
                <th>TATA BAHASA</th>
                <th class="nowrap">JUMLAH AKADEMIK</th>
                <th class="nowrap">NILAI RATA-RATA AKADEMIK</th>
                <th class="nowrap">X7 (70%)</th>
                <th class="nowrap">RATA-RATA MENTAL</th>
                <th class="nowrap">X3 (30%)</th>
                <th class="nowrap">TOTAL</th>
                <th class="nowrap">Nilai Akhir</th>
                <th class="nowrap">Ranking</th>
                <th class="nowrap">Klasifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach (($laporan ?? []) as $r)
                <?php $r = (array)$r; ?>
                <tr>
                    <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                    <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                    <td>{{ $r['kelas'] ?? '-' }}</td>
                    <td>{{ $r['jurusan'] ?? '-' }}</td>
                    <td>{{ $r['mapel'] ?? '-' }}</td>
                    <td class="text-right">{{ $fmt($r['MENDENGAR'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['MEMBACA'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['MENULIS'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['BERBICARA'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['TATA BAHASA'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['JUMLAH AKADEMIK'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['NILAI RATA-RATA AKADEMIK'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['X7'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['NILAI RATA-RATA MENTAL'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['X3'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['TOTAL'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['Nilai Akhir'] ?? 0) }}</td>
                    <td class="text-center">{{ $r['Ranking'] ?? '-' }}</td>
                    <td class="text-center">{{ $r['Klasifikasi'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
