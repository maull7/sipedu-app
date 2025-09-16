@extends('dashboard')

@section('konten')
    <style>
        /* Responsive table styles */
        .table-responsive {
            margin-bottom: 1rem;
            -webkit-overflow-scrolling: touch;
        }

        /* Penilaian tabs styling (match create view) */
        .penilaian-nav .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border: 1px solid #dee2e6;
            margin: 0 4px;
        }
        .penilaian-nav .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .penilaian-nav .btn.active {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        .penilaian-nav .btn:not(.active) {
            background-color: #fff;
            color: #495057;
        }
        .penilaian-nav .btn:not(.active):hover {
            color: #28a745;
            border-color: #28a745;
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
                        <h1 class="m-0">Data Penilaian</h1>
                        <div class="penilaian-nav mt-3">
                            <div class="btn-group" role="group" id="menu-penilaian-index">
                                <a href="#" class="btn active" data-target="utama">
                                    <i class="fas fa-star me-1"></i> Penilaian Utama
                                </a>
                                <a href="#" class="btn" data-target="formatif">
                                    <i class="fas fa-clipboard-check me-1"></i> Formatif & Kehadiran
                                </a>
                                <a href="#" class="btn" data-target="mental">
                                    <i class="fas fa-brain me-1"></i> Nilai Mental
                                </a>
                            </div>
                        </div>
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
                <!-- Tab: Utama -->
                <div id="tab-utama">

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
                            Data Nilai Utama
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table datatable table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th width="15%">Nama Siswa</th>
                                        <th width="15%">Kelas</th>
                                        <th width="15%">Pelajaran</th>
                                        <th width="10%">Jenis Penilaian</th>
                                        <th width="10%">Nilai</th>
                                        <th width="10%">Waktu Penilaian</th>
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
                                            <td>{{ $i->progress }}</td>
                                            <td>
                                                <a href="{{ route('penilaian.edit', $i->id_penilaian) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $i->id_penilaian }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $i->id_penilaian }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">Jika Anda menekan tombol hapus maka data akan terhapus.</div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                                            <form action="{{ route('penilaian.destroy', $i->id_penilaian) }}" method="POST" style="display:inline;">
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

                    <!-- Import Excel - Penilaian Utama -->
                    <div class="card card-outline card-primary mt-3">
                        <div class="card-header py-2">
                            <strong>Import Excel - Penilaian Utama</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">Gunakan template berikut dan isi sesuai kolom yang tersedia.</p>
                            <a href="{{ route('export.template.penilaian') }}" class="btn btn-sm btn-info mb-3">Download Template Excel</a>
                            <form action="{{ route('import.nilai') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-sm-8 col-md-9 mb-2">
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-4 col-md-3 mb-2 text-end">
                                    <button type="submit" class="btn btn-primary w-100">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab: Formatif -->
                <div id="tab-formatif" class="d-none">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Penilaian Formatif & Kehadiran
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table datatable table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th width="20%">Nama Siswa</th>
                                        <th width="15%">Kelas</th>
                                        <th width="20%">Kategori Penilaian</th>
                                        <th width="15%">Nilai Formatif</th>
                                        <th width="15%">Nilai Kehadiran</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataFormatif as $f)
                                        <tr>
                                            <td>{{ $f->nama_siswa }}</td>
                                            <td>{{ $f->nama_kelas }}</td>
                                            <td>{{ $f->kategori_penilaian }}</td>
                                            <td>{{ $f->nilai_formatif }}</td>
                                            <td>{{ $f->nilai_kehadiran }}</td>
                                            <td>
                                                <a href="{{ route('penilaian.formatif.edit', $f->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteFormatifModal{{ $f->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="deleteFormatifModal{{ $f->id }}" tabindex="-1" aria-labelledby="deleteFormatifModalLabel{{ $f->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteFormatifModalLabel{{ $f->id }}">Konfirmasi</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">Jika Anda menekan tombol hapus maka data akan terhapus.</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                                        <form action="{{ route('penilaian.formatif.destroy', $f->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Import Excel - Formatif & Kehadiran -->
                    <div class="card card-outline card-success mt-3">
                        <div class="card-header py-2">
                            <strong>Import Excel - Formatif & Kehadiran</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">Isi kolom: NIP NRP, KATEGORI NILAI, NILAI FORMATIF, NILAI KEHADIRAN.</p>
                            <a href="{{ route('export.template.penilaian.formatif') }}" class="btn btn-sm btn-info mb-3">Download Template Excel</a>
                            <form action="{{ route('import.nilai.formatif') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-sm-8 col-md-9 mb-2">
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-4 col-md-3 mb-2 text-end">
                                    <button type="submit" class="btn btn-primary w-100">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab: Mental -->
                <div id="tab-mental" class="d-none">

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Nilai Mental
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table datatable table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th width="20%">Nama Siswa</th>
                                        <th width="15%">Kelas</th>
                                        <th width="15%">Nilai Mental</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataMental as $m)
                                        <tr>
                                            <td>{{ $m->nama_siswa }}</td>
                                            <td>{{ $m->nama_kelas }}</td>
                                            <td>{{ $m->nilai_mental }}</td>
                                            <td>
                                                <a href="{{ route('penilaian.mental.edit', $m->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteMentalModal{{ $m->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="deleteMentalModal{{ $m->id }}" tabindex="-1" aria-labelledby="deleteMentalModalLabel{{ $m->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteMentalModalLabel{{ $m->id }}">Konfirmasi</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">Jika Anda menekan tombol hapus maka data akan terhapus.</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                                        <form action="{{ route('penilaian.mental.destroy', $m->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Import Excel - Nilai Mental -->
                    <div class="card card-outline card-secondary mt-3">
                        <div class="card-header py-2">
                            <strong>Import Excel - Nilai Mental</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                Isi kolom: <strong>NIP NRP</strong>, <strong>NILAI MENTAL</strong>. Kolom <strong>Kelas</strong> tidak perlu diisi.
                            </p>
                            <a href="{{ route('export.template.nilai.mental') }}" class="btn btn-sm btn-info mb-3">Download Template Excel</a>
                            <form action="{{ route('import.nilai.mental') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-sm-8 col-md-9 mb-2">
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-4 col-md-3 mb-2 text-end">
                                    <button type="submit" class="btn btn-primary w-100">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById('menu-penilaian-index');
            if (!menu) return;

            menu.addEventListener('click', function (e) {
                const link = e.target.closest('a[data-target]');
                if (!link) return;
                e.preventDefault();

                // toggle active
                menu.querySelectorAll('a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');

                const target = link.getAttribute('data-target');
                const sections = {
                    utama: document.getElementById('tab-utama'),
                    formatif: document.getElementById('tab-formatif'),
                    mental: document.getElementById('tab-mental'),
                };

                Object.values(sections).forEach(el => el && el.classList.add('d-none'));
                if (sections[target]) sections[target].classList.remove('d-none');
            });

            // Set initial tab from query param ?tab=
            const params = new URLSearchParams(window.location.search);
            const initialTab = params.get('tab');
            if (initialTab && ['utama','formatif','mental'].includes(initialTab)) {
                const link = menu.querySelector(`a[data-target="${initialTab}"]`);
                if (link) link.click();
            }
        });
    </script>

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
