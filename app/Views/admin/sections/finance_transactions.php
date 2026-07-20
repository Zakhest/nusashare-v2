<?php
/**
 * Admin — Finance Transactions Section
 * Fungsional: AJAX real-data dari alpha-admin/api/transactions
 * Fitur: Modal detail transaksi + Download PDF individual
 */
?>
<div
    x-data="transactionManager()"
    x-init="init()"
    class="ns-transactions-root"
>

    <!-- ══════════════════════════════════════════
         HEADER ROW — Title + Export Button
    ══════════════════════════════════════════ -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-0" style="color:#111827; font-size:1rem;">
                <i class="bi bi-arrow-left-right me-2" style="color:var(--ns-primary);"></i>
                Riwayat Transaksi CC
            </h5>
            <p class="text-muted mb-0" style="font-size:11px;">
                Klik baris transaksi untuk melihat detail &amp; mencetak bukti transaksi PDF.
            </p>
        </div>
        <button
            @click="exportCSV()"
            :disabled="loading || rows.length === 0"
            class="btn btn-sm d-flex align-items-center gap-2"
            style="background:var(--ns-primary); color:#fff; border:none; border-radius:9px; font-size:12px; font-weight:600; padding:8px 16px;"
        >
            <i class="bi bi-download"></i>
            Export CSV
        </button>
    </div>

    <!-- ══════════════════════════════════════════
         STATS CARDS
    ══════════════════════════════════════════ -->
    <div class="row g-3 mb-4">

        <!-- Total Transaksi -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px; background:linear-gradient(135deg,#6366F1 0%,#818CF8 100%); color:#fff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:11px; font-weight:600; opacity:.85;">Total Transaksi</span>
                        <div style="width:32px;height:32px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-receipt" style="font-size:15px;"></i>
                        </div>
                    </div>
                    <div style="font-size:1.6rem;font-weight:800;" x-text="statsLoading ? '...' : stats.total_count.toLocaleString('id-ID')">—</div>
                    <div style="font-size:10px; opacity:.75; margin-top:2px;">Semua transaksi CC tercatat</div>
                </div>
            </div>
        </div>

        <!-- Total CC Masuk -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px; background:#fff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:11px; font-weight:700; color:#6B7280;">CC Masuk</span>
                        <div style="width:32px;height:32px;border-radius:10px;background:rgba(16,185,129,.10);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-arrow-down-circle-fill" style="font-size:15px;color:#10B981;"></i>
                        </div>
                    </div>
                    <div style="font-size:1.45rem;font-weight:800;color:#10B981;" x-text="statsLoading ? '...' : formatCC(stats.total_in)">—</div>
                    <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Top-up &amp; reward</div>
                </div>
            </div>
        </div>

        <!-- Total CC Keluar -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px; background:#fff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:11px; font-weight:700; color:#6B7280;">CC Dibelanjakan</span>
                        <div style="width:32px;height:32px;border-radius:10px;background:rgba(239,68,68,.10);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-arrow-up-circle-fill" style="font-size:15px;color:#EF4444;"></i>
                        </div>
                    </div>
                    <div style="font-size:1.45rem;font-weight:800;color:#EF4444;" x-text="statsLoading ? '...' : formatCC(stats.total_out)">—</div>
                    <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Unlock, download, support</div>
                </div>
            </div>
        </div>

        <!-- Total Topup Volume -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px; background:#fff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:11px; font-weight:700; color:#6B7280;">Volume Top-Up</span>
                        <div style="width:32px;height:32px;border-radius:10px;background:rgba(6,182,212,.10);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-wallet2" style="font-size:15px;color:var(--ns-accent);"></i>
                        </div>
                    </div>
                    <div style="font-size:1.45rem;font-weight:800;color:var(--ns-accent);" x-text="statsLoading ? '...' : formatCC(stats.total_topup)">—</div>
                    <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Total CC dari top-up</div>
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════════════════════════════════════
         FILTER BAR
    ══════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">

                <!-- Search -->
                <div class="col-12 col-md-4">
                    <label style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;" class="mb-1">Cari User / Deskripsi</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0" style="border-radius:9px 0 0 9px; color:#9CA3AF;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-start-0"
                            style="border-radius:0 9px 9px 0; font-size:12px;"
                            placeholder="username atau deskripsi..."
                            x-model="filters.search"
                            @keyup.enter="applyFilters()"
                            @input.debounce.500ms="applyFilters()"
                        >
                    </div>
                </div>

                <!-- Type filter -->
                <div class="col-6 col-md-2">
                    <label style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;" class="mb-1">Tipe</label>
                    <select class="form-select form-select-sm" style="border-radius:9px; font-size:12px;" x-model="filters.type" @change="applyFilters()">
                        <option value="">Semua Tipe</option>
                        <option value="in">CC Masuk (in)</option>
                        <option value="out">CC Keluar (out)</option>
                    </select>
                </div>

                <!-- Category filter -->
                <div class="col-6 col-md-2">
                    <label style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;" class="mb-1">Kategori</label>
                    <select class="form-select form-select-sm" style="border-radius:9px; font-size:12px;" x-model="filters.category" @change="applyFilters()">
                        <option value="">Semua</option>
                        <option value="topup">Top-Up</option>
                        <option value="unlock_chapter">Unlock Bab</option>
                        <option value="unlock_work">Unlock Karya</option>
                        <option value="unlock">Unlock</option>
                        <option value="admin_adjustment">Adj. Admin</option>
                        <option value="reward">Reward</option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="col-6 col-md-2">
                    <label style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;" class="mb-1">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" style="border-radius:9px; font-size:12px;" x-model="filters.date_from" @change="applyFilters()">
                </div>

                <!-- Date To -->
                <div class="col-6 col-md-2">
                    <label style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;" class="mb-1">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" style="border-radius:9px; font-size:12px;" x-model="filters.date_to" @change="applyFilters()">
                </div>

                <!-- Reset Button -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                    <button @click="resetFilters()" class="btn btn-sm btn-outline-secondary" style="font-size:11px; border-radius:8px;">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </button>
                    <button @click="applyFilters()" class="btn btn-sm" style="background:var(--ns-primary);color:#fff;border:none;border-radius:8px;font-size:11px;font-weight:600;">
                        <i class="bi bi-funnel me-1"></i> Terapkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-header d-flex align-items-center justify-content-between border-0" style="background:#fff; padding:1rem 1.25rem;">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:12px; font-weight:700; color:#111827;">Log Transaksi</span>
                <span x-show="!loading" class="badge" style="background:rgba(99,102,241,.1);color:var(--ns-primary);font-size:10px;font-weight:700;border-radius:99px;" x-text="meta.total.toLocaleString('id-ID') + ' entri'"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:11px;color:#9CA3AF;">Tampilkan:</span>
                <select class="form-select form-select-sm" style="width:70px;border-radius:8px;font-size:11px;" x-model.number="filters.per_page" @change="applyFilters()">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary" @click="fetchData()" :disabled="loading" title="Refresh" style="border-radius:8px; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-arrow-clockwise" :class="loading ? 'spin-trx' : ''" style="font-size:13px;"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                <thead>
                    <tr style="background:#F9FAFB; border-bottom:2px solid #F3F4F6;">
                        <th class="px-4 py-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;white-space:nowrap;">ID Transaksi</th>
                        <th class="px-3 py-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;">User</th>
                        <th class="px-3 py-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;">Tipe</th>
                        <th class="px-3 py-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;">Kategori</th>
                        <th class="px-3 py-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;max-width:220px;">Deskripsi</th>
                        <th class="px-3 py-3 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;white-space:nowrap;">Jumlah CC</th>
                        <th class="px-4 py-3 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;white-space:nowrap;">Tanggal</th>
                        <th class="px-3 py-3 text-center" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;white-space:nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Loading Skeleton -->
                    <template x-if="loading">
                        <template x-for="i in 8" :key="i">
                            <tr>
                                <td class="px-4 py-3" colspan="8">
                                    <div style="height:14px;background:#F3F4F6;border-radius:6px;animation:skeleton-pulse 1.4s ease-in-out infinite;" :style="'width:' + (40 + (i % 5) * 12) + '%'"></div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <!-- Data Rows -->
                    <template x-if="!loading">
                        <template x-for="row in rows" :key="row.id">
                            <tr
                                @click="openDetail(row)"
                                style="cursor:pointer; transition:background .12s;"
                                title="Klik untuk lihat detail"
                            >
                                <!-- ID -->
                                <td class="px-4 py-3">
                                    <code style="font-size:10px;color:#6B7280;background:#F3F4F6;padding:2px 6px;border-radius:5px;" x-text="'#' + row.id"></code>
                                </td>
                                <!-- User -->
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,var(--ns-primary),var(--ns-accent));display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0;"
                                             x-text="(row.display_name || row.username || '?').charAt(0).toUpperCase()">
                                        </div>
                                        <div>
                                            <div style="font-weight:700;color:#111827;font-size:12px;" x-text="'@' + (row.username || '—')"></div>
                                            <div style="font-size:10px;color:#9CA3AF;" x-text="row.display_name && row.display_name !== row.username ? row.display_name : ''"></div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Type badge -->
                                <td class="px-3 py-3">
                                    <span
                                        style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:700;"
                                        :style="row.type === 'in'
                                            ? 'background:rgba(16,185,129,.10);color:#059669;border:1px solid rgba(16,185,129,.2);'
                                            : 'background:rgba(239,68,68,.10);color:#DC2626;border:1px solid rgba(239,68,68,.2);'"
                                    >
                                        <i :class="row.type === 'in' ? 'bi bi-arrow-down-short' : 'bi bi-arrow-up-short'" style="font-size:12px;"></i>
                                        <span x-text="row.type === 'in' ? 'Masuk' : 'Keluar'"></span>
                                    </span>
                                </td>
                                <!-- Category -->
                                <td class="px-3 py-3">
                                    <span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:#F3F4F6;color:#374151;text-transform:uppercase;letter-spacing:.04em;"
                                          x-text="formatCategory(row.category)">
                                    </span>
                                </td>
                                <!-- Description -->
                                <td class="px-3 py-3" style="max-width:220px;">
                                    <span
                                        style="color:#6B7280;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:200px;"
                                        x-text="row.description || '—'"
                                        :title="row.description"
                                    ></span>
                                </td>
                                <!-- Amount -->
                                <td class="px-3 py-3 text-end">
                                    <span
                                        style="font-weight:800;font-size:13px;"
                                        :style="row.type === 'in' ? 'color:#10B981;' : 'color:#EF4444;'"
                                        x-text="(row.type === 'in' ? '+' : '-') + Number(row.amount).toLocaleString('id-ID') + ' CC'"
                                    ></span>
                                </td>
                                <!-- Date -->
                                <td class="px-4 py-3 text-end">
                                    <span style="color:#9CA3AF;font-size:11px;white-space:nowrap;" x-text="formatDate(row.created_at)"></span>
                                </td>
                                <!-- Action -->
                                <td class="px-3 py-3 text-center" @click.stop>
                                    <button
                                        @click="openDetail(row)"
                                        class="btn btn-sm btn-outline-secondary"
                                        style="border-radius:7px; font-size:11px; padding:3px 10px;"
                                        title="Lihat detail"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && rows.length === 0">
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px 0;">
                                    <div style="width:56px;height:56px;border-radius:16px;background:#F3F4F6;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-inbox" style="font-size:24px;color:#D1D5DB;"></i>
                                    </div>
                                    <p style="font-size:13px;font-weight:600;color:#6B7280;margin:0;">Tidak ada transaksi ditemukan</p>
                                    <p style="font-size:11px;color:#9CA3AF;margin:0;">Coba ubah filter atau kata pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div x-show="!loading && meta.pages > 1" class="card-footer bg-white border-top d-flex align-items-center justify-content-between px-4 py-3" style="border-radius:0 0 16px 16px;">
            <span style="font-size:11px;color:#6B7280;">
                Halaman <span x-text="meta.page"></span> dari <span x-text="meta.pages"></span>
                &nbsp;·&nbsp;
                <span x-text="meta.total.toLocaleString('id-ID')"></span> entri
            </span>
            <div class="d-flex align-items-center gap-1">
                <button @click="goToPage(1)" :disabled="meta.page === 1" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:11px;padding:3px 8px;"><i class="bi bi-chevron-double-left"></i></button>
                <button @click="goToPage(meta.page - 1)" :disabled="meta.page === 1" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:11px;padding:3px 8px;"><i class="bi bi-chevron-left"></i></button>
                <template x-for="p in pageNumbers" :key="p">
                    <button
                        @click="p !== '...' && goToPage(p)"
                        :disabled="p === '...'"
                        class="btn btn-sm"
                        :style="p === meta.page
                            ? 'background:var(--ns-primary);color:#fff;border:none;border-radius:7px;font-size:11px;padding:3px 10px;font-weight:700;'
                            : 'border-radius:7px;font-size:11px;padding:3px 10px;'"
                        :class="p === meta.page ? '' : 'btn-outline-secondary'"
                        x-text="p"
                    ></button>
                </template>
                <button @click="goToPage(meta.page + 1)" :disabled="meta.page === meta.pages" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:11px;padding:3px 8px;"><i class="bi bi-chevron-right"></i></button>
                <button @click="goToPage(meta.pages)" :disabled="meta.page === meta.pages" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:11px;padding:3px 8px;"><i class="bi bi-chevron-double-right"></i></button>
            </div>
        </div>

    </div>

    <!-- Toast Notification -->
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;min-width:260px;"
        x-cloak
    >
        <div class="d-flex align-items-center gap-3 px-4 py-3 shadow-lg"
             style="border-radius:14px;font-size:12px;font-weight:600;"
             :style="toast.type === 'success'
                ? 'background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#059669;'
                : 'background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#DC2626;'"
        >
            <i :class="toast.type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-circle-fill'" style="font-size:16px;"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     MODAL: DETAIL TRANSAKSI
