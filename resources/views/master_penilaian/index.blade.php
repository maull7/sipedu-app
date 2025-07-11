@extends('dashboard')

@section('konten')
    <style>
        /* Responsive table styles */
        .table-responsive {
            margin-bottom: 1rem;
            -webkit-overflow-scrolling: touch;
        }

        /* Custom scrollbar for better UX */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Ensure buttons in action column stay in one line */
        .btn-group-sm>.btn,
        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        /* Responsive text handling */
        .text-nowrap {
            white-space: nowrap !important;
        }

        @media screen and (max-width: 768px) {

            .table td,
            .table th {
                padding: .5rem;
            }

            .btn-sm {
                padding: .2rem .4rem;
                font-size: .775rem;
            }
        }
    </style>
    <div class="content-wrapper">
        <!-- Banner Image -->


        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Penilaian</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show floating-alert" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Tombol Tambah Guru -->
                <a href="{{route('penilaian.create')}}" class="btn btn-success mb-3">
                    Tambah Nilai
                </a>
                <!-- Tombol Ekspor -->
                <form method="GET" action="{{ route('penilaian.index') }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select name="pelajaran" class="form-control" onchange="this.form.submit()">
                                <option value="">Filter Pelajaran</option>
                                @foreach ($mapel as $m)
                                    <option value="{{ $m->id_pelajaran }}" {{ request('pelajaran') == $m->id_pelajaran ? 'selected' : '' }}>
                                        {{ $m->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>


                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Data Nilai
                    </div>
                    <div class="card-body table-responsive">
                        <table id="tabel-data" class="table datatable table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th width="15%">Nama Siswa</th>
                                    <th width="15%">Kelas</th>
                                    <th width="15%">Pelajaran</th>
                                    <th width="10%">Jenis Penilaian</th>
                                    <th width="10%">Nilai</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $i)
                                    <tr>
                                        <td>{{ $i->nama_siswa }}</td>
                                        <td>{{ $i->nama_kelas }}</td>
                                        <td>{{ $i->nama_mapel }}</td>
                                        <td>{{ $i->kategori_penilaian }}</td>

                                        <td>{{ $i->nilai }}</td>

                                        <td>
                                            <a href="{{ route('penilaian.edit', $i->id_penilaian) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#deleteModal{{ $i->id_penilaian }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $i->id_penilaian }}" tabindex="-1"
                                            aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Jika Anda menekan tombol hapus maka data akan terhapus.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Kembali</button>
                                                        <form action="{{ route('penilaian.destroy', $i->id_penilaian) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Tambah Guru -->
  

    <!-- Modal Impor Guru -->

    <script type="text/javascript">
        $('.datatable').DataTable({
            responsive: true
        });
    </script>

    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#tambahGuruModal').modal('show');
            });
        </script>
    @endif
@endsection
