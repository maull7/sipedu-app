<!-- View: jurusan.edit -->
@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Tahun Ajaran</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        Edit Tahun Ajaran
                    </div>
                    <div class="card-body">
                        <form action="{{ route('master_tahun.update', $data->id_tahun) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="tahun_ajaran">Tahun AJaran</label>
                                <input type="text" id="tahun_ajaran" name="tahun_ajaran" class="form-control"
                                    value="{{ $data->tahun_ajaran }}" required>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ route('master_tahun.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
