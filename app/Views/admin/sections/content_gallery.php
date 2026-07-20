<div x-data="{
    typeFilter: 'All', // All | Cover Art | Character Asset
    selectedAsset: null,
    showPreviewModal: false,
    
    mockAssets: [
        { id: 1, title: 'Garuda Rise Main Cover', creator: 'budisud', type: 'Cover Art', img: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=600', dimensions: '1200 x 1600' },
        { id: 2, title: 'Cinta di Batas Senja Heroine Sketch', creator: 'dewi_novel', type: 'Character Asset', img: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600', dimensions: '1080 x 1080' },
        { id: 3, title: 'Wisnu Dreamscape Landscape BG', creator: 'rianmanga', type: 'Character Asset', img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600', dimensions: '1920 x 1080' },
        { id: 4, title: 'The Lost Temple Poster Mock', creator: 'budisud', type: 'Cover Art', img: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600', dimensions: '800 x 1200' },
        { id: 5, title: 'Sketsa Awan Character Pack', creator: 'tonidesign', type: 'Character Asset', img: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600', dimensions: '2000 x 2000' }
    ],

    getFilteredAssets() {
        return this.mockAssets.filter(a => this.typeFilter === 'All' || a.type === this.typeFilter);
    },
    openPreview(asset) {
        this.selectedAsset = asset;
        this.showPreviewModal = true;
    }
}">

    <!-- Filter Tab Bar -->
    <div class="flex justify-between items-center gap-4 mb-8">
        <div class="bg-darkSurface border border-slate-850 p-1 rounded-xl flex gap-1">
            <button @click="typeFilter = 'All'" :class="typeFilter === 'All' ? 'bg-brandPrimary text-white shadow-lg shadow-indigo-500/15' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                Semua Aset Media
            </button>
            <button @click="typeFilter = 'Cover Art'" :class="typeFilter === 'Cover Art' ? 'bg-brandPrimary text-white shadow-lg shadow-indigo-500/15' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                Gambar Sampul (Cover)
            </button>
            <button @click="typeFilter = 'Character Asset'" :class="typeFilter === 'Character Asset' ? 'bg-brandPrimary text-white shadow-lg shadow-indigo-500/15' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                Aset Visual Karakter
            </button>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <template x-for="asset in getFilteredAssets()" :key="asset.id">
            <div class="bg-darkSurface border border-slate-800 rounded-2xl overflow-hidden group hover:border-slate-700 transition-all flex flex-col shadow-xl">
                
                <!-- Image Container with hover overlay -->
                <div class="aspect-[4/3] relative bg-slate-950 overflow-hidden cursor-pointer" @click="openPreview(asset)">
                    <img :src="asset.img" :alt="asset.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-darkBg/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                    </div>
                </div>

                <!-- Info Block -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[8px] font-bold uppercase tracking-wider" x-text="asset.type"></span>
                            <span class="text-[9px] text-slate-500" x-text="asset.dimensions"></span>
                        </div>
                        <h4 class="font-bold text-xs text-white line-clamp-1 mb-1" x-text="asset.title"></h4>
                        <p class="text-[10px] text-slate-400" x-text="'Oleh: @' + asset.creator"></p>
                    </div>
                </div>

            </div>
        </template>
        <div x-show="getFilteredAssets().length === 0" class="col-span-full bg-darkSurface border border-dashed border-slate-800 rounded-2xl p-12 text-center text-slate-500 italic">
            Tidak ada aset visual dalam kategori ini.
        </div>
    </div>

    <!-- MODAL 1: Image Lightbox Preview -->
    <div x-cloak x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-darkBg/90 backdrop-blur-sm" x-transition>
        <div @click.outside="showPreviewModal = false" class="max-w-3xl w-full flex flex-col overflow-hidden relative">
            <button @click="showPreviewModal = false" class="absolute top-4 right-4 text-white bg-slate-900/60 p-2 rounded-full hover:bg-slate-800 transition-colors z-10 shadow-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
            <img :src="selectedAsset?.img" alt="Full Image" class="max-h-[80vh] object-contain rounded-2xl bg-black/60 shadow-2xl">
            <div class="p-4 bg-darkSurface/90 border border-slate-800 rounded-xl mt-4 text-xs">
                <h4 class="font-bold text-white text-sm" x-text="selectedAsset?.title"></h4>
                <p class="text-slate-400 mt-1" x-text="'Diunggah oleh @' + selectedAsset?.creator + ' | Tipe: ' + selectedAsset?.type + ' | Dimensi: ' + selectedAsset?.dimensions"></p>
            </div>
        </div>
    </div>

</div>
