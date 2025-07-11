<table>
    <thead>
        <tr>
            <th>Ranking</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>JK</th>
            <th>Kelas</th>
            @foreach ($kategori as $kat)
                <th>{{ $kat }}</th>
            @endforeach
            <th>Total</th>
            <th>Rata-rata</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan as $siswa)
            <tr>
                <td>{{ $siswa['ranking'] }}</td>
                <td>{{ $siswa['nama_siswa'] }}</td>
                <td>{{ $siswa['nip'] }}</td>
                <td>{{ $siswa['jk'] }}</td>
                <td>{{ $siswa['kelas'] }}</td>
                @foreach ($kategori as $kat)
                    <td>{{ $siswa[$kat] ?? '-' }}</td>
                @endforeach
                <td>{{ $siswa['total'] }}</td>
                <td>{{ $siswa['rata_rata'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
