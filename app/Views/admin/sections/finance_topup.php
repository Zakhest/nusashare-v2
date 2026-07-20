<div x-data="{
    tab: 'Pending', // Pending | Success | Failed
    selectedTopup: null,
    showReceiptModal: false,
    
    getTopupsByTab() {
        return this.topups.filter(t => t.status === this.tab);
    },
    openReceipt(topup) {
        this.selectedTopup = topup;
        this.showReceiptModal = true;
    }
}">

    <!-- Filter Tab Bar -->
    <div class="flex justify-between items-center gap-4 mb-6">
        <div class="bg-darkSurface border border-slate-850 p-1 rounded-xl flex gap-1">
            <button @click="tab = 'Pending'" :class="tab === 'Pending' ? 'bg-amber-500 text-white shadow-lg' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400" x-show="topups.filter(t=>t.status==='Pending').length > 0"></span>
                Antrian Gateway (Pending)
            </button>
            <button @click="tab = 'Success'" :class="tab === 'Success' ? 'bg-brandPrimary text-white shadow-lg' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                Sukses (Terproses)
            </button>
            <button @click="tab = 'Failed'" :class="tab === 'Failed' ? 'bg-red-500 text-white shadow-lg' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                Gagal / Ditolak
            </button>
        </div>
    </div>

    <!-- Topup table -->
    <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-darkCard/50 border-b border-slate-800 text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                        <th class="p-4 pl-6">ID Transaksi</th>
                        <th class="p-4">User</th>
                        <th class="p-4 text-right">Rupiah (IDR)</th>
                        <th class="p-4 text-right">Koin CC</th>
                        <th class="p-4">Metode Pembayaran</th>
                        <th class="p-4 text-center">Bukti Transfer</th>
                        <th class="p-4 pr-6 text-right">Tgl Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs text-slate-300">
                    <template x-for="topup in getTopupsByTab()" :key="topup.id">
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="p-4 pl-6 font-bold text-slate-500" x-text="topup.id"></td>
                            <td class="p-4 font-semibold text-white" x-text="'@' + topup.user"></td>
                            <td class="p-4 text-right font-bold text-slate-100" x-text="'Rp ' + topup.amount_idr.toLocaleString()"></td>
                            <td class="p-4 text-right font-bold text-brandAccent" x-text="topup.amount_cc + ' CC'"></td>
                            <td class="p-4 text-slate-400" x-text="topup.method"></td>
                            <td class="p-4 text-center">
                                <button @click="openReceipt(topup)" class="px-2 py-1 bg-slate-800 hover:bg-slate-750 text-slate-300 border border-slate-700 rounded-lg text-[9px] font-bold transition-all">
                                    Lihat Slip
                                </button>
                            </td>
                            <td class="p-4 pr-6 text-right text-slate-500" x-text="topup.created_at"></td>
                        </tr>
                    </template>
                    <tr x-show="getTopupsByTab().length === 0">
                        <td colspan="7" class="p-8 text-center text-slate-500 italic">Tidak ada transaksi pembayaran dalam kategori ini.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Receipt Slip Preview -->
    <div x-cloak x-show="showReceiptModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-darkBg/80 backdrop-blur-sm" x-transition>
        <div @click.outside="showReceiptModal = false" class="bg-darkSurface border border-slate-800 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl glow-indigo">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-darkCard/25">
                <h3 class="font-bold text-white text-base" x-text="'Detail Transaksi: ' + selectedTopup?.id"></h3>
                <button @click="showReceiptModal = false" class="text-slate-400 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-4" x-if="selectedTopup">
                <!-- Slip Image -->
                <div class="aspect-[3/4] w-full bg-slate-900 border border-slate-800 rounded-xl overflow-hidden relative group">
                    <img :src="selectedTopup?.receipt" alt="Receipt Transfer" class="w-full h-full object-cover">
                </div>
                
                <div class="p-3 bg-darkCard border border-slate-850 rounded-xl text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-500">User Pembeli:</span>
                        <strong class="text-white" x-text="'@' + selectedTopup?.user"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah Transfer:</span>
                        <strong class="text-white" x-text="'Rp ' + (selectedTopup?.amount_idr ? selectedTopup.amount_idr.toLocaleString() : '0')"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mata Uang CC:</span>
                        <strong class="text-brandAccent" x-text="selectedTopup?.amount_cc + ' CC'"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Pembayaran:</span>
                        <strong class="text-white capitalize" x-text="selectedTopup?.status"></strong>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button @click="showReceiptModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-750 text-white rounded-xl text-xs font-bold transition-all shadow-md">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

</div>
