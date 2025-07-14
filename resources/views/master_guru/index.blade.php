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
                        <h1 class="m-0">Master Guru</h1>
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
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#tambahGuruModal">
                    Tambah Guru
                </button>

                 <a href="{{route('guru.template')}}" class="btn btn-warning mb-3">Download Template</a>

                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#importGuru">
                    Import Excel
                </button>
                <!-- Tombol Ekspor -->

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Data Guru
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table id="tabel-data" class="table datatable table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th width="15%">Nama</th>
                                    <th width="15%">Alamat</th>
                                    <th width="10%">Jenis Kelamin</th>
                                    <th width="10%">NIP</th>

                                    <th width="15%">Email</th>

                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gurus as $guru)
                                    <tr>
                                        <td>{{ $guru->nama_guru }}</td>
                                        <td>{{ $guru->alamat_guru }}</td>
                                        <td>{{ $guru->jenis_kelamin }}</td>
                                        <td>{{ $guru->nip }}</td>

                                        <td>{{ $guru->email ? $guru->email : 'Belum ada Email' }}</td>

                                        <td>
                                            <a href="{{ route('master_guru.edit', $guru->id_guru) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#deleteModal{{ $guru->id_guru }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $guru->id_guru }}" tabindex="-1"
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
                                                        <form action="{{ route('master_guru.destroy', $guru->id_guru) }}"
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

     <div class="modal fade" id="importGuru" tabindex="-1" role="dialog" aria-labelledby="importGuruLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importGuruLabel">Import Data Guru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('guru.import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File Excel</label>
                            <input type="file" name="file"
                                class="form-control @error('file') is-invalid @enderror">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Guru -->
    <div class="modal fade" id="tambahGuruModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Tambah Guru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('master_guru.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_guru">Nama Guru</label>
                                    <input type="text" name="nama_guru"
                                        class="form-control @error('nama_guru') is-invalid @enderror"
                                        value="{{ old('nama_guru') }}">
                                    @error('nama_guru')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="alamat_guru">Alamat</label>
                                    <textarea name="alamat_guru" class="form-control @error('alamat_guru') is-invalid @enderror" rows="4">{{ old('alamat_guru') }}</textarea>
                                    @error('alamat_guru')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nip">NIP</label>
                                    <input type="text" name="nip"
                                        class="form-control @error('nip') is-invalid @enderror"
                                        value="{{ old('nip') }}">
                                    @error('nip')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
