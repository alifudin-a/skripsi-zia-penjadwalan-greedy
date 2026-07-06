{{--
    Partial: tabel jadwal (desktop + mobile cards).
    Expects: $jadwals (Collection of Jadwal with relations loaded)
--}}
@php
    $statusBadge = [
        'draft'   => ['label' => 'Draft',   'class' => 'bg-slate-100 text-slate-600 ring-slate-200'],
        'aktif'   => ['label' => 'Aktif',   'class' => 'bg-emerald-100 text-emerald-700 ring-emerald-200'],
        'selesai' => ['label' => 'Selesai', 'class' => 'bg-sky-100 text-sky-700 ring-sky-200'],
        'batal'   => ['label' => 'Batal',   'class' => 'bg-rose-100 text-rose-700 ring-rose-200'],
    ];
@endphp

{{-- Desktop table --}}
<table class="hidden w-full text-left text-sm sm:table">
    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
        <tr>
            <th class="px-4 py-2 font-medium">Jam</th>
            <th class="px-4 py-2 font-medium">Mata Pelajaran</th>
            <th class="px-4 py-2 font-medium">Guru</th>
            <th class="px-4 py-2 font-medium">Laboratorium</th>
            <th class="px-4 py-2 font-medium">Status</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
        @foreach ($jadwals as $j)
            @php
                $key   = strtolower((string) $j->status);
                $badge = $statusBadge[$key] ?? ['label' => ucfirst($j->status ?? '—'), 'class' => 'bg-slate-100 text-slate-600 ring-slate-200'];
            @endphp
            <tr class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-4 py-3 font-mono text-slate-700">
                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                    –
                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                </td>
                <td class="px-4 py-3 font-medium text-slate-800">
                    {{ $j->mataPelajaran->nama ?? '—' }}
                </td>
                <td class="px-4 py-3 text-slate-700">{{ $j->guru->nama ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-700">{{ $j->laboratorium->nama ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Mobile cards --}}
<ul class="divide-y divide-slate-100 sm:hidden">
    @foreach ($jadwals as $j)
        @php
            $key   = strtolower((string) $j->status);
            $badge = $statusBadge[$key] ?? ['label' => ucfirst($j->status ?? '—'), 'class' => 'bg-slate-100 text-slate-600 ring-slate-200'];
        @endphp
        <li class="space-y-1 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="font-mono text-xs text-slate-500">
                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                    –
                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                </div>
                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
            <div class="font-medium text-slate-800">{{ $j->mataPelajaran->nama ?? '—' }}</div>
            <div class="text-xs text-slate-600">
                {{ $j->guru->nama ?? '—' }}
                @if ($j->laboratorium) · {{ $j->laboratorium->nama }} @endif
            </div>
        </li>
    @endforeach
</ul>
