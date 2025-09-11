@extends('dashboard')

@section('style')
<style>
    .penilaian-nav .btn {
        transition: all 0.3s ease-in-out;
        border-radius: 30px;
    }

    .penilaian-nav .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .fade-in {
        animation: fadeIn 0.4s ease forwards;
    }

    .fade-out {
        animation: fadeOut 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: translateY(5px);
        }
    }
</style>
@endsection

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Membuat Penilaian</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="penilaian-nav text-center mb-4">
                            <div class="btn-group btn-group-lg shadow-sm" role="group" id="menu-penilaian">
                                <a href="#" class="btn btn-primary active" data-target="utama">
                                    <i class="fas fa-star me-1"></i> Penilaian Utama
                                </a>
                                <a href="#" class="btn btn-outline-primary" data-target="formatif">
                                    <i class="fas fa-clipboard-check me-1"></i> Formatif & Kehadiran
                                </a>
                                <a href="#" class="btn btn-outline-primary" data-target="mental">
                                    <i class="fas fa-brain me-1"></i> Nilai Mental
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="form-utama">
                                    <form action="{{ route('penilaian.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="form_type" value="utama">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Siswa">Siswa</label>
                                                    <select name="id_siswa" id="Siswa" class="form-control">
                                                        <option>Pilih Siswa</option>
                                                        @foreach ($siswa as $item)
                                                            <option value="{{ $item->id_siswa }}">{{ $item->nama_siswa }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_siswa')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="Pelajaran">Pelajaran</label>
                                                    <select name="id_pelajaran" id="Pelajaran" class="form-control">
                                                        <option>Pilih Mata Pelajaran</option>
                                                        @foreach ($mapel as $item)
                                                            <option value="{{ $item->id_pelajaran }}">{{ $item->nama_mapel }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_pelajaran')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Jenis">Jenis Penilaian</label>
                                                    <select name="id_kategori_penilaian" id="Jenis" class="form-control">
                                                        <option>Pilih Jenis Penilaian</option>
                                                        @foreach ($kategori as $item)
                                                            <option value="{{ $item->id_kategori }}">{{ $item->kategori_penilaian }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_kategori_penilaian')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="nilai">Nilai</label>
                                                    <input type="text" class="form-control @error('nilai') is-invalid @enderror" id="nilai" name="nilai" required>
                                                    @error('nilai')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Kep">Nilai Kepribadian</label>
                                                    <input type="text" class="form-control @error('kepribadian') is-invalid @enderror" id="Kep" name="kepribadian" required>
                                                    @error('kepribadian')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="intelek">Nilai Intelektual</label>
                                                    <input type="text" class="form-control @error('intelek') is-invalid @enderror" id="intelek" name="intelek" required>
                                                    @error('intelek')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="progress">Waktu Penilaian</label>
                                                    <select name="progress" id="progress" class="form-control">
                                                        <option>Pilih waktu Penilaian</option>
                                                        @foreach ($progress as $item)
                                                            <option value="{{ $item }}">{{ $item }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('progress')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer px-0">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>

                                <div id="form-formatif" class="d-none">
                                    <form action="{{ route('penilaian.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="form_type" value="formatif">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Siswa2">Siswa</label>
                                                    <select name="id_siswa" id="Siswa2" class="form-control">
                                                        <option>Pilih Siswa</option>
                                                        @foreach ($siswa as $item)
                                                            <option value="{{ $item->id_siswa }}">{{ $item->nama_siswa }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_siswa')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Jenis2">Kategori Penilaian</label>
                                                    <select name="id_kategori_penilaian" id="Jenis2" class="form-control">
                                                        <option>Pilih Kategori</option>
                                                        @foreach ($kategori as $item)
                                                            <option value="{{ $item->id_kategori }}">{{ $item->kategori_penilaian }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_kategori_penilaian')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nilai_formatif">Nilai Formatif</label>
                                                    <input type="number" step="0.01" class="form-control @error('nilai_formatif') is-invalid @enderror" id="nilai_formatif" name="nilai_formatif" required>
                                                    @error('nilai_formatif')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nilai_kehadiran">Nilai Kehadiran</label>
                                                    <input type="number" step="0.01" class="form-control @error('nilai_kehadiran') is-invalid @enderror" id="nilai_kehadiran" name="nilai_kehadiran" required>
                                                    @error('nilai_kehadiran')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer px-0">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>

                                <div id="form-mental" class="d-none">
                                    <form action="{{ route('penilaian.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="form_type" value="mental">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Siswa3">Siswa</label>
                                                    <select name="id_siswa" id="Siswa3" class="form-control">
                                                        <option>Pilih Siswa</option>
                                                        @foreach ($siswa as $item)
                                                            <option value="{{ $item->id_siswa }}">{{ $item->nama_siswa }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_siswa')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nilai_mental_input">Nilai Mental</label>
                                                    <input type="number" step="0.01" class="form-control @error('nilai_mental') is-invalid @enderror" id="nilai_mental_input" name="nilai_mental" required>
                                                    @error('nilai_mental')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer px-0">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('menu-penilaian');
    if (!menu) return;

    menu.addEventListener('click', function (e) {
        const link = e.target.closest('a[data-target]');
        if (!link) return;
        e.preventDefault();

        // toggle active
        menu.querySelectorAll('a').forEach(a => a.classList.remove('active', 'btn-primary'));
        menu.querySelectorAll('a').forEach(a => a.classList.add('btn-outline-primary'));
        link.classList.remove('btn-outline-primary');
        link.classList.add('active', 'btn-primary');

        // animasi transisi form
        const target = link.getAttribute('data-target');
        ['utama', 'formatif', 'mental'].forEach(id => {
            const form = document.getElementById('form-' + id);
            if (form) {
                form.classList.add('d-none', 'fade-out');
                form.classList.remove('fade-in');
            }
        });

        const showForm = document.getElementById('form-' + target);
        if (showForm) {
            showForm.classList.remove('d-none', 'fade-out');
            showForm.classList.add('fade-in');
        }
    });
});
</script>
@endsection
