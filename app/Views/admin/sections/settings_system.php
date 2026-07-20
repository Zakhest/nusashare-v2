<div x-data="{
    showSuccessAlert: false,
    alertMsg: '',
    
    saveConfig() {
        this.alertMsg = 'Konfigurasi sistem & batas kapasitas berhasil diperbarui!';
        this.showSuccessAlert = true;
        this.logAction('System Update Settings', 'Global Config', 'Updated limits: ' + this.systemSettings.uploadNovelLimit + 'MB Novel / ' + this.systemSettings.uploadMangaLimit + 'MB Manga');
        setTimeout(() => this.showSuccessAlert = false, 3000);
    },
    toggleMaintenance() {
        this.alertMsg = this.systemSettings.maintenanceMode ? 'Sistem beralih ke Maintenance Mode!' : 'Sistem kembali Online dan aktif.';
        this.showSuccessAlert = true;
        this.logAction('System Maintenance Toggle', 'Maintenance Status', 'Set: ' + (this.systemSettings.maintenanceMode ? 'Active' : 'Inactive'));
        setTimeout(() => this.showSuccessAlert = false, 3000);
    }
}">

    <!-- Success Alert -->
    <div x-cloak x-show="showSuccessAlert" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xl animate-pulse">
        <span class="material-symbols-outlined">check_circle</span>
        <span x-text="alertMsg"></span>
    </div>

    <!-- Configuration Panels Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Column 1: Site Metadata & Brandings -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Site Identity Card -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6 glow-indigo">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-brandPrimary">language</span>
                    Identitas & Branding Situs
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Nama Situs (Site Name)</label>
                        <input type="text" x-model="systemSettings.siteName" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Tagline / Deskripsi Situs</label>
                        <textarea x-model="systemSettings.siteDescription" rows="2.5" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-350 focus:outline-none focus:border-brandPrimary text-xs leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Logo URL</label>
                        <input type="text" x-model="systemSettings.siteLogo" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-semibold">
                    </div>

                    <button @click="saveConfig()" class="w-full py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Simpan Identitas Situs
                    </button>
                </div>
            </div>

            <!-- Upload Limits Editor -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-brandAccent">drive_folder_upload</span>
                    Batas Kapasitas & Kuota Upload
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Max Upload Novel (per File)</label>
                            <div class="relative">
                                <input type="number" x-model="systemSettings.uploadNovelLimit" class="w-full pl-3 pr-12 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-500 font-bold">MB</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Max Upload Manga / PDF</label>
                            <div class="relative">
                                <input type="number" x-model="systemSettings.uploadMangaLimit" class="w-full pl-3 pr-12 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-500 font-bold">MB</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Total Kapasitas Penyimpanan Server (Storage Quota)</label>
                        <div class="relative">
                            <input type="number" x-model="systemSettings.storageTierQuota" class="w-full pl-3 pr-12 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-500 font-bold">GB</span>
                        </div>
                    </div>

                    <button @click="saveConfig()" class="w-full py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Perbarui Batas Kapasitas
                    </button>
                </div>
            </div>

        </div>

        <!-- Column 2: Maintenance Mode Toggles -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Maintenance mode switch card -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6"
                 :class="systemSettings.maintenanceMode ? 'border-red-500/30 glow-indigo' : ''">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-red-500">construction</span>
                    Maintenance Mode (Perbaikan)
                </h3>
                
                <div class="space-y-4">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Jika diaktifkan, pengunjung biasa tidak dapat mengakses website NusaShare dan akan diarahkan ke halaman "Situs Sedang Diperbaiki". Akses dashboard admin tetap terbuka.
                    </p>

                    <!-- Toggle card panel -->
                    <div class="flex items-center justify-between p-4 bg-slate-950 border border-slate-850 rounded-2xl">
                        <div>
                            <strong class="text-xs text-white block">Aktifkan Maintenance</strong>
                            <span class="text-[9px] text-slate-500">Lock public entry points</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="systemSettings.maintenanceMode" @change="toggleMaintenance()" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                        </label>
                    </div>

                    <div x-cloak x-show="systemSettings.maintenanceMode" class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-[10px] text-red-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm shrink-0">warning</span>
                        Situs NusaShare Sedang Ditutup untuk Umum!
                    </div>
                </div>
            </div>

            <!-- Server Environment details card -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4">Informasi Lingkungan Server</h3>
                
                <div class="space-y-3.5 text-xs text-slate-400">
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>Framework Version:</span>
                        <strong class="text-white">CodeIgniter v4.4.8</strong>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>PHP Version:</span>
                        <strong class="text-white">v8.2.12</strong>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-850">
                        <span>Database System:</span>
                        <strong class="text-white">MySQL (MariaDB)</strong>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span>Server Environment:</span>
                        <strong class="text-brandAccent">Development (XAMPP)</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
