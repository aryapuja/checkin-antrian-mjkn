<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RSUD Kota Malang - Check-In Antrean Mobile JKN</title>

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        rsud: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        /* Custom subtle scrollbar if needed */
        #table-container::-webkit-scrollbar { height: 6px; }
        #table-container::-webkit-scrollbar-track { background: #f1f5f9; }
        #table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        #table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Header (RSUD KOTA MALANG THEME) -->
    <header class="bg-white border-b-2 border-emerald-600 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-3 sm:px-4 lg:px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Brand & Logo -->
                <div class="flex items-center space-x-3">
                    <!-- RSUD Kota Malang Green Cross & Leaf Logo Badge -->
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-lime-500 flex items-center justify-center text-white shadow-md shadow-emerald-200 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-extrabold text-emerald-800 text-lg tracking-tight uppercase">RSUD KOTA MALANG</h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                BPJS MJKN
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Portal Check-In & Antrean Mobile JKN</p>
                    </div>
                </div>

                <!-- Right Controls: Realtime Clock & Status -->
                <div class="flex items-center space-x-3">
                    <div class="flex flex-col text-right">
                        <span class="text-xs font-bold text-emerald-800 font-mono" id="live-clock">--:--:-- WIB</span>
                        <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container (Full Width / No Wasted Margin) -->
    <main class="flex-1 w-full px-2 sm:px-4 lg:px-6 py-4 space-y-4">

        <!-- 4 Metric Cards (KPI Summary - RSUD Kota Malang Green Palette) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Hari Ini -->
            <div class="bg-white rounded-xl p-3.5 border border-emerald-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Pasien Hari Ini</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900" id="metric-total-today">{{ $metrics['total_today'] }}</span>
                    <span class="text-xs font-semibold text-emerald-700">({{ $metrics['mjkn_today'] }} MJKN)</span>
                </div>
            </div>

            <!-- Belum Check In -->
            <div class="bg-white rounded-xl p-3.5 border border-amber-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Check In</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-amber-600" id="metric-pending-today">{{ $metrics['pending_today'] }}</span>
                    <span class="text-[11px] text-amber-700/80 font-semibold">Siap dipanggil</span>
                </div>
            </div>

            <!-- Sudah Check In -->
            <div class="bg-white rounded-xl p-3.5 border border-emerald-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Sudah Check In</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-emerald-600" id="metric-checked-in-today">{{ $metrics['checked_in_today'] }}</span>
                    <span class="text-[11px] text-emerald-700/80 font-semibold">Terkirim BPJS</span>
                </div>
            </div>

            <!-- Dibatalkan -->
            <div class="bg-white rounded-xl p-3.5 border border-rose-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Dibatalkan</span>
                    <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-rose-600" id="metric-cancelled-today">{{ $metrics['cancelled_today'] }}</span>
                    <span class="text-[11px] text-rose-700/80 font-semibold">Batal berkunjung</span>
                </div>
            </div>
        </div>

        <!-- Main Card with Tabs & Full-Width Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            
            <!-- Card Header: Tabs & Auto-refresh Controller -->
            <div class="p-3.5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-slate-50/50">
                
                <!-- 3 Tab Navigation (Green Themed) -->
                <div class="flex p-1 bg-slate-200/70 rounded-lg max-w-fit">
                    <button type="button" onclick="switchTab('today_mjkn')" id="tab-today_mjkn"
                            class="tab-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-emerald-600 text-white shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Antrean Hari Ini (MJKN)</span>
                    </button>
                    <button type="button" onclick="switchTab('today_all')" id="tab-today_all"
                            class="tab-btn px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 hover:text-emerald-800 transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                        <span>Semua Pasien Hari Ini</span>
                    </button>
                    <button type="button" onclick="switchTab('all')" id="tab-all"
                            class="tab-btn px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 hover:text-emerald-800 transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Semua Data / Arsip</span>
                    </button>
                </div>

                <!-- Auto-refresh & Manual Refresh Control -->
                <div class="flex items-center gap-2.5">
                    <!-- Countdown Timer Indicator -->
                    <div id="refresh-timer-badge" 
                         class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="relative flex h-2 w-2">
                            <span id="timer-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span id="timer-dot" class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                        </span>
                        <span>Auto-refresh: <strong id="countdown-text" class="font-mono text-emerald-950 font-bold">05:00</strong></span>
                    </div>

                    <!-- Manual Refresh Button -->
                    <button type="button" onclick="manualRefresh()" id="btn-manual-refresh"
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs hover:shadow-xs transition-all flex items-center gap-1.5 active:scale-95">
                        <svg id="refresh-icon" class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Refresh Sekarang</span>
                    </button>
                </div>
            </div>

            <!-- Filters Bar (Search & Date Picker) -->
            <div class="px-3.5 py-2.5 bg-white border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2.5">
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="search-input" placeholder="Cari Nama, No. RM, Kode Booking..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-hidden focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>

                <!-- Date Picker (Hanya Aktif di Tab 3 'Semua Data') -->
                <div id="date-filter-container" class="hidden items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs text-slate-500 font-medium">Filter Tanggal:</span>
                    <input type="date" id="date-input" value="{{ date('Y-m-d') }}"
                           class="px-2.5 py-1 text-xs rounded-lg border border-slate-200 bg-white focus:outline-hidden focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all font-medium">
                    <button type="button" onclick="loadTableData(1)" class="px-3 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                        Terapkan
                    </button>
                </div>
            </div>

            <!-- Table Wrapper (W-FULL FIT / NO HORIZONTAL SCROLL ON STANDARD SCREENS) -->
            <div class="overflow-x-auto w-full" id="table-container">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <tr class="bg-emerald-700 text-white text-[11px] font-bold uppercase tracking-wider">
                            <th class="px-3.5 py-2.5">Kode Booking</th>
                            <th class="px-3.5 py-2.5">Pasien & No. RM</th>
                            <th class="px-3 py-2.5">Tgl Lahir / Usia</th>
                            <th class="px-3 py-2.5">Poli & Dokter</th>
                            <th class="px-2.5 py-2.5 text-center">Sumber</th>
                            <th class="px-2.5 py-2.5 text-center">Status</th>
                            <th class="px-3 py-2.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="divide-y divide-slate-100 bg-white">
                        <!-- Table rows injected via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Loading Skeleton Overlay (Hidden by default) -->
            <div id="table-loading" class="hidden py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-7 w-7 border-b-2 border-emerald-600"></div>
                <p class="text-xs text-slate-500 mt-2 font-medium">Memuat data antrean...</p>
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                <!-- Pagination injected via AJAX -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-3 mt-auto">
        <div class="w-full px-4 text-center text-xs text-slate-500 font-medium">
            &copy; {{ date('Y') }} RSUD KOTA MALANG &bull; Terintegrasi Web Service BPJS Antrean Mobile JKN
        </div>
    </footer>

    <!-- JavaScript Application Logic -->
    <script>
        // State Management
        let currentTab = 'today_mjkn';
        let currentPage = 1;
        let countdownSeconds = 300; // 5 Menit = 300 detik
        let isPaused = false;
        let timerInterval = null;
        let searchDebounceTimeout = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize application on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            initClock();
            loadTableData(1);
            startCountdownTimer();
            setupInteractiveEvents();
        });

        // 1. Clock realtime
        function initClock() {
            const clockEl = document.getElementById('live-clock');
            setInterval(() => {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('id-ID') + ' WIB';
            }, 1000);
        }

        // 2. Countdown Timer with Auto-pause on Hover/Focus
        function startCountdownTimer() {
            if (timerInterval) clearInterval(timerInterval);
            countdownSeconds = 300;

            timerInterval = setInterval(() => {
                if (!isPaused) {
                    countdownSeconds--;
                    updateCountdownDisplay();

                    if (countdownSeconds <= 0) {
                        countdownSeconds = 300;
                        loadTableData(currentPage, true); // Silent auto-refresh
                    }
                }
            }, 1000);
        }

        function updateCountdownDisplay() {
            const m = Math.floor(countdownSeconds / 60);
            const s = countdownSeconds % 60;
            const text = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            document.getElementById('countdown-text').textContent = text;
        }

        // Setup Hover and Input Event Listeners for Smart Pause
        function setupInteractiveEvents() {
            const container = document.getElementById('table-container');
            const searchInput = document.getElementById('search-input');
            const timerBadge = document.getElementById('refresh-timer-badge');
            const timerPing = document.getElementById('timer-ping');
            const timerDot = document.getElementById('timer-dot');

            const pauseTimer = (reason) => {
                isPaused = true;
                timerBadge.classList.replace('bg-emerald-50', 'bg-amber-50');
                timerBadge.classList.replace('text-emerald-800', 'text-amber-800');
                timerPing.classList.add('hidden');
                timerDot.classList.replace('bg-emerald-600', 'bg-amber-500');
                document.getElementById('countdown-text').textContent = `Dijeda (${reason})`;
            };

            const resumeTimer = () => {
                isPaused = false;
                timerBadge.classList.replace('bg-amber-50', 'bg-emerald-50');
                timerBadge.classList.replace('text-amber-800', 'text-emerald-800');
                timerPing.classList.remove('hidden');
                timerDot.classList.replace('bg-amber-500', 'bg-emerald-600');
                updateCountdownDisplay();
            };

            container.addEventListener('mouseenter', () => pauseTimer('Kursor Aktif'));
            container.addEventListener('mouseleave', () => resumeTimer());

            searchInput.addEventListener('focus', () => pauseTimer('Mengetik'));
            searchInput.addEventListener('blur', () => resumeTimer());

            // Debounced Search
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchDebounceTimeout);
                searchDebounceTimeout = setTimeout(() => {
                    loadTableData(1);
                }, 400);
            });
        }

        // 3. Switch Tab Logic
        function switchTab(tab) {
            currentTab = tab;
            currentPage = 1;

            // Update tab UI buttons (RSUD Green theme)
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = 'tab-btn px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 hover:text-emerald-800 transition-all flex items-center gap-1.5';
            });
            const activeBtn = document.getElementById(`tab-${tab}`);
            activeBtn.className = 'tab-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-emerald-600 text-white shadow-xs flex items-center gap-1.5';

            // Show date filter only on Tab 3
            const dateContainer = document.getElementById('date-filter-container');
            if (tab === 'all') {
                dateContainer.classList.remove('hidden');
                dateContainer.classList.add('flex');
            } else {
                dateContainer.classList.add('hidden');
                dateContainer.classList.remove('flex');
            }

            loadTableData(1);
        }

        // 4. AJAX Load Table Data (Partial Refresh)
        function loadTableData(page = 1, isSilent = false) {
            currentPage = page;
            const search = document.getElementById('search-input').value;
            const tanggal = document.getElementById('date-input').value;

            const tbody = document.getElementById('table-body');
            const loading = document.getElementById('table-loading');
            const refreshIcon = document.getElementById('refresh-icon');

            if (!isSilent) {
                loading.classList.remove('hidden');
                tbody.classList.add('opacity-40');
                refreshIcon.classList.add('animate-spin');
            }

            const url = new URL('/api/antrean-data', window.location.origin);
            url.searchParams.append('tab', currentTab);
            url.searchParams.append('page', page);
            url.searchParams.append('search', search);
            if (currentTab === 'all' && tanggal) {
                url.searchParams.append('tanggal', tanggal);
            }

            fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    tbody.innerHTML = data.html;
                    document.getElementById('pagination-container').innerHTML = data.pagination;

                    // Update KPI Metric Cards
                    if (data.metrics) {
                        document.getElementById('metric-total-today').textContent = data.metrics.total_today;
                        document.getElementById('metric-pending-today').textContent = data.metrics.pending_today;
                        document.getElementById('metric-checked-in-today').textContent = data.metrics.checked_in_today;
                        document.getElementById('metric-cancelled-today').textContent = data.metrics.cancelled_today;
                    }
                }
            })
            .catch(err => {
                console.error('Error fetching table data:', err);
            })
            .finally(() => {
                loading.classList.add('hidden');
                tbody.classList.remove('opacity-40');
                refreshIcon.classList.remove('animate-spin');
            });
        }

        // Manual refresh button
        function manualRefresh() {
            countdownSeconds = 300;
            updateCountdownDisplay();
            loadTableData(currentPage);
        }

        // 5. Action: Check In Confirmation & Execution
        function confirmCheckIn(id, nama, isMjkn) {
            Swal.fire({
                title: 'Konfirmasi Check-In',
                html: `Apakah Anda yakin ingin melakukan <strong>Check-In</strong> untuk pasien:<br><br><span class="text-emerald-700 font-bold text-base">${nama}</span><br><code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-700 font-mono">Kode: ${id}</code><br><br><small class="text-slate-500">${isMjkn ? 'Sinyal kehadiran akan dikirimkan ke BPJS Antrean RS' : 'Check-In lokal (Pasien Offline RS)'}</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Check-In Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    executeCheckIn(id);
                }
            });
        }

        function executeCheckIn(id) {
            Swal.fire({
                title: 'Memproses Check-In...',
                text: 'Menghubungi server BPJS dan memperbarui data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/api/antrean/${id}/checkin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Check-In Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadTableData(currentPage, true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check-In Gagal',
                        text: data.message || 'Terjadi kesalahan saat memproses Check-In.'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: err.message
                });
            });
        }

        // 6. Action: Batal Antrean Confirmation & Execution
        function confirmBatal(id, nama, isMjkn) {
            Swal.fire({
                title: 'Batalkan Antrean?',
                html: `Anda akan membatalkan antrean untuk:<br><span class="text-rose-600 font-bold text-base">${nama}</span> (${id})<br><br><span class="text-xs text-slate-500">Wajib memasukkan alasan pembatalan untuk pelaporan BPJS:</span>`,
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan pembatalan (misal: Dokter berhalangan / Pasien minta batal)...',
                inputAttributes: {
                    'aria-label': 'Alasan pembatalan'
                },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Batalkan Antrean',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value || value.trim().length < 3) {
                        return 'Alasan pembatalan minimal 3 karakter!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeBatal(id, result.value);
                }
            });
        }

        function executeBatal(id, keterangan) {
            Swal.fire({
                title: 'Membatalkan Antrean...',
                text: 'Mengirim informasi pembatalan ke BPJS...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/api/antrean/${id}/batal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ keterangan })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Antrean Dibatalkan',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadTableData(currentPage, true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membatalkan',
                        text: data.message || 'Terjadi kesalahan saat membatalkan antrean.'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: err.message
                });
            });
        }

        // Copy to clipboard helper
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: false
            });
            Toast.fire({
                icon: 'success',
                title: `Kode booking ${text} disalin!`
            });
        }
    </script>
</body>
</html>
