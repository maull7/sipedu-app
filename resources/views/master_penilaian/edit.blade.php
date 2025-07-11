@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Nilai</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('penilaian.update', $penilaian->id_penilaian) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Siswa -->
                            <div class="form-group">
                                <label for="Siswa">Siswa</label>
                                <select name="id_siswa" id="Siswa" class="form-control">
                                    <option disabled selected>Pilih Siswa</option>
                                    @foreach ($siswa as $item)
                                        <option value="{{ $item->id_siswa }}" {{ $item->id_siswa == $penilaian->id_siswa ? 'selected' : '' }}>
                                            {{ $item->nama_siswa }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_siswa')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Mata Pelajaran -->
                            <div class="form-group">
                                <label for="Pelajaran">Pelajaran</label>
                                <select name="id_pelajaran" id="Pelajaran" class="form-control">
                                    <option disabled selected>Pilih Mata Pelajaran</option>
                                    @foreach ($mapel as $item)
                                        <option value="{{ $item->id_pelajaran }}" {{ $item->id_pelajaran == $penilaian->id_pelajaran ? 'selected' : '' }}>
                                            {{ $item->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_pelajaran')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Jenis Penilaian -->
                            <div class="form-group">
                                <label for="Jenis">Jenis Penilaian</label>
                                <select name="id_kategori_penilaian" id="Jenis" class="form-control">
                                    <option disabled selected>Pilih Jenis Penilaian</option>
                                    @foreach ($kategori as $item)
                                        <option value="{{ $item->id_kategori }}" {{ $item->id_kategori == $penilaian->id_kategori_penilaian ? 'selected' : '' }}>
                                            {{ $item->kategori_penilaian }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kategori_penilaian')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nilai -->
                            <div class="form-group">
                                <label for="nilai">Nilai</label>
                                <input type="text" class="form-control @error('nilai') is-invalid @enderror"
                                    id="nilai" name="nilai" value="{{ old('nilai', $penilaian->nilai) }}" required>
                                @error('nilai')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>

            </div>
        </section>
    </div>
@endsection
