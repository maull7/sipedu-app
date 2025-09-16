@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Penilaian Formatif & Kehadiran</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('penilaian.formatif.update', $formatif->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="siswa">Siswa</label>
                                        <select name="id_siswa" id="siswa" class="form-control">
                                            <option disabled selected>Pilih Siswa</option>
                                            @foreach ($siswa as $item)
                                                <option value="{{ $item->id_siswa }}" {{ old('id_siswa', $formatif->id_siswa) == $item->id_siswa ? 'selected' : '' }}>
                                                    {{ $item->nama_siswa }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_siswa')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kategori">Kategori Penilaian</label>
                                        <select name="id_kategori_penilaian" id="kategori" class="form-control">
                                            <option disabled selected>Pilih Kategori Penilaian</option>
                                            @foreach ($kategori as $item)
                                                <option value="{{ $item->id_kategori }}" {{ old('id_kategori_penilaian', $formatif->id_kategori_penilaian) == $item->id_kategori ? 'selected' : '' }}>
                                                    {{ $item->kategori_penilaian }}
                                                </option>
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
                                        <input type="number" step="0.01" class="form-control @error('nilai_formatif') is-invalid @enderror" id="nilai_formatif" name="nilai_formatif" value="{{ old('nilai_formatif', $formatif->nilai_formatif) }}" required>
                                        @error('nilai_formatif')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nilai_kehadiran">Nilai Kehadiran</label>
                                        <input type="number" step="0.01" class="form-control @error('nilai_kehadiran') is-invalid @enderror" id="nilai_kehadiran" name="nilai_kehadiran" value="{{ old('nilai_kehadiran', $formatif->nilai_kehadiran) }}" required>
                                        @error('nilai_kehadiran')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('penilaian.index', ['tab' => 'formatif']) }}" class="btn btn-secondary mr-2">Kembali</a>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