═══════════════════════════════════════ -->
<div class="modal fade" id="trxDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,.15);">

            <!-- Gradient Header -->
            <div id="trx-modal-header" style="position:relative; padding:22px 28px 20px;">
                <div class="d-flex align-items-center gap-3">
                    <!-- Icon circle -->
                    <div id="trx-modal-icon" style="width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.20);flex-shrink:0;">
                        <i id="trx-modal-icon-i" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white" style="font-size:10px;opacity:.75;font-weight:600;text-transform:uppercase;letter-spacing:.08em;">ID Transaksi</p>
                        <h5 class="mb-0 text-white fw-bold font-monospace" id="trx-modal-id" style="font-size:1.05rem;letter-spacing:.02em;"></h5>
                    </div>
                    <div class="ms-auto text-end">
                        <div id="trx-modal-amount" style="font-size:1.6rem;font-weight:800;color:#fff;line-height:1;"></div>
                        <span id="trx-modal-type-badge" style="font-size:10px;font-weight:700;padding:2px 10px;border-radius:99px;background:rgba(255,255,255,.20);color:#fff;"></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <!-- Info Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                            <div class="text-muted mb-1" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Kategori</div>
                            <div id="trx-modal-category" style="font-size:13px;font-weight:700;color:#374151;"></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                            <div class="text-muted mb-1" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Jumlah CC</div>
                            <div id="trx-modal-cc" style="font-size:13px;font-weight:800;"></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                            <div class="text-muted mb-1" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Tipe Aliran</div>
                            <div id="trx-modal-flow" style="font-size:13px;font-weight:700;"></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                            <div class="text-muted mb-1" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Tanggal</div>
                            <div id="trx-modal-date" style="font-size:11px;font-weight:600;color:#374151;"></div>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="p-3 rounded-3 mb-3 d-flex align-items-center gap-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                    <div id="trx-modal-avatar" style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366F1,#06B6D4);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;"></div>
                    <div>
                        <div style="font-size:10px;color:#9CA3AF;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">User Pemilik Transaksi</div>
                        <div id="trx-modal-username" style="font-size:14px;font-weight:700;color:#111827;"></div>
                        <div id="trx-modal-displayname" style="font-size:11px;color:#6B7280;"></div>
                    </div>
                    <div class="ms-auto">
                        <span id="trx-modal-role-badge" style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;background:rgba(99,102,241,.1);color:#6366F1;"></span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <div style="font-size:10px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Deskripsi / Keterangan</div>
                    <div id="trx-modal-desc" class="p-3 rounded-3" style="background:#F8F9FB;border:1px solid #E5E7EB;font-size:13px;color:#374151;font-style:italic;min-height:42px;"></div>
                </div>

                <!-- Receipt-style summary box -->
                <div id="trx-receipt-box" class="p-4 rounded-3 mb-4" style="background:linear-gradient(135deg,rgba(99,102,241,.04),rgba(6,182,212,.04));border:1.5px dashed rgba(99,102,241,.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:11px;color:#6B7280;font-weight:600;">Platform</span>
                        <span style="font-size:11px;font-weight:700;color:#374151;">NusaShare</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:11px;color:#6B7280;font-weight:600;">Ref. ID</span>
                        <code id="rcpt-id" style="font-size:11px;color:#6366F1;"></code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:11px;color:#6B7280;font-weight:600;">Waktu</span>
                        <span id="rcpt-date" style="font-size:11px;font-weight:600;color:#374151;"></span>
                    </div>
                    <hr style="border-color:rgba(99,102,241,.2);margin:10px 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:13px;font-weight:700;color:#374151;">Total CC</span>
                        <span id="rcpt-amount" style="font-size:1.2rem;font-weight:800;"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-3">
                    <button
                        type="button"
                        onclick="downloadTrxPDF()"
                        class="btn btn-sm d-flex align-items-center gap-2"
                        style="background:var(--ns-primary);color:#fff;border:none;border-radius:9px;font-size:12px;font-weight:600;padding:8px 18px;"
                    >
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        Download PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:9px;font-size:12px;">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     HIDDEN PRINT TEMPLATE (for PDF)
═══════════════════════════════════════ -->
<div id="trx-print-area" style="display:none;">
    <div id="trx-print-content"></div>
