<div x-data="{
    activeCategory: 1,
    newCategoryName: '',
    showAddCategoryInput: false,
    editingFaqId: null,
    
    // Form fields for new/edit faq
    faqQuestion: '',
    faqAnswer: '',
    faqMode: 'create', // create | edit
    
    addCategory() {
        if (!this.newCategoryName) return;
        const newId = this.faqCategories.length + 1;
        this.faqCategories.push({
            id: newId,
            name: this.newCategoryName
        });
        this.newCategoryName = '';
        this.showAddCategoryInput = false;
        this.logAction('FAQ Add Category', this.newCategoryName, 'Added new category header');
    },
    deleteCategory(id) {
        this.faqCategories = this.faqCategories.filter(c => c.id !== id);
        this.faqs = this.faqs.filter(f => f.category_id !== id);
        if (this.activeCategory === id && this.faqCategories.length > 0) {
            this.activeCategory = this.faqCategories[0].id;
        }
        this.logAction('FAQ Delete Category', 'Category ID: ' + id, 'Removed category and its respective questions');
    },
    openCreateFaq() {
        this.faqQuestion = '';
        this.faqAnswer = '';
        this.faqMode = 'create';
        this.editingFaqId = -1; // special value to trigger form view
    },
    openEditFaq(faq) {
        this.faqQuestion = faq.question;
        this.faqAnswer = faq.answer;
        this.faqMode = 'edit';
        this.editingFaqId = faq.id;
    },
    submitFaq() {
        if (!this.faqQuestion || !this.faqAnswer) return;
        
        if (this.faqMode === 'create') {
            const nextId = this.faqs.length + 1;
            this.faqs.push({
                id: nextId,
                category_id: this.activeCategory,
                question: this.faqQuestion,
                answer: this.faqAnswer,
                open: false
            });
            this.logAction('FAQ Add Question', this.faqQuestion, 'Added Q&A entry');
        } else {
            let f = this.faqs.find(item => item.id === this.editingFaqId);
            if (f) {
                f.question = this.faqQuestion;
                f.answer = this.faqAnswer;
            }
            this.logAction('FAQ Edit Question', this.faqQuestion, 'Updated Q&A contents');
        }
        
        this.editingFaqId = null;
    },
    deleteFaq(id) {
        this.faqs = this.faqs.filter(f => f.id !== id);
        this.logAction('FAQ Delete Question', 'FAQ ID: ' + id, 'Removed FAQ entry');
    }
}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Categories List -->
        <div class="lg:col-span-4 bg-darkSurface border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-850">
                    <h3 class="font-bold text-white text-sm">Kategori FAQ</h3>
                    <button @click="showAddCategoryInput = !showAddCategoryInput" class="w-8 h-8 rounded-lg bg-slate-850 hover:bg-slate-850 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                </div>
                
                <!-- Add Category Inline Form -->
                <div x-show="showAddCategoryInput" class="mb-4 space-y-2 p-2 bg-slate-900 border border-slate-850 rounded-xl">
                    <input type="text" x-model="newCategoryName" placeholder="Nama Kategori..." class="w-full px-2 py-1.5 rounded bg-slate-950 border border-slate-850 text-white text-xs">
                    <div class="flex justify-end gap-1">
                        <button @click="showAddCategoryInput = false" class="px-2 py-1 bg-slate-800 text-[10px] text-slate-400 rounded">Batal</button>
                        <button @click="addCategory()" class="px-2 py-1 bg-brandPrimary text-[10px] text-white rounded">Simpan</button>
                    </div>
                </div>

                <!-- Categories list buttons -->
                <div class="space-y-1.5">
                    <template x-for="cat in faqCategories" :key="cat.id">
                        <div class="flex items-center justify-between group rounded-xl px-3 py-2 transition-all cursor-pointer"
                             :class="activeCategory === cat.id ? 'bg-brandPrimary/10 border-l-2 border-brandPrimary' : 'hover:bg-slate-850/50'">
                            <span @click="activeCategory = cat.id; editingFaqId = null;" class="text-xs font-bold flex-1"
                                  :class="activeCategory === cat.id ? 'text-brandPrimary' : 'text-slate-300'" x-text="cat.name"></span>
                            <button @click="deleteCategory(cat.id)" class="opacity-0 group-hover:opacity-100 hover:text-red-400 text-slate-500 transition-opacity">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-500 italic mt-6 leading-relaxed">
                Menghapus kategori akan menghapus seluruh data tanya jawab yang ada di dalamnya secara otomatis.
            </p>
        </div>

        <!-- Right Column: Accordion Questions Editor -->
        <div class="lg:col-span-8 bg-darkSurface border border-slate-800 rounded-2xl p-6 glow-indigo flex flex-col">
            
            <!-- Default QA view -->
            <div x-show="editingFaqId === null" class="flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-white text-base" x-text="'Daftar Pertanyaan: ' + (faqCategories.find(c=>c.id===activeCategory)?.name || '')"></h3>
                            <p class="text-[10px] text-slate-500">Kelola FAQ pembaca di halaman bantuan</p>
                        </div>
                        <button @click="openCreateFaq()" class="px-3 py-1.5 bg-brandPrimary hover:bg-indigo-650 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                            Buat Tanya Jawab
                        </button>
                    </div>

                    <!-- Accordion Q&A list -->
                    <div class="space-y-3.5">
                        <template x-for="f in faqs.filter(faq => faq.category_id === activeCategory)" :key="f.id">
                            <div class="bg-darkCard border border-slate-850 rounded-2xl overflow-hidden">
                                
                                <!-- Accordion Header -->
                                <div class="px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-slate-800/20" @click="f.open = !f.open">
                                    <h4 class="font-bold text-xs text-white" x-text="f.question"></h4>
                                    <div class="flex items-center gap-2">
                                        <button @click.stop="openEditFaq(f)" class="w-7 h-7 rounded-lg hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <button @click.stop="deleteFaq(f.id)" class="w-7 h-7 rounded-lg hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-red-400">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                        <span class="material-symbols-outlined text-slate-500 text-lg transition-transform" :class="f.open ? 'rotate-180' : ''">expand_more</span>
                                    </div>
                                </div>

                                <!-- Accordion Body -->
                                <div x-show="f.open" class="px-4 pb-4 pt-1 text-[11px] text-slate-400 border-t border-slate-850/50 leading-relaxed bg-slate-900/30" x-text="f.answer"></div>
                            </div>
                        </template>
                        <div x-show="faqs.filter(faq => faq.category_id === activeCategory).length === 0" class="p-8 text-center text-slate-500 italic">
                            Belum ada tanya jawab dalam kategori ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editor Inline Q&A Form -->
            <div x-show="editingFaqId !== null" class="space-y-4">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-850">
                    <h3 class="font-bold text-white text-sm" x-text="faqMode === 'create' ? 'Buat FAQ Baru' : 'Edit FAQ'"></h3>
                    <button @click="editingFaqId = null" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Pertanyaan</label>
                    <input type="text" x-model="faqQuestion" placeholder="Masukkan pertanyaan..." class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-brandPrimary text-xs font-bold">
                </div>

                <div>
                    <label class="block text-[10px] text-slate-500 uppercase font-semibold mb-1.5">Jawaban Lengkap</label>
                    <textarea x-model="faqAnswer" rows="6" placeholder="Tuliskan jawaban yang rinci dan solutif..." class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 focus:outline-none focus:border-brandPrimary text-xs leading-relaxed"></textarea>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button @click="editingFaqId = null" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-400 rounded-lg text-xs font-semibold transition-all">Batal</button>
                    <button @click="submitFaq()" class="px-4 py-2 bg-brandPrimary hover:bg-indigo-650 text-white rounded-lg text-xs font-bold transition-all shadow-md">Simpan Q&A</button>
                </div>
            </div>

        </div>

    </div>

</div>
