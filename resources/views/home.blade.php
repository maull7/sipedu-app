@extends('dashboard')

@section('konten')
    <style>
        /* Base styles */
        .content-wrapper {
            background-color: #f8f9fc;
            padding: 1.5rem;
        }

        /* Card styles */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }

        /* Icon styles */
        .icon-bg {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .bg-success-light {
            background-color: rgba(78, 115, 223, 0.1);
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-success-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-info-light {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .icon-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        /* Badge styles */
        .badge-soft-success {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .badge-soft-warning {
            color: #ffc107;
            background-color: rgba(255, 193, 7, 0.1);
        }

        .badge-soft-success {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* Animation for wave */
        .wave-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .wave {
            width: 100%;
            height: 60px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .display-4 {
                font-size: 2rem;
            }
        }
    </style>

    <div class="content-wrapper">
        {{-- <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <!-- Indikator -->
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>

            <!-- Gambar -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="{{ url('img/carousel/bg1.jpg') }}" alt="Slide 1"
                        style="height: 200px; object-fit: cover;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="{{ url('img/carousel/bg2.jpg') }}" alt="Slide 2"
                        style="height: 200px; object-fit: cover;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="{{ url('img/carousel/bg3.jpg') }}" alt="Slide 3"
                        style="height: 200px; object-fit: contain; background-color: #f0f0f0;">
                </div>
            </div>

            <!-- Tombol Panah -->
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Sebelumnya</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Selanjutnya</span>
            </a>
        </div> --}}

        <div class="content-header">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-success">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-right text-muted">
                            <i class="far fa-clock mr-1"></i> Last updated: {{ now()->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Welcome Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-gradient-success text-white overflow-hidden shadow-lg">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="mr-4">
                                    <div class="avatar bg-white text-success rounded-circle p-3 shadow-sm">
                                        <i class="fas fa-user fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-weight-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                                    <p class="mb-0 opacity-75">Sistem Penilaian Terpadu</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <span class="badge badge-light font-weight-bold py-2 px-3">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        <span class="user-role" style="color: black;">
                                            @if (auth()->user()->role == 0)
                                                admin
                                            @else
                                                Guru
                                            @endif
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="wave-container">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none"
                                    class="wave">
                                    <path fill="rgba(255, 255, 255, 0.1)"
                                        d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,133.3C672,139,768,181,864,181.3C960,181,1056,139,1152,122.7C1248,107,1344,117,1392,122.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Overview -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0 mr-2">Analytics Overview</h5>
                            <div class="flex-grow-1 border-bottom"></div>
                        </div>
                    </div>
                </div>

                <!-- success Stats -->
                <div class="row">



                    {{-- Total Pengajuan --}}
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-bg bg-warning-light rounded-lg p-3 mr-3">
                                        <i class="fas fa-book-open text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted">Total Guru</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="font-weight-bold mb-0">
                                        {{ $data['total_guru'] }}
                                    </h3>
                                    <span class="badge badge-soft-warning ml-2">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent p-0">
                                <a href="{{ route('master_guru.index') }}" class="btn btn-link text-warning btn-block">
                                    Kelola Guru <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-bg bg-success-light rounded-lg p-3 mr-3">
                                        <i class="fas fa-book-open text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted">Total Siswa</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="font-weight-bold mb-0">
                                        {{ $data['total_siswa'] }}
                                    </h3>
                                    <span class="badge badge-soft-success ml-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent p-0">
                                <a href="{{ route('master_siswa.index') }}" class="btn btn-link text-success btn-block">
                                    Kelola Siswa <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-bg bg-danger-light rounded-lg p-3 mr-3">
                                        <i class="fas fa-book-open text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted">Total Admin</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="font-weight-bold mb-0">
                                        {{ $data['total_admin'] }}
                                    </h3>
                                    <span class="badge badge-soft-warning ml-2">
                                        <i class="fas fa-check-circle text-danger"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent p-0">
                                <a href="{{ route('master_user.index') }}" class="btn btn-link text-danger btn-block">
                                    Kelola Admin <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-bg bg-waprimaryrning-light rounded-lg p-3 mr-3">
                                        <i class="fas fa-book-open text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted">Total Kelas</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="font-weight-bold mb-0">
                                        {{ $data['total_kelas'] }}
                                    </h3>
                                    <span class="badge badge-soft-primary ml-2">
                                        <i class="fas fa-check-circle text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent p-0">
                                <a href="{{ route('master_kelas.index') }}" class="btn btn-link text-primary btn-block">
                                    Kelola Kelas <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>





                </div>


                <!-- Quick Links + Recent Activity -->
                <div class="row mt-2">
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white"><strong>Master</strong></div>
                            <div class="card-body py-3">
                                <div class="d-flex flex-wrap" style="gap:10px">
                                    <a href="{{ url('/master_user') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-user-shield mr-1"></i> User</a>
                                    <a href="{{ url('/master_guru') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chalkboard-teacher mr-1"></i> Guru</a>
                                    <a href="{{ url('/master_siswa') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-user-graduate mr-1"></i> Siswa</a>
                                    <a href="{{ url('/master_kelas') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chalkboard mr-1"></i> Kelas</a>
                                    <a href="{{ url('/master_mapel') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-book-open mr-1"></i> Mapel</a>
                                    <a href="{{ url('/master_kategori') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-layer-group mr-1"></i> Kategori</a>
                                    <a href="{{ url('/master_tahun') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-calendar-alt mr-1"></i> Tahun</a>
                                    <a href="{{ url('/master_jurusan') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-project-diagram mr-1"></i> Jurusan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white"><strong>Penilaian</strong></div>
                            <div class="card-body py-3">
                                <a href="{{ route('penilaian.index') }}" class="btn btn-sm btn-outline-success mr-2 mb-2"><i class="fas fa-list-alt mr-1"></i> Nilai Tersedia</a>
                                <a href="{{ url('/penilaian/create') }}" class="btn btn-sm btn-outline-primary mb-2"><i class="fas fa-plus-circle mr-1"></i> Tambah Nilai</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white"><strong>Laporan</strong></div>
                            <div class="card-body py-3">
                                <a href="{{ route('laporan.pmfk') }}" class="btn btn-sm btn-outline-info mr-2 mb-2"><i class="fas fa-file-alt mr-1"></i> PMFK</a>
                                <a href="{{ route('laporan.x') }}" class="btn btn-sm btn-outline-info mr-2 mb-2"><i class="fas fa-chart-line mr-1"></i> Nilai X</a>
                                <a href="{{ route('laporan.rekap') }}" class="btn btn-sm btn-outline-info mb-2"><i class="fas fa-clipboard-list mr-1"></i> Rekap</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <strong>Aktivitas Terbaru</strong>
                                <a href="{{ route('penilaian.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Kelas</th>
                                                <th>Mapel</th>
                                                <th>Kategori</th>
                                                <th class="text-right">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $rows = $recent_penilaian ?? []; @endphp
                                            @forelse ($rows as $n)
                                                @php
                                                    $nama     = data_get($n, 'nama_siswa', '-');
                                                    $kelas    = data_get($n, 'nama_kelas', '-');
                                                    $mapel    = data_get($n, 'nama_mapel', '-');
                                                    $kategori = data_get($n, 'kategori_penilaian', '-');
                                                    $nilaiRaw = data_get($n, 'nilai');
                                                @endphp
                                                <tr>
                                                    <td>{{ $nama }}</td>
                                                    <td>{{ $kelas }}</td>
                                                    <td>{{ $mapel }}</td>
                                                    <td>{{ $kategori }}</td>
                                                    <td class="text-right">{{ is_numeric($nilaiRaw) ? number_format((float)$nilaiRaw, 2, ',', '.') : ($nilaiRaw ?? '-') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted p-3">Belum ada aktivitas penilaian terbaru.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white"><strong>Ringkas</strong></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-user-shield mr-2 text-danger"></i> Admin: <strong>{{ $data['total_admin'] ?? 0 }}</strong></li>
                                    <li class="mb-2"><i class="fas fa-chalkboard-teacher mr-2 text-warning"></i> Guru: <strong>{{ $data['total_guru'] ?? 0 }}</strong></li>
                                    <li class="mb-2"><i class="fas fa-user-graduate mr-2 text-success"></i> Siswa: <strong>{{ $data['total_siswa'] ?? 0 }}</strong></li>
                                    <li class="mb-2"><i class="fas fa-chalkboard mr-2 text-primary"></i> Kelas: <strong>{{ $data['total_kelas'] ?? 0 }}</strong></li>
                                    <li class="mb-2"><i class="fas fa-book-open mr-2 text-info"></i> Mapel: <strong>{{ $data['total_mapel'] ?? 0 }}</strong></li>
                                    <li class="mb-2"><i class="fas fa-layer-group mr-2 text-secondary"></i> Kategori: <strong>{{ $data['total_kategori'] ?? 0 }}</strong></li>
                                    <li class="mb-0"><i class="fas fa-list-alt mr-2 text-success"></i> Penilaian: <strong>{{ $data['total_penilaian'] ?? 0 }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats Row -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0 mr-2">Module Management</h5>
                            <div class="flex-grow-1 border-bottom"></div>
                        </div>
                    </div>
                </div>


                <!-- System Stats -->

            </div>
        </section>
    </div>
@endsection
