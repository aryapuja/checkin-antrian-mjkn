@forelse($antreans as $item)
    <tr class="hover:bg-emerald-50/40 transition-colors border-b border-slate-100 group" id="row-{{ $item->ID }}">
        <!-- Kode Booking & No Antrean -->
        <td class="px-3.5 py-2.5">
            <div class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                    #{{ $item->NOMOR_ANTRIAN ?? '-' }}
                </span>
                <span class="font-mono text-xs font-bold text-slate-800 tracking-tight bg-slate-100 px-2 py-0.5 rounded border border-slate-200 shrink-0">
                    {{ $item->ID }}
                </span>
                <button type="button" onclick="copyToClipboard('{{ $item->ID }}')" title="Salin Kode Booking" class="text-slate-400 hover:text-emerald-700 transition-colors p-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1 whitespace-nowrap">
                <span>{{ $item->TANGGALKUNJUNGAN ? \Carbon\Carbon::parse($item->TANGGALKUNJUNGAN)->format('d M Y') : '-' }}</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-slate-600">{{ $item->JAM_PRAKTEK ?? '-' }}</span>
            </div>
        </td>

        <!-- Pasien & NORM -->
        <td class="px-3.5 py-2.5">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 border border-emerald-200">
                    {{ strtoupper(substr($item->NAMA ?? 'P', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="font-semibold text-slate-900 text-xs flex items-center gap-1">
                        <span class="truncate max-w-[150px] sm:max-w-[190px]" title="{{ $item->NAMA }}">{{ $item->NAMA ?? 'Nama Pasien' }}</span>
                        <span class="text-[10px] text-slate-500 font-normal shrink-0">({{ $item->JK ?? '-' }})</span>
                    </div>
                    <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                        <span>No. RM: <strong class="text-slate-700 font-mono">{{ $item->NORM ? sprintf('%06d', $item->NORM) : '-' }}</strong></span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-[10px] px-1 rounded bg-slate-100 text-slate-600">{{ $item->JENIS == 2 ? 'Baru' : 'Lama' }}</span>
                    </div>
                </div>
            </div>
        </td>

        <!-- Tanggal Lahir & Usia -->
        <td class="px-3 py-2.5 whitespace-nowrap text-xs text-slate-700">
            <div class="font-medium">{{ $item->TANGGAL_LAHIR ? \Carbon\Carbon::parse($item->TANGGAL_LAHIR)->format('d/m/Y') : '-' }}</div>
            <div class="text-[11px] text-slate-500">{{ $item->usia }}</div>
        </td>

        <!-- Poli & Dokter -->
        <td class="px-3 py-2.5">
            <div class="flex items-center gap-1">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-800 border border-emerald-200 uppercase shrink-0">
                    {{ $item->POLI_BPJS ?? 'POLI' }}
                </span>
            </div>
            <div class="text-[11px] text-slate-600 mt-0.5 truncate max-w-[170px]" title="{{ $item->DOKTER }}">
                {{ $item->DOKTER ?? '-' }}
            </div>
        </td>

        <!-- Sumber Aplikasi -->
        <td class="px-2.5 py-2.5 whitespace-nowrap text-center">
            @if($item->is_mjkn)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    MJKN
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Offline
                </span>
            @endif
        </td>

        <!-- Status -->
        <td class="px-2.5 py-2.5 whitespace-nowrap text-center">
            @php $badge = $item->status_badge; @endphp
            <div class="inline-flex flex-col items-center">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                    {{ $badge['text'] }}
                </span>
                @if($item->STATUS == 2 && $item->WAKTU_CHECK_IN)
                    <span class="text-[10px] text-slate-400 mt-0.5 font-mono">
                        {{ \Carbon\Carbon::parse($item->WAKTU_CHECK_IN)->format('H:i') }} WIB
                    </span>
                @endif
            </div>
        </td>

        <!-- Aksi -->
        <td class="px-3 py-2.5 whitespace-nowrap text-center">
            <div class="inline-flex items-center justify-center gap-1.5">
                @if($tab === 'today_all')
                    <!-- Di Tab 2: Tombol Edit Poli (Status Disabled) -->
                    <button type="button" disabled
                            title="Perubahan poli memerlukan sinkronisasi dokter poli (Fitur dalam pengembangan)"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Poli</span>
                    </button>
                @endif

                @if($tab !== 'today_all')
                    <!-- Tombol Check In -->
                    @if($item->STATUS == 1 || $item->STATUS == 99)
                        <button type="button"
                                onclick="confirmCheckIn('{{ $item->ID }}', '{{ addslashes($item->NAMA) }}', {{ $item->is_mjkn ? 'true' : 'false' }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs hover:shadow transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Check In</span>
                        </button>
                    @else
                        <button type="button" disabled
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Check In</span>
                        </button>
                    @endif
                @endif

                <!-- Tombol Batal -->
                @if($item->STATUS == 1 || $item->STATUS == 99)
                    <button type="button"
                            onclick="confirmBatal('{{ $item->ID }}', '{{ addslashes($item->NAMA) }}', {{ $item->is_mjkn ? 'true' : 'false' }})"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 border border-rose-200 shadow-2xs transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </button>
                @else
                    <button type="button" disabled
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
            <div class="max-w-xs mx-auto flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="font-bold text-slate-700 text-xs">Tidak Ada Data Antrean</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Tidak ditemukan antrean yang cocok dengan filter atau kata kunci saat ini.</p>
            </div>
        </td>
    </tr>
@endforelse
