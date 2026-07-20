<div x-data="{
    searchQuery: '',
    actionFilter: 'All',
    
    getFilteredLogs() {
        return this.auditLogs.filter(log => {
            const matchesSearch = log.target.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  log.detail.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesAction = this.actionFilter === 'All' || log.action === this.actionFilter;
            return matchesSearch && matchesAction;
        });
    }
}">

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
        
        <!-- Search -->
        <div class="relative w-full md:w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg">search</span>
            <input type="text" x-model="searchQuery" placeholder="Cari log target atau rincian aksi..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-darkSurface border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brandPrimary transition-all text-xs">
        </div>

        <!-- Action Category Select -->
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs text-slate-500 font-semibold shrink-0">Filter Aksi:</span>
            <select x-model="actionFilter" class="bg-darkSurface border border-slate-800 rounded-lg text-slate-350 text-xs px-3 py-1.5 focus:outline-none focus:border-brandPrimary font-semibold">
                <option value="All">Semua Aktivitas</option>
                <option value="Ban User">Ban User</option>
                <option value="Suspend User">Suspend User</option>
                <option value="Mute User">Mute User</option>
                <option value="Adjust CC">Penyesuaian CC</option>
                <option value="Approve Creator">Approve Kreator</option>
                <option value="Reject Creator">Tolak Kreator</option>
                <option value="Freeze Monetization">Monetisasi Dibekukan</option>
                <option value="Broadcast Announcement">Siaran Broadcast</option>
            </select>
        </div>

    </div>

    <!-- Audit Logs Table -->
    <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-darkCard/50 border-b border-slate-800 text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                        <th class="p-4 pl-6">Timestamp</th>
                        <th class="p-4">Administrator</th>
                        <th class="p-4 text-center font-semibold">Tipe Aksi</th>
                        <th class="p-4">Target</th>
                        <th class="p-4 pr-6">Rincian Log Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs text-slate-300">
                    <template x-for="(log, idx) in getFilteredLogs()" :key="idx">
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="p-4 pl-6 text-slate-500 font-medium" x-text="log.timestamp"></td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6.5 h-6.5 rounded-full bg-slate-800 text-[10px] font-bold text-slate-400 flex items-center justify-center">AD</div>
                                    <strong class="text-white text-xs" x-text="log.admin"></strong>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase tracking-wider inline-block"
                                      :class="{
                                          'bg-red-500/10 text-red-400 border border-red-500/20': log.action === 'Ban User' || log.action === 'Freeze CC' || log.action === 'Reject Creator',
                                          'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20': log.action === 'Approve Creator' || log.action === 'Activate User',
                                          'bg-amber-500/10 text-amber-400 border border-amber-500/20': log.action === 'Suspend User' || log.action === 'Freeze Monetization',
                                          'bg-brandPrimary/10 text-brandPrimary border border-brandPrimary/20': log.action === 'Adjust CC' || log.action === 'Broadcast Announcement'
                                      }" x-text="log.action"></span>
                            </td>
                            <td class="p-4 font-bold text-slate-200" x-text="log.target"></td>
                            <td class="p-4 pr-6 text-slate-400 italic" x-text="log.detail"></td>
                        </tr>
                    </template>
                    <tr x-show="getFilteredLogs().length === 0">
                        <td colspan="5" class="p-8 text-center text-slate-500 italic">Tidak ada audit log jejak tindakan admin ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
