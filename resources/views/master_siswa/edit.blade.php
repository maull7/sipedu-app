@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Siswa</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('master_siswa.update', $siswa->id_siswa) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_siswa">Nama Siswa</label>
                                        <input type="text" name="nama_siswa"
                                            class="form-control @error('nama_siswa') is-invalid @enderror"
                                            value="{{ old('nama_siswa', $siswa->nama_siswa) }}">
                                        @error('nama_siswa')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat_siswa">Alamat</label>
                                        <textarea name="alamat_siswa" class="form-control @error('alamat_siswa') is-invalid @enderror" rows="4">{{ old('alamat_siswa', $siswa->alamat_siswa) }}</textarea>
                                        @error('alamat_siswa')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select name="jenis_kelamin"
                                            class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                            <option value=" Laki-laki" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>
                                                Laki-laki
                                            </option>
                                            <option value="Perempuan" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>
                                                Perempuan
                                            </option>
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
                                            value="{{ old('nip', $siswa->nip) }}">
                                        @error('nip')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nik">NIK</label>
                                        <input type="text" name="nik"
                                            class="form-control @error('nik') is-invalid @enderror"
                                            value="{{ old('nik', $siswa->nik) }}">
                                        @error('nik')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email siswa</label>
                                        <input type="text" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $siswa->email) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group" id="kelas-group">
                                        <label for="id_kelas">Kelas</label>
                                        <select name="id_kelas" id="kelas"
                                            class="form-control @error('id_kelas') is-invalid @enderror">
                                            <option value="">Pilih kelas</option>
                                            @foreach ($kelas as $kls)
                                                <option value="{{ $kls->id_kelas }}"
                                                    {{ $siswa->id_kelas == $kls->id_kelas ? 'selected' : '' }}>
                                                    {{ $kls->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                        @error('id_kelas')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="{{ route('master_siswa.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            $('input[name="foto"]').change(function(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('fotoPreview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    </script>
@endsection
