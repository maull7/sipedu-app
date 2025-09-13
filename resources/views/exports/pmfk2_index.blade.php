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
    <h3>Rekapitulasi PMFK &amp; Akademik (Index)</h3>
    <table>
        <thead>
            <tr>
                <th class="nowrap">NIP</th>
                <th>Nama</th>
                <th class="nowrap">JK</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Mapel</th>
                <th>Kategori</th>
                <th>Progress Test</th>
                <th>Middle Test</th>
                <th>Final Test</th>
                <th>Formatif</th>
                <th>Kehadiran</th>
                <th class="nowrap">10% (PT)</th>
                <th class="nowrap">30% (MT)</th>
                <th class="nowrap">40% (FT)</th>
                <th class="nowrap">10% Formatif</th>
                <th class="nowrap">10% Kehadiran</th>
                <th class="nowrap">Nilai Akademik</th>
            </tr>
        </thead>
        <tbody>
            @foreach (($laporan ?? []) as $r)
                <?php $r = (array)$r; ?>
                <tr>
                    <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                    <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                    <td>{{ $r['jk'] ?? $r['jenis_kelamin'] ?? '-' }}</td>
                    <td>{{ $r['kelas'] ?? '-' }}</td>
                    <td>{{ $r['jurusan'] ?? '-' }}</td>
                    <td>{{ $r['mapel'] ?? '-' }}</td>
                    <td>{{ $r['kategori_penilaian'] ?? '-' }}</td>
                    <td class="text-right">{{ $fmt($r['Progress Test'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['Middle Test'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['Final Test'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_formatif'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_kehadiran'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['10%'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['30%'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['40%'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['10%_formatif'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['10%_kehadiran'] ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r['nilai_akademik'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
