<div x-data="{
    categoryFilter: 'All',
    statusFilter: 'All',
    selectedReport: null,
    showPreviewModal: false,
    
    getFilteredReports() {
        return this.reports.filter(r => {
            const matchesCat = this.categoryFilter === 'All' || r.category === this.categoryFilter;
            const matchesStatus = this.statusFilter === 'All' || r.status === this.statusFilter;
            return matchesCat && matchesStatus;
        });
    },
    openReport(r) {
        this.selectedReport = r;
        this.showPreviewModal = true;
    },
    quickResolve(id, actionName) {
        this.resolveReport(id);
        if (actionName === 'hapus') {
            this.logAction('Report Quick Action', 'Hapus Konten (Report ID: ' + id + ')', 'Content removed based on user report');
        } else if (actionName === 'dismiss') {
            this.logAction('Report Quick Action', 'Dismiss Report (Report ID: ' + id + ')', 'Report investigated and dismissed');
        }
    }
}">

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
        
        <!-- Category Toggles -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
            <span class="text-xs text-slate-500 font-semibold shrink-0">Kategori:</span>
            <button @click="categoryFilter = 'All'" :class="categoryFilter === 'All' ? 'bg-brandPrimary text-white shadow-lg' : 'bg-darkSurface text-slate-400 border border-slate-850 hover:bg-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">Semua</button>
            <button @click="categoryFilter = 'Spam'" :class="categoryFilter === 'Spam' ? 'bg-amber-500 text-white shadow-lg' : 'bg-darkSurface text-slate-400 border border-slate-850 hover:bg-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">Spam</button>
            <button @click="categoryFilter = 'Plagiat'" :class="categoryFilter === 'Plagiat' ? 'bg-cyan-500 text-white shadow-lg' : 'bg-darkSurface text-slate-400 border border-slate-850 hover:bg-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">Plagiat</button>
            <button @click="categoryFilter = 'NSFW'" :class="categoryFilter === 'NSFW' ? 'bg-red-500 text-white shadow-lg' : 'bg-darkSurface text-slate-400 border border-slate-850 hover:bg-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">NSFW</button>
        </div>

        <!-- Status dropdown filter -->
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs text-slate-500 font-semibold shrink-0">Status:</span>
            <select x-model="statusFilter" class="bg-darkSurface border border-slate-800 rounded-lg text-slate-300 text-xs px-3 py-1.5 focus:outline-none focus:border-brandPrimary">
                <option value="All">Semua Status</option>
                <option value="Pending">Pending (Aktif)</option>
                <option value="Resolved">Resolved (Selesai)</option>
            </select>
        </div>

    </div>

    <!-- Reports Table -->
    <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-darkCard/50 border-b border-slate-800 text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                        <th class="p-4 pl-6">ID</th>
                        <th class="p-4">Pelapor</th>
                        <th class="p-4">Tipe Aset</th>
                        <th class="p-4">Konteks Laporan</th>
                        <th class="p-4 text-center">Kategori</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Tgl Report</th>
                        <th class="p-4 pr-6 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs text-slate-300">
                    <template x-for="r in getFilteredReports()" :key="r.id">
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="p-4 pl-6 font-bold text-slate-500" x-text="'#RP-' + r.id"></td>
                            <td class="p-4 font-semibold text-slate-300" x-text="'@' + r.reporter"></td>
                            <td class="p-4 text-slate-400">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[8px] font-bold uppercase tracking-wider" x-text="r.target_type"></span>
                            </td>
                            <td class="p-4 font-bold text-white max-w-xs truncate" x-text="r.target_title"></td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] uppercase tracking-wide inline-block"
                                      :class="{
                                          'bg-amber-500/10 text-amber-400 border border-amber-500/20': r.category === 'Spam',
                                          'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20': r.category === 'Plagiat',
                                          'bg-red-500/10 text-red-400 border border-red-500/20': r.category === 'NSFW'
                                      }" x-text="r.category"></span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] uppercase tracking-wide inline-block"
                                      :class="r.status === 'Resolved' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'"
                                      x-text="r.status"></span>
                            </td>
                            <td class="p-4 text-right text-slate-500" x-text="r.created_at"></td>
                            <td class="p-4 pr-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button @click="openReport(r)" class="px-2.5 py-1 bg-slate-850 hover:bg-slate-800 text-slate-300 border border-slate-750 rounded-lg text-[10px] font-bold transition-all">
                                        Periksa
                                    </button>
                                    <template x-if="r.status === 'Pending'">
                                        <div class="flex items-center gap-1.5">
                                            <button @click="quickResolve(r.id, 'dismiss')" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-750 text-slate-400 rounded-lg text-[10px] font-bold transition-all">
                                                Dismiss
                                            </button>
                                            <button @click="quickResolve(r.id, 'hapus')" class="px-2.5 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg text-[10px] font-bold transition-all shadow-sm">
                                                Hapus Konten
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="getFilteredReports().length === 0">
                        <td colspan="8" class="p-8 text-center text-slate-500 italic">Tidak ada laporan pelanggaran yang masuk.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: View Report details -->
    <div x-cloak x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-darkBg/80 backdrop-blur-sm" x-transition>
        <div @click.outside="showPreviewModal = false" class="bg-darkSurface border border-slate-800 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl glow-indigo">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-darkCard/25">
                <div>
                    <h3 class="font-bold text-white text-base" x-text="'Laporan ID: #RP-' + selectedReport?.id"></h3>
                    <p class="text-[9px] text-slate-500" x-text="'Dilaporkan oleh @' + selectedReport?.reporter"></p>
                </div>
                <button @click="showPreviewModal = false" class="text-slate-400 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <template x-if="selectedReport">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-darkCard border border-slate-850 rounded-xl">
                            <span class="text-[9px] text-slate-500 block">Kategori Pelanggaran</span>
                            <strong class="text-white capitalize" x-text="selectedReport.category"></strong>
                        </div>
                        <div class="p-3 bg-darkCard border border-slate-850 rounded-xl">
                            <span class="text-[9px] text-slate-500 block">Tipe Konten</span>
                            <strong class="text-brandAccent uppercase" x-text="selectedReport.target_type"></strong>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-900 border border-slate-850 rounded-xl">
                        <span class="text-[9px] text-slate-500 block mb-1">Aset/Konten Yang Dilaporkan</span>
                        <strong class="text-xs text-white" x-text="selectedReport.target_title"></strong>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Isi Konten / Bukti Laporan</span>
                        <p class="text-xs text-slate-300 bg-slate-950 p-4 border border-slate-850 rounded-xl leading-relaxed italic"
                           x-text="selectedReport.content_preview"></p>
                    </div>

                    <div class="flex gap-2 justify-end pt-3 border-t border-slate-800" x-show="selectedReport.status === 'Pending'">
                        <button @click="quickResolve(selectedReport.id, 'dismiss'); showPreviewModal = false;" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-400 rounded-lg text-xs font-semibold transition-all">Dismiss Laporan</button>
                        <button @click="quickResolve(selectedReport.id, 'hapus'); showPreviewModal = false;" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-bold transition-all shadow-md">Eksekusi Hapus Konten</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
