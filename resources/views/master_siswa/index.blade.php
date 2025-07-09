@extends('dashboard')

@section('konten')
    <style>
        /* Existing styles... */

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

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Master Siswa</h1>
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
                <!-- Tombol Tambah Siswa -->
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#tambahSiswaModal">
                    Tambah Siswa
                </button>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Data Siswa
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-0">
                            <table id="tabel-data" class="table datatable table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th width="15%">Nama</th>
                                        <th width="15%">Alamat</th>
                                        <th width="5%">Jenis Kelamin</th>
                                        <th width="10%">NIP</th>
                                        <th width="10%">NIK</th>
                                        <th width="5%">Email</th>
                                        <th width="10%">Kelas</th>

                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswa as $data)
                                        <tr>
                                            <td>{{ $data->nama_siswa }}</td>
                                            <td>{{ $data->alamat_siswa }}</td>
                                            <td>
                                                {{ $data->jenis_kelamin }}
                                            </td>
                                            <td>{{ $data->nip }}</td>
                                            <td>{{ $data->nik }}</td>

                                            <td>{{ $data->email ? $data->email : 'belum ada email' }}</td>
                                            <td>{{ $data->nama_kelas ?? 'belum ada kelas' }}</td>

                                            <td>
                                                <a href="{{ route('master_siswa.edit', $data->id_siswa) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#deleteModal{{ $data->id_siswa }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $data->id_siswa }}" tabindex="-1"
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
                                                            jika anda menekan tombol hapus maka data akan terhapus
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Kembali</button>
                                                            <form
                                                                action="{{ route('master_siswa.destroy', $data->id_siswa) }}"
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
            </div>
        </section>
    </div>

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="tambahSiswaModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Tambah Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('master_siswa.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_siswa">Nama Siswa</label>
                                    <input type="text" name="nama_siswa"
                                        class="form-control @error('nama_siswa') is-invalid @enderror"
                                        value="{{ old('nama_siswa') }}">
                                    @error('nama_siswa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="alamat_siswa">Alamat</label>
                                    <textarea type="text" name="alamat_siswa" class="form-control @error('alamat_siswa') is-invalid @enderror"
                                        value="{{ old('alamat_siswa') }}" rows="4"></textarea>
                                    @error('alamat_siswa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
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
                                    <label for="nik">NIK</label>
                                    <input type="text" name="nik"
                                        class="form-control @error('nik') is-invalid @enderror"
                                        value="{{ old('nik') }}">
                                    @error('nik')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group" id="jurusan-group">
                                    <label for="id_kelas">Kelas</label>
                                    <select name="id_kelas" id="kelas"
                                        class="form-control @error('id_kelas') is-invalid @enderror">
                                        <option value="">Pilih kelas</option>
                                        @foreach ($kelas as $kelas)
                                            <option value="{{ $kelas->id_kelas }}"
                                                {{ old('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_kelas')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">email</label>
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

    <!-- Modal Impor Siswa -->

    <script type="text/javascript">
        $('.datatable').DataTable({
            responsive: true
        });
    </script>

    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#tambahSiswaModal').modal('show');
            });
        </script>
    @endif
@endsection
