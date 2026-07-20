<div x-data="{
    newTitle: '',
    newMessage: '',
    targetGroup: 'Semua User',
    showSuccessAlert: false,
    
    sendBroadcast() {
        if (!this.newTitle || !this.newMessage) return;
        const now = new Date();
        const formattedDate = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
        
        this.broadcasts.unshift({
            id: this.broadcasts.length + 1,
            title: this.newTitle,
            message: this.newMessage,
            target: this.targetGroup,
            date: formattedDate
        });
        
        this.logAction('Broadcast Announcement', this.newTitle, 'Target: ' + this.targetGroup);
        
        // Reset
        this.newTitle = '';
        this.newMessage = '';
        this.targetGroup = 'Semua User';
        this.showSuccessAlert = true;
        setTimeout(() => this.showSuccessAlert = false, 3000);
    }
}">

    <!-- Success Alert -->
    <div x-cloak x-show="showSuccessAlert" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xl animate-pulse">
        <span class="material-symbols-outlined">check_circle</span>
        Pengumuman massal berhasil disiarkan (Broadcasted) ke seluruh target user!
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Create Broadcast Form -->
        <div class="lg:col-span-7 bg-darkSurface border border-slate-800 rounded-2xl p-6 glow-indigo">
            <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-brandPrimary">campaign</span>
                Broadcast Pengumuman Massal
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Judul Pengumuman</label>
                    <input type="text" x-model="newTitle" placeholder="Ketik judul pesan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                </div>

                <div>
                    <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Target Pengguna</label>
                    <select x-model="targetGroup" class="w-full bg-slate-900 border border-slate-800 text-slate-350 rounded-lg px-3 py-2.5 focus:outline-none focus:border-brandPrimary text-xs font-semibold">
                        <option value="Semua User">Semua Pengguna (Pembaca & Kreator)</option>
                        <option value="Hanya Pembaca">Hanya Pembaca</option>
                        <option value="Kreator">Hanya Kreator</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Isi Pesan Pengumuman</label>
                    <textarea x-model="newMessage" rows="6" placeholder="Ketik isi pengumuman massal disini..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 focus:outline-none focus:border-brandPrimary text-xs leading-relaxed"></textarea>
                </div>

                <button @click="sendBroadcast()" class="w-full py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    Siarkan Broadcast Sekarang
                </button>
            </div>
        </div>

        <!-- Right: Broadcast History Feed -->
        <div class="lg:col-span-5 bg-darkSurface border border-slate-800 rounded-2xl p-6 flex flex-col">
            <h3 class="font-bold text-white text-sm mb-4">Riwayat Pengumuman Aktif</h3>
            
            <div class="space-y-4 flex-1 overflow-y-auto max-h-[420px] pr-1">
                <template x-for="b in broadcasts" :key="b.id">
                    <div class="p-3.5 bg-darkCard border border-slate-850 rounded-xl hover:border-slate-800 transition-all text-xs">
                        <div class="flex justify-between items-center mb-1">
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-brandAccent text-[8px] font-bold uppercase tracking-wider" x-text="b.target"></span>
                            <span class="text-[9px] text-slate-500 font-bold" x-text="b.date"></span>
                        </div>
                        <h4 class="font-bold text-white mb-1.5" x-text="b.title"></h4>
                        <p class="text-[10px] text-slate-400 leading-relaxed italic" x-text="b.message"></p>
                    </div>
                </template>
            </div>
        </div>

    </div>

</div>