</div>

<!-- Skeleton + spin styles -->
<style>
@keyframes skeleton-pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.45; }
}
@keyframes spin-trx-anim { to { transform: rotate(360deg); } }
.spin-trx { animation: spin-trx-anim 0.8s linear infinite; display: inline-block; }

/* Clickable row hover */
.ns-transactions-root tbody tr[style*="cursor:pointer"]:hover td {
    background: rgba(99,102,241,.04) !important;
}

/* PDF Print Styles */
@media print {
    body > * { display: none !important; }
    #trx-print-area { display: block !important; }
    #trx-print-area * { display: block; }

    .pdf-receipt {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        max-width: 600px;
        margin: 0 auto;
        padding: 40px;
    }
    .pdf-logo-bar {
        display: flex !important;
        align-items: center;
        gap: 12px;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid #6366F1;
    }
    .pdf-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0; }
    .pdf-subtitle { font-size: 12px; color: #6B7280; margin: 0; }
    .pdf-badge {
        display: inline-flex !important;
        align-items: center;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }
    .pdf-row {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #F3F4F6;
    }
    .pdf-row:last-child { border-bottom: none; }
    .pdf-label { font-size: 12px; color: #6B7280; font-weight: 600; }
    .pdf-value { font-size: 13px; color: #111827; font-weight: 700; }
    .pdf-total-row {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: #F8F9FB;
        border-radius: 10px;
        margin-top: 12px;
    }
    .pdf-total-label { font-size: 14px; font-weight: 700; color: #374151; }
    .pdf-total-amount { font-size: 24px; font-weight: 800; }
    .pdf-footer {
        margin-top: 36px;
        text-align: center;
        font-size: 10px;
        color: #9CA3AF;
    }
}
</style>

<script>
// ═══════════════════════════════════════════════
//  ALPINE.JS — Transaction Manager
// ═══════════════════════════════════════════════
function transactionManager() {
    return {
        loading:     true,
        statsLoading: true,
        rows:        [],
        stats: { total_count: 0, total_in: 0, total_out: 0, total_topup: 0, total_unlock: 0, categories: [] },
        meta: { total: 0, pages: 1, page: 1, per_page: 20 },
        filters: { search: '', type: '', category: '', date_from: '', date_to: '', per_page: 20 },
        toast: { show: false, type: 'success', message: '' },

        // ── Computed ──
        get pageNumbers() {
            const { page, pages } = this.meta;
            if (pages <= 7) return Array.from({ length: pages }, (_, i) => i + 1);
            const nums = [];
            nums.push(1);
            if (page > 3) nums.push('...');
            for (let i = Math.max(2, page - 1); i <= Math.min(pages - 1, page + 1); i++) nums.push(i);
            if (page < pages - 2) nums.push('...');
            nums.push(pages);
            return nums;
        },

        async init() {
            await Promise.all([this.fetchStats(), this.fetchData()]);
        },

        async fetchStats() {
            this.statsLoading = true;
            try {
                const res  = await fetch('<?= base_url('alpha-admin/api/transactions/stats') ?>');
                const json = await res.json();
                if (json.success) this.stats = json.data;
            } catch (e) { console.error('Stats fetch error:', e); }
            this.statsLoading = false;
        },

        async fetchData() {
            this.loading = true;
            const p = new URLSearchParams({
                search: this.filters.search, type: this.filters.type,
                category: this.filters.category, date_from: this.filters.date_from,
                date_to: this.filters.date_to, page: this.meta.page,
                per_page: this.filters.per_page,
            });
            try {
                const res  = await fetch('<?= base_url('alpha-admin/api/transactions') ?>?' + p.toString());
                const json = await res.json();
                if (json.success) { this.rows = json.data; this.meta = { ...this.meta, ...json.meta }; }
            } catch (e) { this.showToast('error', 'Gagal memuat data transaksi.'); }
            this.loading = false;
        },

        applyFilters() { this.meta.page = 1; this.fetchData(); },
        resetFilters()  { this.filters = { search: '', type: '', category: '', date_from: '', date_to: '', per_page: 20 }; this.meta.page = 1; this.fetchData(); },
        goToPage(p)     { if (p < 1 || p > this.meta.pages || p === this.meta.page) return; this.meta.page = p; this.fetchData(); window.scrollTo({ top: 0, behavior: 'smooth' }); },

        openDetail(row) {
            openTrxModal(row);
        },

        exportCSV() {
            const headers = ['ID', 'Username', 'Display Name', 'Role', 'Tipe', 'Kategori', 'Deskripsi', 'Jumlah CC', 'Tanggal'];
            const csvRows = [headers.join(',')];
            for (const r of this.rows) {
                csvRows.push([
                    r.id, '@' + (r.username || ''),
                    '"' + (r.display_name || '').replace(/"/g, '""') + '"',
                    r.role || '',
                    r.type, r.category,
                    '"' + (r.description || '').replace(/"/g, '""') + '"',
                    (r.type === 'in' ? '+' : '-') + r.amount,
                    r.created_at,
                ].join(','));
            }
            const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href = url; a.download = 'transaksi-nusashare-' + new Date().toISOString().slice(0, 10) + '.csv';
            a.click(); URL.revokeObjectURL(url);
            this.showToast('success', 'CSV berhasil diunduh!');
        },

        formatCC(n)         { return Number(n || 0).toLocaleString('id-ID') + ' CC'; },
        formatCategory(cat) {
            const map = { topup:'Top-Up', unlock_chapter:'Unlock Bab', unlock_work:'Unlock Karya',
                          unlock:'Unlock', admin_adjustment:'Adj. Admin', reward:'Reward', cashout:'Cashout', refund:'Refund' };
            return map[cat] || (cat || '—');
        },
        formatDate(dt) {
            if (!dt) return '—';
            const d = new Date(dt);
            return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
                + ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        },
        showToast(type, message) {
            this.toast = { show: true, type, message };
            setTimeout(() => this.toast.show = false, 3500);
        },
    };
}

// ═══════════════════════════════════════════════
//  MODAL DETAIL — Plain JS (outside Alpine scope)
// ═══════════════════════════════════════════════
let _trxDetailModal = null;
let _currentTrxRow  = null;

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('trxDetailModal');
    if (el) _trxDetailModal = new bootstrap.Modal(el, { backdrop: true });
});

function openTrxModal(row) {
    _currentTrxRow = row;

    const isIn    = row.type === 'in';
    const color   = isIn ? '#10B981' : '#EF4444';
    const iconCls = isIn ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
    const catMap  = { topup:'Top-Up', unlock_chapter:'Unlock Bab', unlock_work:'Unlock Karya',
                      unlock:'Unlock', admin_adjustment:'Adj. Admin', reward:'Reward', cashout:'Cashout', refund:'Refund' };
    const catLabel = catMap[row.category] || (row.category || '—');

    const amountFmt = Number(row.amount).toLocaleString('id-ID') + ' CC';
    const signedFmt = (isIn ? '+' : '−') + amountFmt;
    const dateFmt   = formatTrxDate(row.created_at);
    const initial   = (row.display_name || row.username || '?').charAt(0).toUpperCase();

    // Header gradient
    const header = document.getElementById('trx-modal-header');
    header.style.background = isIn
        ? 'linear-gradient(135deg, #059669 0%, #10B981 100%)'
        : 'linear-gradient(135deg, #DC2626 0%, #EF4444 100%)';

    // Icon
    document.getElementById('trx-modal-icon-i').className = 'bi ' + iconCls;

    // ID
    document.getElementById('trx-modal-id').textContent = '#' + row.id;

    // Amount
    document.getElementById('trx-modal-amount').textContent = signedFmt;

    // Type badge
    document.getElementById('trx-modal-type-badge').textContent = isIn ? '↓ CC Masuk' : '↑ CC Keluar';

    // Info grid
    document.getElementById('trx-modal-category').textContent = catLabel;
    const ccEl = document.getElementById('trx-modal-cc');
    ccEl.textContent = signedFmt;
    ccEl.style.color = color;
    const flowEl = document.getElementById('trx-modal-flow');
    flowEl.textContent = isIn ? 'Kredit (Masuk)' : 'Debit (Keluar)';
    flowEl.style.color = color;
    document.getElementById('trx-modal-date').textContent = dateFmt;

    // User
    document.getElementById('trx-modal-avatar').textContent = initial;
    document.getElementById('trx-modal-username').textContent = '@' + (row.username || '—');
    const dn = document.getElementById('trx-modal-displayname');
    dn.textContent = row.display_name && row.display_name !== row.username ? row.display_name : '';
    const roleEl = document.getElementById('trx-modal-role-badge');
    roleEl.textContent = row.role ? row.role.charAt(0).toUpperCase() + row.role.slice(1) : '—';

    // Description
    document.getElementById('trx-modal-desc').textContent = row.description || 'Tidak ada keterangan.';

    // Receipt box
    document.getElementById('rcpt-id').textContent   = '#' + row.id;
    document.getElementById('rcpt-date').textContent = dateFmt;
    const rcptAmt = document.getElementById('rcpt-amount');
    rcptAmt.textContent = signedFmt;
    rcptAmt.style.color = color;

    _trxDetailModal && _trxDetailModal.show();
}

function formatTrxDate(dt) {
    if (!dt) return '—';
    const d = new Date(dt);
    return d.toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric' })
        + ' · ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' }) + ' WIB';
}

// ═══════════════════════════════════════════════
//  DOWNLOAD PDF — Browser Print API
// ═══════════════════════════════════════════════
function downloadTrxPDF() {
    if (!_currentTrxRow) return;
    const row     = _currentTrxRow;
    const isIn    = row.type === 'in';
    const color   = isIn ? '#059669' : '#DC2626';
    const catMap  = { topup:'Top-Up', unlock_chapter:'Unlock Bab', unlock_work:'Unlock Karya',
                      unlock:'Unlock', admin_adjustment:'Adj. Admin', reward:'Reward', cashout:'Cashout', refund:'Refund' };
    const catLabel    = catMap[row.category] || (row.category || '—');
    const amountFmt   = Number(row.amount).toLocaleString('id-ID') + ' CC';
    const signedFmt   = (isIn ? '+' : '−') + amountFmt;
    const dateFmt     = formatTrxDate(row.created_at);
    const printedAt   = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' })
                       + ' ' + new Date().toLocaleTimeString('id-ID');

    const html = `
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Bukti Transaksi #${row.id} — NusaShare</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Segoe UI', Inter, sans-serif;
                background: #fff;
                color: #111827;
            }
            .receipt {
                max-width: 580px;
                margin: 0 auto;
                padding: 40px 36px;
            }
            /* ── Header ── */
            .receipt-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 20px;
                border-bottom: 2.5px solid ${color};
                margin-bottom: 24px;
            }
            .brand-name {
                font-size: 20px;
                font-weight: 800;
                color: ${color};
                letter-spacing: -.03em;
            }
            .brand-sub {
                font-size: 10px;
                color: #9CA3AF;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .08em;
            }
            .receipt-badge {
                display: inline-block;
                padding: 5px 16px;
                background: ${isIn ? 'rgba(5,150,105,.10)' : 'rgba(220,38,38,.10)'};
                color: ${color};
                border-radius: 99px;
                font-size: 11px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .05em;
                border: 1.5px solid ${isIn ? 'rgba(5,150,105,.25)' : 'rgba(220,38,38,.25)'};
            }

            /* ── Amount hero ── */
            .amount-hero {
                text-align: center;
                padding: 28px 0 24px;
                border-bottom: 1px solid #F3F4F6;
                margin-bottom: 24px;
            }
            .amount-label {
                font-size: 11px;
                color: #9CA3AF;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .08em;
                margin-bottom: 6px;
            }
            .amount-value {
                font-size: 44px;
                font-weight: 800;
                color: ${color};
                letter-spacing: -.03em;
                line-height: 1;
            }
            .amount-sub {
                font-size: 12px;
                color: #9CA3AF;
                margin-top: 6px;
            }

            /* ── Info table ── */
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 24px;
            }
            .info-table tr td {
                padding: 10px 0;
                border-bottom: 1px solid #F3F4F6;
                vertical-align: top;
            }
            .info-table tr:last-child td { border-bottom: none; }
            .info-label {
                font-size: 11px;
                color: #9CA3AF;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .05em;
                width: 38%;
            }
            .info-value {
                font-size: 13px;
                color: #111827;
                font-weight: 600;
                text-align: right;
            }

            /* ── User block ── */
            .user-block {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 16px;
                background: #F8F9FB;
                border-radius: 12px;
                margin-bottom: 24px;
                border: 1px solid #E5E7EB;
            }
            .user-avatar {
                width: 44px; height: 44px;
                border-radius: 12px;
                background: linear-gradient(135deg, #6366F1, #06B6D4);
                display: flex; align-items: center; justify-content: center;
                font-size: 18px; font-weight: 800; color: #fff;
            }
            .user-name  { font-size: 15px; font-weight: 800; color: #111827; }
            .user-role  { font-size: 11px; color: #9CA3AF; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }

            /* ── Description ── */
            .desc-block {
                padding: 14px;
                background: #F8F9FB;
                border-radius: 10px;
                font-size: 13px;
                color: #374151;
                font-style: italic;
                border: 1px solid #E5E7EB;
                margin-bottom: 28px;
                line-height: 1.55;
            }

            /* ── Dashed total box ── */
            .total-box {
                border: 2px dashed ${color};
                border-radius: 12px;
                padding: 18px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 32px;
            }
            .total-label-text { font-size: 13px; font-weight: 700; color: #374151; }
            .total-amount-text { font-size: 24px; font-weight: 800; color: ${color}; }

            /* ── Footer ── */
            .receipt-footer {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #F3F4F6;
            }
            .footer-note { font-size: 10px; color: #9CA3AF; line-height: 1.6; }
            .footer-brand { font-size: 12px; font-weight: 700; color: #6366F1; margin-top: 6px; }
            .watermark {
                position: fixed;
                bottom: 20px;
                right: 20px;
                font-size: 9px;
                color: #D1D5DB;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .1em;
            }
        </style>
    </head>
    <body>
        <div class="receipt">
            <!-- Header -->
            <div class="receipt-header">
                <div>
                    <div class="brand-name">NusaShare</div>
                    <div class="brand-sub">Bukti Transaksi CC</div>
                </div>
                <div class="receipt-badge">${isIn ? '↓ CC Masuk' : '↑ CC Keluar'}</div>
            </div>

            <!-- Amount Hero -->
            <div class="amount-hero">
                <div class="amount-label">Total Koin CC</div>
                <div class="amount-value">${signedFmt}</div>
                <div class="amount-sub">Kategori: ${catLabel}</div>
            </div>

            <!-- User Block -->
            <div class="user-block">
                <div class="user-avatar">${(row.display_name || row.username || '?').charAt(0).toUpperCase()}</div>
                <div>
                    <div class="user-name">@${row.username || '—'}</div>
                    <div class="user-role">${row.role || '—'} ${row.display_name && row.display_name !== row.username ? '· ' + row.display_name : ''}</div>
                </div>
            </div>

            <!-- Info Table -->
            <table class="info-table">
                <tr>
                    <td class="info-label">ID Transaksi</td>
                    <td class="info-value" style="font-family:monospace;color:#6366F1;">#${row.id}</td>
                </tr>
                <tr>
                    <td class="info-label">Tipe Aliran</td>
                    <td class="info-value" style="color:${color};">${isIn ? 'Kredit — CC Masuk' : 'Debit — CC Keluar'}</td>
                </tr>
                <tr>
                    <td class="info-label">Kategori</td>
                    <td class="info-value">${catLabel}</td>
                </tr>
                <tr>
                    <td class="info-label">Tanggal & Waktu</td>
                    <td class="info-value">${dateFmt}</td>
                </tr>
                <tr>
                    <td class="info-label">Dicetak Pada</td>
                    <td class="info-value" style="color:#9CA3AF;">${printedAt}</td>
                </tr>
            </table>

            <!-- Description -->
            <div class="desc-block">
                ${row.description || 'Tidak ada keterangan tambahan.'}
            </div>

            <!-- Total -->
            <div class="total-box">
                <div class="total-label-text">Total CC</div>
                <div class="total-amount-text">${signedFmt}</div>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="footer-note">
                    Dokumen ini adalah bukti resmi pergerakan koin CC pada platform NusaShare.<br>
                    Mohon simpan dokumen ini sebagai referensi transaksi Anda.
                </div>
                <div class="footer-brand">NusaShare · Platform Karya Kreatif Indonesia</div>
            </div>
        </div>

        <div class="watermark">Dicetak oleh Alpha Admin Panel</div>

        <script>
            window.onload = () => {
                window.print();
                setTimeout(() => window.close(), 800);
            };
        <\/script>
    </body>
    </html>`;

    const win = window.open('', '_blank', 'width=680,height=900,toolbar=0,menubar=0,location=0');
    if (win) {
        win.document.open();
        win.document.write(html);
        win.document.close();
    } else {
        alert('Popup diblokir browser. Izinkan popup untuk fitur ini.');
    }
}
</script>
