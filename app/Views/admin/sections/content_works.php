<div x-data="{
    searchQuery: '',
    typeFilter: 'All',
    paywallFilter: 'All',
    selectedWork: null,
    showChapterDrawer: false,
    mockChapters: [
        { id: 1, num: 1, title: 'Bab 1: Awal Mula Legenda', paywall: 'Free', status: 'Active' },
        { id: 2, num: 2, title: 'Bab 2: Pertemuan Misterius', paywall: 'Free', status: 'Active' },
        { id: 3, num: 3, title: 'Bab 3: Gerbang Rahasia', paywall: 'Premium', status: 'Active' },
        { id: 4, num: 4, title: 'Bab 4: Latihan Jiwa (StarSoul)', paywall: 'Premium', status: 'Locked' },
        { id: 5, num: 5, title: 'Bab 5: Pertempuran Naga Hitam', paywall: 'Premium', status: 'Hidden' }
    ],
    
    getFilteredWorks() {
        return this.works.filter(w => {
            const matchesSearch = w.title.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  w.creator.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesType = this.typeFilter === 'All' || w.type === this.typeFilter;
            const matchesPaywall = this.paywallFilter === 'All' || w.paywall === this.paywallFilter;
            return matchesSearch && matchesType && matchesPaywall;
        });
    },
    openChapters(work) {
        this.selectedWork = work;
        this.showChapterDrawer = true;
    },
    toggleChapterStatus(cId) {
        let ch = this.mockChapters.find(c => c.id === cId);
        if (ch) {
            ch.status = (ch.status === 'Active') ? 'Hidden' : 'Active';
            this.logAction('Toggle Chapter Visibility', this.selectedWork.title + ' - ' + ch.title, 'Updated to ' + ch.status);
        }
    },
    toggleChapterLock(cId) {
        let ch = this.mockChapters.find(c => c.id === cId);
        if (ch) {
            ch.status = (ch.status === 'Locked') ? 'Active' : 'Locked';
            this.logAction('Toggle Chapter Lock', this.selectedWork.title + ' - ' + ch.title, 'Updated to ' + ch.status);
        }
    }
}">

    <!-- Filter Bar -->
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-center mb-6">
        
        <!-- Search -->
        <div class="relative w-full lg:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg">search</span>
            <input type="text" x-model="searchQuery" placeholder="Cari judul karya atau kreator..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-darkSurface border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brandPrimary transition-all text-xs">
        </div>

        <!-- Multi Filter Toggles -->
        <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
            
            <!-- Type filter -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-semibold shrink-0">Tipe:</span>
                <select x-model="typeFilter" class="bg-darkSurface border border-slate-800 rounded-lg text-slate-300 text-xs px-3 py-1.5 focus:outline-none focus:border-brandPrimary">
                    <option value="All">Semua Tipe</option>
                    <option value="Novel">Novel</option>
                    <option value="Manga">Manga</option>
                    <option value="Illustrasi">Ilustrasi</option>
                </select>
            </div>

            <!-- Paywall Filter -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-semibold shrink-0">Akses:</span>
                <select x-model="paywallFilter" class="bg-darkSurface border border-slate-800 rounded-lg text-slate-300 text-xs px-3 py-1.5 focus:outline-none focus:border-brandPrimary">
                    <option value="All">Semua Akses</option>
                    <option value="Free">Free</option>
                    <option value="Premium">Premium</option>
                </select>
            </div>

        </div>

    </div>

    <!-- Works Table -->
    <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-darkCard/50 border-b border-slate-800 text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                        <th class="p-4 pl-6">Cover & Judul</th>
                        <th class="p-4">Kreator</th>
                        <th class="p-4 text-center">Tipe</th>
                        <th class="p-4 text-center">Paywall</th>
                        <th class="p-4 text-center">Jumlah Bab</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 pr-6 text-center">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs text-slate-300">
                    <template x-for="work in getFilteredWorks()" :key="work.id">
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="p-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <img :src="work.cover_url" alt="Cover" class="w-10 h-13 object-cover rounded-lg border border-slate-800 shrink-0">
                                    <div>
                                        <h4 class="font-bold text-white text-xs" x-text="work.title"></h4>
                                        <span class="text-[9px] text-slate-500" x-text="'ID: #KW-0' + work.id"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-slate-300" x-text="'@' + work.creator"></span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-bold text-[9px]" x-text="work.type"></span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] uppercase tracking-wide inline-block"
                                      :class="work.paywall === 'Premium' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-550/20'"
                                      x-text="work.paywall"></span>
                            </td>
                            <td class="p-4 text-center font-bold text-slate-100" x-text="work.chapters + ' Bab'"></td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] uppercase tracking-wide inline-block"
                                      :class="work.status === 'Published' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                                      x-text="work.status"></span>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button @click="openChapters(work)" class="px-2 py-1 bg-slate-800 hover:bg-slate-750 text-slate-300 border border-slate-700 rounded-lg text-[10px] font-bold transition-all">
                                        Kelola Bab
                                    </button>
                                    <button @click="toggleWorkPaywall(work.id)" class="px-2 py-1 bg-brandPrimary/10 hover:bg-brandPrimary/20 text-brandPrimary border border-brandPrimary/20 rounded-lg text-[10px] font-bold transition-all">
                                        Paywall
                                    </button>
                                    <button @click="toggleWorkStatus(work.id)" class="px-2 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 rounded-lg text-[10px] font-bold transition-all">
                                        Hide/Show
                                    </button>
                                    <button @click="deleteWork(work.id)" class="px-2 py-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-lg text-[10px] font-bold transition-all">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="getFilteredWorks().length === 0">
                        <td colspan="7" class="p-8 text-center text-slate-500 italic">Tidak ada data karya ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CHAPTER MANAGEMENT DRAWER (SLIDE-OVER / MODAL) -->
    <div x-cloak x-show="showChapterDrawer" class="fixed inset-0 z-50 flex items-center justify-end bg-darkBg/80 backdrop-blur-sm" x-transition>
        <div @click.outside="showChapterDrawer = false" class="bg-darkSurface border-l border-slate-800 w-full max-w-lg h-full overflow-hidden shadow-2xl flex flex-col glow-indigo">
            
            <!-- Header -->
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-darkCard/40">
                <div>
                    <h3 class="font-bold text-white text-base" x-text="'Kelola Bab: ' + selectedWork?.title"></h3>
                    <p class="text-[10px] text-slate-500" x-text="'Kreator: @' + selectedWork?.creator"></p>
                </div>
                <button @click="showChapterDrawer = false" class="text-slate-400 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- List of Chapters -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <template x-for="c in mockChapters" :key="c.id">
                    <div class="p-4 bg-darkCard border border-slate-850 rounded-2xl flex items-center justify-between hover:border-slate-800 transition-all">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-500 font-bold text-[8px]" x-text="'Bab ' + c.num"></span>
                                <span class="px-1.5 py-0.5 rounded font-bold text-[8px] uppercase tracking-wider" 
                                      :class="c.paywall === 'Premium' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-slate-850 text-slate-400'"
                                      x-text="c.paywall"></span>
                            </div>
                            <h4 class="font-bold text-xs text-white" x-text="c.title"></h4>
                            <div class="flex items-center gap-1.5 mt-1.5 text-[9px] font-bold">
                                <span :class="{
                                    'text-emerald-400': c.status === 'Active',
                                    'text-amber-400': c.status === 'Locked',
                                    'text-red-400': c.status === 'Hidden'
                                }" x-text="'Status: ' + c.status"></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Toggle Lock -->
                            <button @click="toggleChapterLock(c.id)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-750 flex items-center justify-center text-slate-400 hover:text-white transition-colors"
                                    :title="c.status === 'Locked' ? 'Unlock Chapter' : 'Lock Chapter (Paywall Required)'">
                                <span class="material-symbols-outlined text-sm" x-text="c.status === 'Locked' ? 'lock_open' : 'lock'"></span>
                            </button>
                            <!-- Toggle Hide/Show -->
                            <button @click="toggleChapterStatus(c.id)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-750 flex items-center justify-center text-slate-400 hover:text-white transition-colors"
                                    :title="c.status === 'Hidden' ? 'Show Chapter' : 'Hide Chapter'">
                                <span class="material-symbols-outlined text-sm" x-text="c.status === 'Hidden' ? 'visibility' : 'visibility_off'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-800 bg-darkCard/20">
                <button @click="showChapterDrawer = false" class="w-full py-2.5 bg-slate-800 hover:bg-slate-750 text-white rounded-xl text-xs font-semibold transition-all">
                    Selesai & Tutup
                </button>
            </div>
            
        </div>
    </div>

</div>
