<!-- View: jurusan.edit -->
@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Kelas</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        Edit Data Kelas
                    </div>
                    <div class="card-body">
                        <form action="{{ route('master_kelas.update', $data->id_kelas) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="nama_jabatan">Nama Kelas</label>
                                <input type="text" name="nama_kelas" class="form-control" value="{{ $data->nama_kelas }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="jrsn">Jurusan</label>
                                <select name="id_jurusan" id="jrsn" class="form-control" required>

                                    @foreach ($jurusan as $item)
                                        <option value="{{ $item->id_jurusan }}"
                                            {{ $item->id_jurusan == $data->id_jurusan ? 'selected' : '' }}>
                                            {{ $item->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Id">Tahun Ajaran</label>
                                <select name="id_tahun" id="Id" class="form-control" required>

                                    @foreach ($tahun as $item)
                                        <option value="{{ $item->id_tahun }}"
                                            {{ $item->id_tahun == $data->id_tahun ? 'selected' : '' }}>
                                            {{ $item->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-danger">Simpan</button>
                                <a href="{{ route('master_kelas.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
