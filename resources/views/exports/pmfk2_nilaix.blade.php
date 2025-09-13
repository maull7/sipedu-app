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
    <h3>Rekapitulasi PMFK &amp; Akademik (Nilai X)</h3>
    <table>
        <thead>
            <tr>
                <th class="nowrap">NIP</th>
                <th>Nama</th>
                <th>JK</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Mapel</th>
                <th class="nowrap">Total Akademik</th>
                <th class="nowrap">Rata-rata Akademik</th>
                <th class="nowrap">Nilai Mental</th>
                <th class="nowrap">X7 (70%)</th>
                <th class="nowrap">X3 (30%)</th>
                <th class="nowrap">TOTAL</th>
                <th class="nowrap">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach (($laporan ?? []) as $r)
                <?php $r = (array)$r; ?>
                <tr>
                    <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                    <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                    <td>{{ $r['jk'] ?? '-' }}</td>
                    <td>{{ $r['kelas'] ?? '-' }}</td>
                    <td>{{ $r['jurusan'] ?? '-' }}</td>
                    <td>{{ $r['mapel'] ?? '-' }}</td>
                    <td class="text-right">{{ $fmt($r['total_nilai_akademik_kategori'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_akademik'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_mental'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['x7'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['x3'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['total_akhir'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_akhir'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
<!-- End -->
</html>
