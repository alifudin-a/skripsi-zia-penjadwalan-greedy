@extends('layouts.public')

@section('title', 'Jadwal Hari Ini')

@section('content')
    <header class="mb-6">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">
                @if ($isDefaultHari)
                    Jadwal Hari Ini
                @else
                    Jadwal {{ $hari }}
                @endif
            </h1>
            <span class="text-sm text-slate-500">{{ $tanggalHariIni }}</span>
        </div>
        <p class="mt-1 text-sm text-slate-500">
            @if ($isDefaultHari)
                Semua kelas yang memiliki jadwal pada hari {{ $today }} ditampilkan di sini.
            @else
                Anda melihat jadwal untuk hari {{ $hari }}. Kembali ke
                <a href="{{ route('public.jadwal') }}" class="text-slate-700 underline hover:text-slate-900">hari ini</a>.
            @endif
        </p>
    </header>

    {{-- Filter --}}
    <form method="GET" action="{{ route('public.jadwal') }}"
          class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="hari" class="mb-1 block text-sm font-medium text-slate-700">Hari</label>
                <select id="hari" name="hari"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">Hari Ini ({{ $today }})</option>
                    @foreach ($hariOptions as $h)
                        <option value="{{ $h }}" @selected($hari === $h && ! $isDefaultHari)>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="kelas" class="mb-1 block text-sm font-medium text-slate-700">Kelas</label>
                <select id="kelas" name="kelas"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" @selected($kelas && $kelas->id === $k->id)>
                            {{ $k->tingkat }} {{ $k->jurusan }} — {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="guru" class="mb-1 block text-sm font-medium text-slate-700">Guru</label>
                <select id="guru" name="guru"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">Semua Guru</option>
                    @foreach ($guruList as $g)
                        <option value="{{ $g->id }}" @selected($guru && $guru->id === $g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="mapel" class="mb-1 block text-sm font-medium text-slate-700">Mata Pelajaran</label>
                <select id="mapel" name="mapel"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">Semua Mapel</option>
                    @foreach ($mapelList as $m)
                        <option value="{{ $m->id }}" @selected($mapel && $mapel->id === $m->id)>
                            {{ $m->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                Tampilkan
            </button>
            @if ($hasActiveFilters)
                <a href="{{ route('public.jadwal') }}"
                   class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Reset ke hari ini
                </a>
            @endif
        </div>
    </form>

    {{-- Ringkasan filter aktif --}}
    @if ($kelas || $guru || $mapel || ! $isDefaultHari)
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <span>Filter aktif:</span>
            @if (! $isDefaultHari)
                <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-medium text-sky-700">
                    Hari: {{ $hari }}
                </span>
            @endif
            @if ($kelas)
                <span class="inline-flex items-center rounded-full bg-slate-900 px-2.5 py-0.5 text-xs font-medium text-white">
                    Kelas: {{ $kelas->tingkat }} {{ $kelas->jurusan }}
                </span>
            @endif
            @if ($guru)
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                    Guru: {{ $guru->nama }}
                </span>
            @endif
            @if ($mapel)
                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                    Mapel: {{ $mapel->nama }}
                </span>
            @endif
        </div>
    @endif

    {{-- Mode A: tanpa filter kelas — tampil semua kelas yang punya jadwal hari ini --}}
    @if (! $kelas && $jadwalsByKelas->isNotEmpty())
        <div class="space-y-8">
            @foreach ($jadwalsByKelas as $kelasId => $jadwals)
                @php $first = $jadwals->first(); @endphp
                <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <header class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <h2 class="text-base font-semibold text-slate-800">
                            {{ $first->kelasRelasi->tingkat ?? '' }}
                            {{ $first->kelasRelasi->jurusan ?? '' }}
                            @if ($first->kelasRelasi && $first->kelasRelasi->nama_kelas)
                                — {{ $first->kelasRelasi->nama_kelas }}
                            @endif
                        </h2>
                        <span class="text-xs text-slate-500">{{ $jadwals->count() }} sesi</span>
                    </header>
                    @include('public._jadwal-table', ['jadwals' => $jadwals])
                </section>
            @endforeach
        </div>

    {{-- Mode B: ada filter kelas — tampil per hari --}}
    @elseif ($kelas && $jadwalsByHari->isNotEmpty())
        <div class="space-y-8">
            @foreach ($jadwalsByHari as $namaHari => $jadwals)
                <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <header class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <h2 class="text-base font-semibold text-slate-800">{{ $namaHari }}</h2>
                        <span class="text-xs text-slate-500">{{ $jadwals->count() }} sesi</span>
                    </header>
                    @include('public._jadwal-table', ['jadwals' => $jadwals])
                </section>
            @endforeach
        </div>

    {{-- Empty state --}}
    @else
        <div class="rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center">
            @if ($isDefaultHari)
                <p class="text-sm text-slate-500">
                    Tidak ada jadwal untuk hari {{ $today }} ({{ $tanggalHariIni }}).
                </p>
                <p class="mt-2 text-xs text-slate-400">
                    Coba pilih hari lain, atau cek kembali besok.
                </p>
            @else
                <p class="text-sm text-slate-500">
                    Tidak ada jadwal untuk hari {{ $hari }} yang cocok dengan filter.
                </p>
            @endif
        </div>
    @endif
@endsection
