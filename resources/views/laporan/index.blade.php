@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Sistem Laporan Penilaian</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <a href="{{ route('laporan.export.excel', request()->query()) }}" class="btn btn-success mb-3">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('laporan.pdf', request()->query()) }}" class="btn btn-danger mb-3">
                    <i class="fas fa-file-pdf"></i> Export Pdf
                </a>





                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i> Data
                    </div>
                    <div class="card-body">
                        <!-- Form Filter -->
                        <form action="{{ route('laporan.index') }}" method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="jurusan">Jurusan</label>
                                        <select name="jurusan" id="jurusan" class="form-control">
                                            <option value="">-- Semua Jurusan --</option>
                                            @foreach ($jurusanList as $j)
                                                <option value="{{ $j->id_jurusan }}" {{ request('jurusan') == $j->id_jurusan ? 'selected' : '' }}>
                                                    {{ $j->nama_jurusan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="kelas">Kelas</label>
                                        <select name="kelas" id="kelas" class="form-control">
                                            <option value="">-- Semua Kelas --</option>
                                            @foreach ($kelasList as $k)
                                                <option value="{{ $k->id_kelas }}" {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>
                                                    {{ $k->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="mapel">Mata Pelajaran</label>
                                        <select name="mapel" id="mapel" class="form-control">
                                            <option value="">-- Semua Mapel --</option>
                                            @foreach ($mapelList as $m)
                                                <option value="{{ $m->id_pelajaran }}" {{ request('mapel') == $m->id_pelajaran ? 'selected' : '' }}>
                                                    {{ $m->nama_mapel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </form>

                    
                      
                            <div class="table-responsive">
                                <table class="table table-hover datatable text-nowrap">
                                    <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Kelas</th>
                                                <th>Mata Pelajaran</th>
                                                @foreach ($kategori as $kategoriNama)
                                                    <th>{{ $kategoriNama }}</th>
                                                @endforeach
                                                <th>Total</th>
                                                <th>Rata-rata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($laporan as $siswa)
                                                <tr>
                                                    <td>{{ $siswa['nama_siswa'] }}</td>
                                                    <td>{{ $siswa['nip'] }}</td>
                                                    <td>{{ $siswa['jk'] }}</td>
                                                    <td>{{ $siswa['kelas'] }}</td>
                                                    <td>{{ $siswa['mapel'] }}</td>
                                                    @foreach ($kategori as $kategoriNama)
                                                        <td>
                                                            @if(isset($siswa[$kategoriNama]))
                                                                {{ number_format($siswa[$kategoriNama], 2, ',', '.') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td>{{ number_format($siswa['total'], 2, ',', '.') }}</td>
                                                    <td>{{ number_format($siswa['rata_rata'], 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                </table>
                            </div>
                      
                    </div>

                </div>


        </section>
    </div>



    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true
            });
        });
    </script>


@endsection
