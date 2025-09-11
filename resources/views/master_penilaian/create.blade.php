@extends('dashboard')

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
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="list-group list-group-horizontal" id="menu-penilaian">
                                    <a href="#" class="list-group-item list-group-item-action active" data-target="utama">Penilaian Utama</a>
                                    <a href="#" class="list-group-item list-group-item-action" data-target="formatif">Penilaian Formatif & Kehadiran</a>
                                </div>
                            </div>
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
                                                    <select name="progress" id="Jenis" class="form-control">
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
        var menu = document.getElementById('menu-penilaian');
        if (!menu) return;

        menu.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-target]');
            if (!link) return;
            e.preventDefault();

            // Toggle active menu state
            menu.querySelectorAll('a').forEach(function (a) { a.classList.remove('active'); });
            link.classList.add('active');

            // Show/Hide forms
            var target = link.getAttribute('data-target');
            var utama = document.getElementById('form-utama');
            var formatif = document.getElementById('form-formatif');
            if (target === 'utama') {
                utama.classList.remove('d-none');
                formatif.classList.add('d-none');
            } else {
                formatif.classList.remove('d-none');
                utama.classList.add('d-none');
            }
        });
    });
</script>
@endsection
