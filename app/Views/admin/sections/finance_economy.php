<div x-data="{
    searchUserQuery: '',
    selectedUsername: '',
    adjustAmount: 100,
    adjustReason: 'Bonus Event Kreatif Juni',
    adjustType: 'add',
    showSuccessAlert: false,
    alertMsg: '',
    
    getFilteredUsers() {
        if (!this.searchUserQuery) return [];
        return this.users.filter(u => u.username.toLowerCase().includes(this.searchUserQuery.toLowerCase())).slice(0, 3);
    },
    selectUser(uName) {
        this.selectedUsername = uName;
        this.searchUserQuery = uName;
    },
    processAdjustment() {
        if (!this.selectedUsername) return;
        const amt = this.adjustType === 'add' ? this.adjustAmount : -this.adjustAmount;
        this.adjustUserCC(this.selectedUsername, amt, this.adjustReason);
        this.alertMsg = 'Berhasil menyesuaikan ' + amt + ' CC milik @' + this.selectedUsername + '!';
        this.showSuccessAlert = true;
        
        // Reset
        this.selectedUsername = '';
        this.searchUserQuery = '';
        this.adjustAmount = 100;
        this.adjustReason = 'Bonus Pembaca Teraktif';
        setTimeout(() => this.showSuccessAlert = false, 3500);
    },
    saveConversion() {
        this.alertMsg = 'Konversi IDR ke CC diperbarui: 1 IDR = ' + this.economySettings.conversionRate + ' CC';
        this.showSuccessAlert = true;
        this.logAction('Update Rate', 'Conversion Rate', 'Set conversion: 1 IDR = ' + this.economySettings.conversionRate + ' CC');
        setTimeout(() => this.showSuccessAlert = false, 3500);
    },
    toggleGlobalFreeze() {
        this.alertMsg = this.economySettings.globalFreeze ? 'Ekonomi CC Dibekukan secara Global!' : 'Ekonomi CC Berjalan Normal.';
        this.showSuccessAlert = true;
        this.logAction('Toggle Global CC Lock', 'All Transactions', 'Status: ' + (this.economySettings.globalFreeze ? 'Locked' : 'Unlocked'));
        setTimeout(() => this.showSuccessAlert = false, 3500);
    }
}">

    <!-- Alert Box -->
    <div x-cloak x-show="showSuccessAlert" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xl animate-pulse">
        <span class="material-symbols-outlined">check_circle</span>
        <span x-text="alertMsg"></span>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Column 1: Adjust Wallet & Conversion rate -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Manual Wallet Adjustment -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6 glow-indigo">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-brandPrimary">account_balance_wallet</span>
                    Manual Wallet CC Adjustment
                </h3>
                
                <div class="space-y-4">
                    <!-- User Search dropdown -->
                    <div class="relative">
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Cari Dompet User</label>
                        <input type="text" x-model="searchUserQuery" placeholder="Ketik nama user..."
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brandPrimary text-xs font-bold">
                        
                        <div x-cloak x-show="getFilteredUsers().length > 0" class="absolute left-0 right-0 mt-2 bg-darkCard border border-slate-800 rounded-xl overflow-hidden z-20 shadow-2xl">
                            <template x-for="u in getFilteredUsers()" :key="u.id">
                                <div @click="selectUser(u.username)" class="p-2.5 hover:bg-slate-800/60 transition-colors text-xs text-slate-200 cursor-pointer flex justify-between">
                                    <span class="font-semibold" x-text="'@' + u.username"></span>
                                    <span class="text-brandAccent" x-text="u.cc_balance + ' CC'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Aksi Saldo</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="adjustType = 'add'" :class="adjustType === 'add' ? 'bg-brandPrimary text-white' : 'bg-slate-800 text-slate-400'"
                                        class="py-2.5 rounded-lg text-[10px] font-semibold transition-all">
                                    Tambah CC
                                </button>
                                <button @click="adjustType = 'deduct'" :class="adjustType === 'deduct' ? 'bg-red-500 text-white' : 'bg-slate-800 text-slate-400'"
                                        class="py-2.5 rounded-lg text-[10px] font-semibold transition-all">
                                    Kurang CC
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Nominal Koin (CC)</label>
                            <input type="number" x-model="adjustAmount" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Berikan Catatan / Alasan</label>
                        <textarea x-model="adjustReason" rows="2.5" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 focus:outline-none focus:border-brandPrimary text-xs"></textarea>
                    </div>

                    <button @click="processAdjustment()" :disabled="!selectedUsername"
                            :class="selectedUsername ? 'bg-brandPrimary hover:bg-indigo-650' : 'bg-slate-800 text-slate-600 cursor-not-allowed'"
                            class="w-full py-2.5 rounded-xl text-white text-xs font-bold transition-all shadow-md">
                        Eksekusi Penyesuaian Saldo
                    </button>
                </div>
            </div>

            <!-- Conversion rate editor -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-brandAccent">currency_exchange</span>
                    Kurs Konversi Rupiah (IDR)
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-1 p-3.5 bg-slate-900 border border-slate-850 rounded-xl flex justify-between items-center text-xs">
                            <span class="text-slate-500">Mata Uang Dasar:</span>
                            <strong class="text-white">Rp 1 Rupiah (IDR)</strong>
                        </div>
                        <div class="w-10 text-center text-slate-400 font-bold text-lg">=</div>
                        <div class="flex-1 relative">
                            <input type="number" x-model="economySettings.conversionRate"
                                   class="w-full pl-3 pr-10 py-3.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] text-brandAccent font-bold">CC</span>
                        </div>
                    </div>
                    
                    <button @click="saveConversion()" class="w-full py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-xl text-xs font-bold transition-all">
                        Perbarui Nilai Kurs Konversi
                    </button>
                </div>
            </div>

        </div>

        <!-- Column 2: System Economy Freeze and stats -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Global Freeze control -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6"
                 :class="economySettings.globalFreeze ? 'border-red-500/30' : ''">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-red-500">lock_reset</span>
                    Global Transaction CC Freeze
                </h3>
                
                <div class="space-y-4">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Jika diaktifkan, seluruh aktivitas debit/kredit CC global di platform (Unlock Chapter, Download, Top Up) akan dikunci demi alasan keamanan.
                    </p>

                    <!-- Large toggle switch -->
                    <div class="flex items-center justify-between p-4 bg-slate-950 border border-slate-850 rounded-2xl">
                        <div>
                            <strong class="text-xs text-white block">Bekukan Transaksi Global</strong>
                            <span class="text-[9px] text-slate-500">Gunakan saat maintenance/darurat</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="economySettings.globalFreeze" @change="toggleGlobalFreeze()" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                        </label>
                    </div>
                    
                    <div x-show="economySettings.globalFreeze" class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-[10px] text-red-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm shrink-0">warning</span>
                        Sistem Sedang Memblokir Transaksi CC!
                    </div>
                </div>
            </div>

            <!-- Economy overview card -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4">Ringkasan Nilai Ekonomi</h3>
                
                <div class="space-y-3.5 text-xs text-slate-400">
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>Porsi Konversi Saat Ini:</span>
                        <strong class="text-white" x-text="'1 IDR = ' + economySettings.conversionRate + ' CC'"></strong>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>Total CC Beredar:</span>
                        <strong class="text-white">2,450,000 CC</strong>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>Locked CC (Escrow):</span>
                        <strong class="text-brandPrimary">125,000 CC</strong>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span>Tax / Platform Fee:</span>
                        <strong class="text-brandAccent">15%</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
