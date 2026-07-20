<div x-data="{
    pageTab: 'Terms', // Terms | Privacy | About | Guidelines
    editorContent: {
        Terms: '# Syarat & Ketentuan Penggunaan NusaShare\n\nSelamat datang di NusaShare! Harap baca syarat penggunaan ini secara seksama sebelum mengakses web platform kami.\n\n1. Hak Kekayaan Intelektual\nSetiap karya novel, manga, dan visual yang diterbitkan di platform ini tetap sepenuhnya menjadi hak milik intelektual kreator masing-masing. Pembaca dilarang mendistribusikan karya tanpa izin tertulis dari kreator.\n\n2. Koin CC (Sakti)\nKoin sakti CC dibeli secara sah melalui payment gateway resmi. CC digunakan untuk meng-unlock konten premium dan mengapresiasi proses karya kreator.',
        Privacy: '# Kebijakan Privasi Pengguna NusaShare\n\nNusaShare berkomitmen penuh dalam melindungi keamanan privasi data pribadi Anda.\n\nKami mengumpulkan informasi pendaftaran seperti alamat email, nama pengguna, dan log audit deposit CC secara terenkripsi demi menjamin keselamatan transaksi.',
        About: '# Tentang NusaShare\n\nNusaShare adalah platform apresiasi karya kreatif lokal Indonesia, menjembatani kreator berbakat dengan para pendukung setia secara adil, transparan, dan organik.',
        Guidelines: '# Panduan Komunitas & Konten Layanan\n\nUntuk menjaga lingkungan NusaShare yang kondusif, harap perhatikan:\n- Dilarang keras mengunggah karya plagiat atau bajakan.\n- Konten dewasa (NSFW) wajib menggunakan tag sensor 18+.\n- Dilarang mengirim link spam di kolom komentar umum.'
    },
    showSuccessAlert: false,
    alertMsg: '',
    
    savePage() {
        this.alertMsg = 'Halaman statis ' + this.pageTab + ' berhasil diterbitkan!';
        this.showSuccessAlert = true;
        this.logAction('CMS Update Page', 'Page: ' + this.pageTab, 'Updated static page text content');
        setTimeout(() => this.showSuccessAlert = false, 3000);
    }
}">

    <!-- Alert Box -->
    <div x-cloak x-show="showSuccessAlert" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xl animate-pulse">
        <span class="material-symbols-outlined">check_circle</span>
        <span x-text="alertMsg"></span>
    </div>

    <!-- Main Container -->
    <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        
        <!-- Tab Bar -->
        <div class="bg-darkCard/50 border-b border-slate-800 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            
            <!-- Document selects -->
            <div class="flex gap-2">
                <button @click="pageTab = 'Terms'" :class="pageTab === 'Terms' ? 'bg-brandPrimary text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Terms of Service</button>
                <button @click="pageTab = 'Privacy'" :class="pageTab === 'Privacy' ? 'bg-brandPrimary text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Privacy Policy</button>
                <button @click="pageTab = 'About'" :class="pageTab === 'About' ? 'bg-brandPrimary text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">About Us</button>
                <button @click="pageTab = 'Guidelines'" :class="pageTab === 'Guidelines' ? 'bg-brandPrimary text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Community Guidelines</button>
            </div>

            <!-- Publish Button -->
            <button @click="savePage()" class="w-full sm:w-auto px-5 py-2.5 bg-brandPrimary hover:bg-indigo-650 text-white text-xs font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 glow-indigo">
                <span class="material-symbols-outlined text-sm">publish</span> Terbitkan Perubahan
            </button>

        </div>

        <!-- Editor Workspace -->
        <div class="p-6 space-y-4">
            
            <!-- Dummy WYSIWYG Toolbar -->
            <div class="flex items-center gap-1.5 bg-slate-900 border border-slate-850 p-2 rounded-xl flex-wrap">
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Bold">
                    <span class="material-symbols-outlined text-sm">format_bold</span>
                </button>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Italic">
                    <span class="material-symbols-outlined text-sm">format_italic</span>
                </button>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Underline">
                    <span class="material-symbols-outlined text-sm">format_underlined</span>
                </button>
                <div class="w-px h-5 bg-slate-800 mx-1"></div>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Bullet List">
                    <span class="material-symbols-outlined text-sm">format_list_bulleted</span>
                </button>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Numbered List">
                    <span class="material-symbols-outlined text-sm">format_list_numbered</span>
                </button>
                <div class="w-px h-5 bg-slate-800 mx-1"></div>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Insert Link">
                    <span class="material-symbols-outlined text-sm">link</span>
                </button>
                <button class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center" title="Insert Image">
                    <span class="material-symbols-outlined text-sm">image</span>
                </button>
            </div>

            <!-- Editor Textarea Area -->
            <div class="flex-1 relative">
                <textarea x-model="editorContent[pageTab]" rows="16"
                          class="w-full px-5 py-4 rounded-2xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brandPrimary text-xs font-mono leading-relaxed"
                          style="resize: none;"></textarea>
            </div>

        </div>

    </div>

</div>
