<div x-data="{
    showSuccessAlert: false,
    alertMsg: '',
    
    saveHero() {
        this.alertMsg = 'Hero Section landing page berhasil diperbarui!';
        this.showSuccessAlert = true;
        this.logAction('CMS Update', 'Hero Section', 'Updated title: ' + this.cmsHome.heroTitle);
        setTimeout(() => this.showSuccessAlert = false, 3000);
    },
    savePromotions() {
        this.alertMsg = 'Promotional banners saved successfully!';
        this.showSuccessAlert = true;
        this.logAction('CMS Update', 'Promotion Banners', 'Updated active promotion slider list');
        setTimeout(() => this.showSuccessAlert = false, 3000);
    }
}">

    <!-- Alert Box -->
    <div x-cloak x-show="showSuccessAlert" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xl animate-pulse">
        <span class="material-symbols-outlined">check_circle</span>
        <span x-text="alertMsg"></span>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Hero Editor -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Hero section details -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6 glow-indigo">
                <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-brandPrimary">edit_note</span>
                    Hero Section Landing Page Editor
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Headline Utama (Title)</label>
                        <input type="text" x-model="cmsHome.heroTitle"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Subheadline (Subtitle Description)</label>
                        <textarea x-model="cmsHome.heroSubtitle" rows="3.5"
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 focus:outline-none focus:border-brandPrimary text-xs leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Label Tombol CTA</label>
                            <input type="text" x-model="cmsHome.ctaText"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Background Banner Image URL</label>
                            <input type="text" x-model="cmsHome.bannerUrl"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                        </div>
                    </div>

                    <!-- Visual image preview -->
                    <div class="p-4 bg-slate-950 border border-slate-850 rounded-2xl">
                        <span class="block text-[10px] text-slate-500 uppercase font-semibold mb-2">Live Preview Banner</span>
                        <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900 relative">
                            <img :src="cmsHome.bannerUrl" alt="Live Preview Banner" class="w-full h-full object-cover opacity-60">
                            <div class="absolute inset-0 p-6 flex flex-col justify-end">
                                <h4 class="font-bold text-white text-base leading-tight mb-2 max-w-sm" x-text="cmsHome.heroTitle"></h4>
                                <p class="text-[10px] text-slate-300 line-clamp-2 max-w-sm" x-text="cmsHome.heroSubtitle"></p>
                            </div>
                        </div>
                    </div>

                    <button @click="saveHero()" class="w-full py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Simpan & Update Landing Page
                    </button>
                </div>
            </div>

            <!-- Promotion banner lists -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-white text-sm">Banner Promosi Slider</h3>
                    <button class="px-2.5 py-1 bg-slate-800 hover:bg-slate-750 text-slate-300 text-[10px] font-bold rounded-lg border border-slate-700 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">add</span> Add Banner
                    </button>
                </div>
                
                <div class="space-y-3.5">
                    <div class="p-3 bg-darkCard border border-slate-850 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 rounded bg-slate-900 overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-white">Promo CC Bonus 20% Ramadhan</h4>
                                <span class="text-[9px] text-slate-500">CTA Link: /topup | Status: Aktif</span>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-brandPrimary"></div>
                        </label>
                    </div>

                    <div class="p-3 bg-darkCard border border-slate-850 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 rounded bg-slate-900 overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=200" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-white">Event Cerpen StarSoul Batch 3</h4>
                                <span class="text-[9px] text-slate-500">CTA Link: /event | Status: Aktif</span>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-brandPrimary"></div>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Featured Works & Trending Toggles -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Featured works drag list -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-white text-sm">Karya Unggulan Pilihan</h3>
                    <button class="text-xs font-semibold text-brandPrimary hover:underline">Kelola Semua</button>
                </div>
                
                <div class="space-y-3.5">
                    <template x-for="work in works.slice(0, 3)" :key="work.id">
                        <div class="p-3 bg-darkCard border border-slate-850 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img :src="work.cover_url" class="w-9 h-12 object-cover rounded shrink-0">
                                <div>
                                    <h4 class="font-bold text-xs text-white" x-text="work.title"></h4>
                                    <span class="text-[9px] text-slate-500" x-text="'Oleh @' + work.creator"></span>
                                </div>
                            </div>
                            <button class="w-8 h-8 bg-slate-850 hover:bg-slate-800 text-slate-400 hover:text-white rounded-lg flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-sm">drag_handle</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Trending algorithm parameters -->
            <div class="bg-darkSurface border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4">Algoritma Trending Section</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3.5 bg-slate-950 border border-slate-850 rounded-xl">
                        <div>
                            <strong class="text-xs text-slate-200 block">Sistem Otomatis (DAU/Views)</strong>
                            <span class="text-[9px] text-slate-500">Trending ditentukan secara real-time</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-brandPrimary"></div>
                        </label>
                    </div>

                    <div class="text-xs text-slate-400">
                        <label class="block text-[9px] text-slate-500 uppercase font-semibold mb-1.5">Batas Kuota Tampil (Max Items)</label>
                        <select class="w-full bg-slate-900 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:border-brandPrimary">
                            <option value="4">4 Karya Teratas</option>
                            <option value="8">8 Karya Teratas</option>
                            <option value="12">12 Karya Teratas</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
