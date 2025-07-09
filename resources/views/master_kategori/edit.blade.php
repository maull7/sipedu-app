<!-- View: jurusan.edit -->
@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Kategori Penilaian</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        Edit Data Kategori Penilaian
                    </div>
                    <div class="card-body">
                        <form action="{{ route('master_kategori.update', $data->id_kategori) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="kategori_penilaian">Kategori Penilaian</label>
                                <input type="text" id="kategori_penilaian" name="kategori_penilaian" class="form-control"
                                    value="{{ $data->kategori_penilaian }}" required>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ route('master_kategori.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
