<!-- View: jurusan.edit -->
@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Jurusan</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        Edit Data Jurusan
                    </div>
                    <div class="card-body">
                        <form action="{{ route('master_jurusan.update', $data->id_jurusan) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="tahun_ajaran">Jurusan</label>
                                <input type="text" id="nama_jurusan" name="nama_jurusan" class="form-control"
                                    value="{{ $data->nama_jurusan }}" required>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ route('master_jurusan.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
