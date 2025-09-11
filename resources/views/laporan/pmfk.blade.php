@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Sistem Laporan Penilaian Pmfk</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">


                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i> Data Penilaian Siswa Pmfk
                    </div>
                    <div class="card-body">
                        <!-- Form Filter -->
                        <form action="{{ route('laporan.pmfk') }}" method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="kelas">Kelas</label>
                                    <select name="kelas" id="kelas" class="form-control">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach ($kelasList as $m)
                                            <option value="{{ $m->id_kelas }}"
                                                {{ request('kelas') == $m->id_kelas ? 'selected' : '' }}>
                                                {{ $m->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Filter Mata Pelajaran --}}
                                <div class="col-md-3">
                                    <label for="mapel">Mata Pelajaran</label>
                                    <select name="mapel" id="mapel" class="form-control">
                                        <option value="">-- Semua Mata Pelajaran --</option>
                                        @foreach ($mapelList as $m)
                                            <option value="{{ $m->id_pelajaran }}"
                                                {{ request('mapel') == $m->id_pelajaran ? 'selected' : '' }}>
                                                {{ $m->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Kategori Penilaian --}}
                                <div class="col-md-3">
                                    <label for="kategori">Kategori Penilaian</label>
                                    <select name="kategori" id="kategori" class="form-control">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach ($kategoriList as $k)
                                            <option value="{{ $k->id_kategori }}"
                                                {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}>
                                                {{ $k->kategori_penilaian }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Tombol Filter --}}
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Informasi Filter Aktif --}}
                        @if (request()->filled('mapel') || request()->filled('kategori'))
                            <div class="alert alert-info">
                                <strong>Filter Aktif:</strong>
                                @if (request()->filled('mapel'))
                                    @php
                                        $selectedMapel = $mapelList->firstWhere('id_pelajaran', request('mapel'));
                                    @endphp
                                    Mata Pelajaran: <span
                                        class="badge badge-primary">{{ $selectedMapel->nama_mapel ?? 'Tidak diketahui' }}</span>
                                @endif
                                @if (request()->filled('kategori'))
                                    @php
                                        $selectedKategori = $kategoriList->firstWhere(
                                            'id_kategori',
                                            request('kategori'),
                                        );
                                    @endphp
                                    Kategori: <span
                                        class="badge badge-success">{{ $selectedKategori->kategori_penilaian ?? 'Tidak diketahui' }}</span>
                                @endif
                                <a href="{{ route('laporan.pmfk') }}" class="btn btn-sm btn-outline-secondary ml-2">
                                    <i class="fas fa-times"></i> Reset Filter
                                </a>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover datatable text-nowrap">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>NIP</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Progress Test<br><small class="text-muted">(10%)</small>
                                        </th>
                                        <th class="text-center">Middle Test<br><small class="text-muted">(30%)</small></th>
                                        <th class="text-center">Final Test<br><small class="text-muted">(40%)</small></th>
                                        <th class="text-center bg-light"><strong>Nilai Akademik</strong><br><small
                                                class="text-muted">(80%)</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($laporan as $index => $siswa)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $siswa['nama_siswa'] }}</td>
                                            <td>{{ $siswa['nip'] }}</td>
                                            <td>
                                                @if ($siswa['jk'] == 'Laki-laki')
                                                    <span class="badge badge-info">Laki-laki</span>
                                                @else
                                                    <span class="badge badge-warning">Perempuan</span>
                                                @endif
                                            </td>
                                            <td>{{ $siswa['kelas'] }}</td>
                                            <td>{{ $siswa['jurusan'] }}</td>
                                            <td>{{ $siswa['mapel'] }}</td>
                                            <td>{{ $siswa['kategori_penilaian'] }}</td>
                                            <td class="text-center">
                                                @if ($siswa['10%'] > 0)
                                                    {{ number_format($siswa['10%'], 1, ',', '.') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($siswa['30%'] > 0)
                                                    {{ number_format($siswa['30%'], 1, ',', '.') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($siswa['40%'] > 0)
                                                    {{ number_format($siswa['40%'], 1, ',', '.') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center bg-light">
                                                <strong>
                                                    @if ($siswa['nilai_akademik'] > 0)
                                                        {{ number_format($siswa['nilai_akademik'], 1, ',', '.') }}
                                                        @php
                                                            $grade = '';
                                                            if ($siswa['nilai_akademik'] >= 85) {
                                                                $grade = 'text-success';
                                                            } elseif ($siswa['nilai_akademik'] >= 75) {
                                                                $grade = 'text-info';
                                                            } elseif ($siswa['nilai_akademik'] >= 65) {
                                                                $grade = 'text-warning';
                                                            } else {
                                                                $grade = 'text-danger';
                                                            }
                                                        @endphp
                                                        <span class="{{ $grade }}">
                                                            @if ($siswa['nilai_akademik'] >= 85)
                                                                A
                                                            @elseif($siswa['nilai_akademik'] >= 75)
                                                                B
                                                            @elseif($siswa['nilai_akademik'] >= 65)
                                                                C
                                                            @else
                                                                D
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <h5>Tidak ada data</h5>
                                                    <p>Data penilaian tidak ditemukan dengan filter yang dipilih.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Informasi Summary --}}
                        @if (count($laporan) > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Total Data:</strong> {{ count($laporan) }} siswa
                                        @if (request()->filled('mapel') || request()->filled('kategori'))
                                            (berdasarkan filter yang dipilih)
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [1, 'asc']
                ], // Sort by nama siswa
                columnDefs: [{
                        orderable: false,
                        targets: 0
                    } // Disable sorting for No column
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        });
    </script>
@endsection
