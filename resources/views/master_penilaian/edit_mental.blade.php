@extends('dashboard')

@section('konten')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Nilai Mental</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('penilaian.mental.update', $mental->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="siswa">Siswa</label>
                                        <select name="id_siswa" id="siswa" class="form-control">
                                            <option disabled selected>Pilih Siswa</option>
                                            @foreach ($siswa as $item)
                                                <option value="{{ $item->id_siswa }}" {{ old('id_siswa', $mental->id_siswa) == $item->id_siswa ? 'selected' : '' }}>
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
                                        <label for="nilai_mental">Nilai Mental</label>
                                        <input type="number" step="0.01" class="form-control @error('nilai_mental') is-invalid @enderror" id="nilai_mental" name="nilai_mental" value="{{ old('nilai_mental', $mental->nilai_mental) }}" required>
                                        @error('nilai_mental')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('penilaian.index', ['tab' => 'mental']) }}" class="btn btn-secondary mr-2">Kembali</a>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
