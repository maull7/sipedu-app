@extends('dashboard')

@section('style')
<style>
  .table-sticky thead th { position: sticky; top: 0; z-index: 1; background: #e9f2ff; }
  .nowrap { white-space: nowrap; }
  .text-right { text-align: right; }
  .badge-soft { background: #eef6ff; color: #0b5ed7; border: 1px solid #cfe2ff; }
  .page-title { font-weight: 700; letter-spacing: .2px; }
  .subtitle { color: #6c757d; }
</style>
@endsection

@section('konten')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1 class="m-0 page-title">Rekapitulasi PMFK & Akademik</h1>
          <div class="subtitle">Laporan Progress/Middle/Final dengan Formatif, Kehadiran, dan Mental</div>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mb-3">
        <div class="card-body">
          <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Mata Pelajaran</label>
              <select name="mapel" class="form-control">
                <option value="">Semua Mapel</option>
                @isset($mapelList)
                  @foreach ($mapelList as $m)
                    <option value="{{ $m->id_pelajaran }}" {{ (string)request('mapel') === (string)$m->id_pelajaran ? 'selected' : '' }}>
                      {{ $m->nama_mapel }}
                    </option>
                  @endforeach
                @endisset
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Kelas</label>
              <select name="kelas" class="form-control">
                <option value="">Semua Kelas</option>
                @isset($kelasList)
                  @foreach ($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ (string)request('kelas') === (string)$k->id_kelas ? 'selected' : '' }}>
                      {{ $k->nama_kelas }}
                    </option>
                  @endforeach
                @endisset
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Kategori Penilaian</label>
              <select name="kategori" class="form-control">
                <option value="">Semua Kategori</option>
                @isset($kategoriList)
                  @foreach ($kategoriList as $kat)
                    <option value="{{ $kat->id_kategori }}" {{ (string)request('kategori') === (string)$kat->id_kategori ? 'selected' : '' }}>
                      {{ $kat->kategori_penilaian }}
                    </option>
                  @endforeach
                @endisset
                @empty($kategoriList)
                  @isset($kategori)
                    @foreach ($kategori as $id => $nama)
                      <option value="{{ $id }}" {{ (string)request('kategori') === (string)$id ? 'selected' : '' }}>
                        {{ $nama }}
                      </option>
                    @endforeach
                  @endisset
                @endempty
              </select>
            </div>
            <div class="col-12 mt-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i> Terapkan Filter
              </button>
              <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          @php
            $first = $laporan[0] ?? null;
            $mode = 'unknown';
            if ($first) {
              if (is_array($first)) {
                if (array_key_exists('kategori_penilaian', $first) && array_key_exists('10%', $first)) {
                  $mode = 'index';
                } elseif (array_key_exists('JUMLAH AKADEMIK', $first)) {
                  $mode = 'rekap';
                } elseif (array_key_exists('nilai_final', $first)) {
                  $mode = 'nilaiX';
                }
              } elseif (is_object($first)) {
                if (isset($first->kategori_penilaian) && isset($first->{'10%'})) {
                  $mode = 'index';
                } elseif (isset($first->Nilai) && isset($first->Ranking)) {
                  $mode = 'rekap';
                } elseif (isset($first->nilai_final)) {
                  $mode = 'nilaiX';
                }
              }
            }
            $fmt = function($v) { return is_numeric($v) ? number_format((float)$v, 2, ',', '.') : ($v ?? '-'); };
          @endphp

          @if (empty($laporan))
            <div class="alert alert-info mb-0">Tidak ada data untuk filter yang dipilih.</div>
          @elseif ($mode === 'index')
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-sticky">
                <thead>
                  <tr class="text-center align-middle">
                    <th class="nowrap">NIP</th>
                    <th>Nama</th>
                    <th class="nowrap">JK</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Mapel</th>
                    <th>Kategori</th>
                    <th>Progress Test</th>
                    <th>Middle Test</th>
                    <th>Final Test</th>
                    <th>Formatif</th>
                    <th>Kehadiran</th>
                    <th class="nowrap">10% (PT)</th>
                    <th class="nowrap">30% (MT)</th>
                    <th class="nowrap">40% (FT)</th>
                    <th class="nowrap">10% (Formatif)</th>
                    <th class="nowrap">10% (Kehadiran)</th>
                    <th class="nowrap">Nilai Akademik</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($laporan as $r)
                    @php $r = (array)$r; @endphp
                    <tr>
                      <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                      <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                      <td class="text-center">{{ $r['jk'] ?? '-' }}</td>
                      <td>{{ $r['kelas'] ?? '-' }}</td>
                      <td>{{ $r['jurusan'] ?? '-' }}</td>
                      <td>{{ $r['mapel'] ?? '-' }}</td>
                      <td><span class="badge badge-soft">{{ $r['kategori_penilaian'] ?? '-' }}</span></td>
                      <td class="text-right">{{ $fmt($r['Progress Test'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['Middle Test'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['Final Test'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['nilai_formatif'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['nilai_kehadiran'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['10%'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['30%'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['40%'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['10%_formatif'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['10%_kehadiran'] ?? 0) }}</td>
                      <td class="text-right fw-bold">{{ $fmt($r['nilai_akademik'] ?? 0) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @elseif ($mode === 'nilaiX')
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-sticky">
                <thead>
                  <tr class="text-center align-middle">
                    <th class="nowrap">NIP</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Mapel</th>
                    <th class="nowrap">Total Akademik</th>
                    <th class="nowrap">Rata-rata Akademik</th>
                    <th class="nowrap">Nilai Mental</th>
                    <th class="nowrap">X7 (70%)</th>
                    <th class="nowrap">X3 (30%)</th>
                    <th class="nowrap">TOTAL</th>
                    <th class="nowrap">Nilai Akhir</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($laporan as $r)
                    @php $r = (array)$r; @endphp
                    <tr>
                      <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                      <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                      <td class="text-center">{{ $r['jk'] ?? '-' }}</td>
                      <td>{{ $r['kelas'] ?? '-' }}</td>
                      <td>{{ $r['jurusan'] ?? '-' }}</td>
                      <td>{{ $r['mapel'] ?? '-' }}</td>
                      <td class="text-right">{{ $fmt($r['total_akademik'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['nilai_akademik'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['nilai_mental'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['x7'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['x3'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['total'] ?? 0) }}</td>
                      <td class="text-right fw-bold">{{ $fmt($r['nilai_final'] ?? 0) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @elseif ($mode === 'rekap')
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-sticky">
                <thead>
                  <tr class="text-center align-middle">
                    <th class="nowrap">NIP</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Mapel</th>
                    <th>MENDENGAR</th>
                    <th>MEMBACA</th>
                    <th>MENULIS</th>
                    <th>BERBICARA</th>
                    <th>TATA BAHASA</th>
                    <th class="nowrap">JUMLAH AKADEMIK</th>
                    <th class="nowrap">RATA-RATA AKADEMIK</th>
                    <th class="nowrap">X7 (70%)</th>
                    <th class="nowrap">RATA-RATA MENTAL</th>
                    <th class="nowrap">X3 (30%)</th>
                    <th class="nowrap">TOTAL</th>
                    <th class="nowrap">Nilai Akhir</th>
                    <th class="nowrap">Ranking</th>
                    <th class="nowrap">Klasifikasi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($laporan as $r)
                    @php $r = (array)$r; @endphp
                    <tr>
                      <td class="nowrap">{{ $r['nip'] ?? '-' }}</td>
                      <td>{{ $r['nama_siswa'] ?? '-' }}</td>
                      <td>{{ $r['kelas'] ?? '-' }}</td>
                      <td>{{ $r['jurusan'] ?? '-' }}</td>
                      <td>{{ $r['mapel'] ?? '-' }}</td>
                      <td class="text-right">{{ $fmt($r['MENDENGAR'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['MEMBACA'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['MENULIS'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['BERBICARA'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['TATA BAHASA'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['JUMLAH AKADEMIK'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['NILAI RATA-RATA AKADEMIK'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['X7'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['NILAI RATA-RATA MENTAL'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['X3'] ?? 0) }}</td>
                      <td class="text-right">{{ $fmt($r['TOTAL'] ?? 0) }}</td>
                      <td class="text-right fw-bold">{{ $fmt($r['Nilai Akhir'] ?? 0) }}</td>
                      <td class="text-center">{{ $r['Ranking'] ?? '-' }}</td>
                      <td class="text-center">{{ $r['Klasifikasi'] ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-warning mb-0">Struktur data laporan tidak dikenali. Pastikan controller mengirimkan data sesuai format.</div>
          @endif
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

