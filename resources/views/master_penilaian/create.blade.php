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
                <form action="{{route('penilaian.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Siswa">Siswa</label>
                                <select name="id_siswa" id="Siswa" class="form-control">
                                    <option>Pilih Siswa</option>
                                    @foreach ($siswa as $item )
                                         <option value="{{$item->id_siswa}}">{{$item->nama_siswa}}</option>
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
                                    @foreach ($mapel as $item )
                                         <option value="{{$item->id_pelajaran}}">{{$item->nama_mapel}}</option>
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
                                    @foreach ($kategori as $item )
                                         <option value="{{$item->id_kategori}}">{{$item->kategori_penilaian}}</option>
                                    @endforeach
                                </select>
                                @error('id_kategori_penilaian')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nilai">Nilai</label>
                                <input type="text" class="form-control @error('nilai') is-invalid @enderror"
                                    id="nilai" name="nilai" required>
                                @error('nilai')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
