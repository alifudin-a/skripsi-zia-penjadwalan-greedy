{{--
    Partial: tabel jadwal (desktop + mobile cards).
    Expects: $jadwals (Collection of Jadwal with relations loaded)
--}}

{{-- Desktop table --}}
<table class="hidden w-full text-left text-sm sm:table">
    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
        <tr>
            <th class="px-4 py-2 font-medium">Hari</th>
            <th class="px-4 py-2 font-medium">Jam</th>
            <th class="px-4 py-2 font-medium">Mata Pelajaran</th>
            <th class="px-4 py-2 font-medium">Guru</th>
            <th class="px-4 py-2 font-medium">Laboratorium</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
        @foreach ($jadwals as $j)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $j->hari }}</td>
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
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Mobile cards --}}
<ul class="divide-y divide-slate-100 sm:hidden">
    @foreach ($jadwals as $j)
        <li class="space-y-1 px-4 py-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                    {{ $j->hari }}
                </span>
                <div class="font-mono text-xs text-slate-500">
                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                    –
                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                </div>
            </div>
            <div class="font-medium text-slate-800">{{ $j->mataPelajaran->nama ?? '—' }}</div>
            <div class="text-xs text-slate-600">
                {{ $j->guru->nama ?? '—' }}
                @if ($j->laboratorium) · {{ $j->laboratorium->nama }} @endif
            </div>
        </li>
    @endforeach
</ul>
