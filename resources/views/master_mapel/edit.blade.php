<!-- View: Mata Pelajaran.edit -->
@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Mata Pelajaran</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        Edit Data Mata Pelajaran
                    </div>
                    <div class="card-body">
                        <form action="{{ route('master_mapel.update', $data->id_pelajaran) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="Mapel">Mata Pelajaran</label>
                                <input type="text" id="Mapel" name="nama_mapel" class="form-control"
                                    value="{{ $data->nama_mapel }}" required>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ route('master_mapel.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
