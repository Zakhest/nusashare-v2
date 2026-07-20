<?php
/**
 * Admin — Creator Management Section
 * Fungsional: AJAX real-data dari alpha-admin/api/creators
 * UI: Bootstrap + AdminLTE (konsisten dengan user_management.php)
 */
?>
<div x-data="creatorManagementApp()" x-init="init()">

    <!-- ══════════════════════════════════════
         STAT SUMMARY BAR
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(99,102,241,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:var(--ns-primary);" x-text="metaAll.total ?? '—'"></div>
                <div class="text-muted" style="font-size:11px;">Total Kreator</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(245,158,11,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#D97706;" x-text="metaPending.total ?? '—'"></div>
                <div class="text-muted" style="font-size:11px;">Pending Approval</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(16,185,129,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#059669;" x-text="metaActive.total ?? '—'"></div>
                <div class="text-muted" style="font-size:11px;">Kreator Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(239,68,68,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#DC2626;" x-text="metaSuspended.total ?? '—'"></div>
                <div class="text-muted" style="font-size:11px;">Dibekukan / Banned</div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         FILTER & SEARCH BAR
    ═══════════════════════════════════════ -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">

                <!-- Search -->
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                               id="creator-search-input"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari username, email, atau nama kreator..."
                               x-model="searchQuery"
                               @input.debounce.400ms="fetchCreators(1)"
                               style="font-size:13px;">
                        <button class="btn btn-outline-secondary" type="button"
                                x-show="searchQuery" @click="searchQuery=''; fetchCreators(1)"
                                title="Hapus pencarian">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <!-- Tab Filter Pills -->
                <div class="col-12 col-md-5 d-flex flex-wrap gap-1 align-items-center">
                    <span class="text-muted me-1" style="font-size:12px; font-weight:600;">Filter:</span>
                    <template x-for="f in tabFilters" :key="f.value">
                        <button class="btn btn-sm"
                                :class="tabFilter === f.value
                                    ? 'btn-' + f.color
                                    : 'btn-outline-' + f.color + ' text-muted'"
                                @click="tabFilter = f.value; fetchCreators(1)"
                                style="font-size:11px; border-radius:20px; padding:2px 12px;"
                                x-text="f.label">
                        </button>
                    </template>
                </div>

                <!-- Per page + Refresh -->
                <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                    <select class="form-select form-select-sm" x-model="perPage" @change="fetchCreators(1)"
                            style="font-size:12px; width:80px;">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" @click="fetchCreators(currentPage)"
                            title="Refresh data" :disabled="loading">
                        <i class="bi bi-arrow-clockwise" :class="loading ? 'spin-icon' : ''"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         DATA TABLE
    ═══════════════════════════════════════ -->
    <div class="card">
        <div class="card-body p-0">

            <!-- Loading Overlay -->
            <template x-if="loading">
                <div class="text-center py-5">
                    <div class="spinner-border spinner-border-sm me-2" style="color:var(--ns-primary);"></div>
                    <span class="text-muted" style="font-size:13px;">Memuat data kreator...</span>
                </div>
            </template>

            <!-- Table -->
            <div x-show="!loading" class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="creators-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:40px;">#</th>
                            <th>Kreator</th>
                            <th>Email</th>
                            <th class="text-center">StarSoul</th>
                            <th class="text-center">Karya</th>
                            <th class="text-center">Pengikut</th>
                            <th class="text-center">Status Akun</th>
                            <th class="text-center">Status Kreator</th>
                            <th class="text-center">Bergabung</th>
                            <th class="text-center pe-3" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="creators.length === 0 && !loading">
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-badge" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                    Tidak ada data kreator yang ditemukan.
                                </td>
                            </tr>
                        </template>

                        <template x-for="(creator, idx) in creators" :key="creator.id">
                            <tr :class="String(creator.is_active) === '-1' ? 'table-danger bg-opacity-10' : ''">

                                <!-- No -->
                                <td class="ps-3 text-muted" style="font-size:12px;"
                                    x-text="(currentPage - 1) * perPage + idx + 1"></td>

                                <!-- Kreator -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2 fw-bold position-relative"
                                             style="width:38px; height:38px; font-size:12px; color:#fff;"
                                             :style="'background:' + avatarColor(creator.username)">
                                            <span x-text="creator.username.substring(0,2).toUpperCase()"></span>
                                            <!-- Verified badge if active -->
                                            <span x-show="String(creator.is_active) === '1' && creator.starsoul_status !== 'probation'"
                                                  class="position-absolute"
                                                  style="bottom:-3px; right:-3px; width:14px; height:14px; background:#10B981; border-radius:50%; border:2px solid #fff; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-check" style="font-size:8px; color:#fff; line-height:1;"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:13px;" x-text="creator.username"></div>
                                            <div class="text-muted" style="font-size:10px;"
                                                 x-text="creator.display_name ? creator.display_name : 'Belum ada nama tampilan'"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="text-muted" style="font-size:12px;" x-text="creator.email"></td>

                                <!-- StarSoul Score -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2"
                                         style="background:rgba(99,102,241,0.08); font-size:11px; font-weight:700;">
                                        <i class="bi bi-stars" style="color:var(--ns-primary);"></i>
                                        <span x-text="Number(creator.starsoul_value || 0).toFixed(1)" style="color:var(--ns-primary);"></span>
                                    </div>
                                </td>

                                <!-- Karya -->
                                <td class="text-center fw-semibold" style="font-size:13px;" x-text="creator.works_count ?? 0"></td>

                                <!-- Followers -->
                                <td class="text-center" style="font-size:12px;">
                                    <span x-text="Number(creator.follower_count ?? 0).toLocaleString('id-ID')"></span>
                                </td>

                                <!-- Status Akun -->
                                <td class="text-center">
                                    <span class="badge rounded-pill"
                                          :class="{
                                              'badge-active':       String(creator.is_active) === '1',
                                              'badge-warning-soft': String(creator.is_active) === '0',
                                              'badge-danger-soft':  String(creator.is_active) === '-1',
                                          }">
                                        <span x-text="accountStatusLabel(creator.is_active)"></span>
                                    </span>
                                </td>

                                <!-- Status Kreator (starsoul_status) -->
                                <td class="text-center">
                                    <span class="badge rounded-pill"
                                          :class="{
                                              'badge-active':       creator.starsoul_status === 'normal',
                                              'badge-warning-soft': creator.starsoul_status === 'warning',
                                              'badge-danger-soft':  creator.starsoul_status === 'probation',
                                          }">
                                        <span x-text="creatorStatusLabel(creator.starsoul_status)"></span>
                                    </span>
                                </td>

                                <!-- Bergabung -->
                                <td class="text-center text-muted" style="font-size:11px;"
                                    x-text="formatDate(creator.created_at)"></td>

                                <!-- Actions -->
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Detail -->
                                        <button class="btn btn-sm btn-outline-secondary btn-icon-xs"
                                                @click="openDetail(creator)"
                                                title="Lihat Detail Kreator">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Quick Actions Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon-xs btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="padding:0 6px;" title="Aksi">
                                                <i class="bi bi-three-dots-vertical" style="font-size:11px;"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:12px; min-width:190px; border-radius:10px;">

                                                <!-- Approve (jika pending) -->
                                                <li x-show="creator.starsoul_status === 'probation' && String(creator.is_active) === '1'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                            @click="quickApprove(creator)">
                                                        <i class="bi bi-patch-check-fill"></i> Approve Kreator
                                                    </button>
                                                </li>

                                                <!-- Aktifkan akun -->
                                                <li x-show="String(creator.is_active) !== '1'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                            @click="quickStatus(creator, 'active')">
                                                        <i class="bi bi-check-circle-fill"></i> Aktifkan Akun
                                                    </button>
                                                </li>

                                                <!-- Set Warning -->
                                                <li x-show="creator.starsoul_status !== 'warning' && String(creator.is_active) === '1'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                            @click="quickStarsoul(creator, 'warning')">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Beri Peringatan
                                                    </button>
                                                </li>

                                                <!-- Bekukan (probation) -->
                                                <li x-show="creator.starsoul_status !== 'probation' && String(creator.is_active) === '1'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                            @click="openSuspendConfirm(creator)">
                                                        <i class="bi bi-pause-circle-fill"></i> Bekukan Kreator
                                                    </button>
                                                </li>

                                                <li><hr class="dropdown-divider"></li>

                                                <!-- Suspend Akun -->
                                                <li x-show="String(creator.is_active) !== '0'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                            @click="quickStatus(creator, 'suspended')">
                                                        <i class="bi bi-slash-circle"></i> Suspend Akun
                                                    </button>
                                                </li>

                                                <!-- Ban -->
                                                <li x-show="String(creator.is_active) !== '-1'">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                            @click="quickStatus(creator, 'banned')">
                                                        <i class="bi bi-slash-circle-fill"></i> Ban Permanen
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="d-flex flex-wrap align-items-center justify-content-between px-3 py-2 border-top" x-show="!loading && meta.total > 0">
                <div class="text-muted" style="font-size:12px;">
                    Menampilkan
                    <strong x-text="(currentPage - 1) * perPage + 1"></strong>–<strong x-text="Math.min(currentPage * perPage, meta.total)"></strong>
                    dari <strong x-text="meta.total"></strong> kreator
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <li class="page-item" :class="currentPage === 1 ? 'disabled' : ''">
                            <button class="page-link" style="border-radius:6px;" @click="fetchCreators(currentPage - 1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </li>
                        <template x-for="p in visiblePages" :key="p">
                            <li class="page-item" :class="p === currentPage ? 'active' : ''">
                                <button class="page-link" style="border-radius:6px;
                                               background: p === currentPage ? 'var(--ns-primary)' : '';
                                               border-color: p === currentPage ? 'var(--ns-primary)' : '';"
                                        @click="typeof p === 'number' ? fetchCreators(p) : null"
                                        :disabled="typeof p !== 'number'"
                                        x-text="p">
                                </button>
                            </li>
                        </template>
                        <li class="page-item" :class="currentPage === meta.pages ? 'disabled' : ''">
                            <button class="page-link" style="border-radius:6px;" @click="fetchCreators(currentPage + 1)">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════
         TOAST NOTIFICATION
    ═══════════════════════════════════════ -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
        <div x-cloak x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="toast show align-items-center border-0 shadow"
             :class="toast.type === 'success' ? 'text-bg-success' : 'text-bg-danger'"
             role="alert" style="min-width:280px; border-radius:12px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i :class="toast.type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill'"></i>
                    <span x-text="toast.message" style="font-size:13px;"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="toast.show = false"></button>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         MODAL: CREATOR DETAIL
    ═══════════════════════════════════════ -->
    <div class="modal fade" id="creatorDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none;">

                <!-- Header gradient -->
                <div style="background: linear-gradient(135deg, var(--ns-primary) 0%, var(--ns-accent) 100%); padding:20px 24px; position:relative;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 fw-bold"
                             style="width:52px; height:52px; font-size:18px; color:#fff; background:rgba(255,255,255,0.20);"
                             x-text="selectedCreator ? selectedCreator.username.substring(0,2).toUpperCase() : ''"></div>
                        <div>
                            <h5 class="mb-0 text-white fw-bold" x-text="selectedCreator ? '@' + selectedCreator.username : ''"></h5>
                            <small class="text-white opacity-75" x-text="selectedCreator ? (selectedCreator.display_name || 'Tidak ada nama tampilan') : ''"></small>
                        </div>
                        <div class="ms-auto d-flex flex-column align-items-end gap-1">
                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1"
                                  style="background:rgba(255,255,255,0.20); color:#fff; font-size:11px;">
                                <i class="bi bi-stars"></i>
                                <span x-text="selectedCreator ? Number(selectedCreator.starsoul_value || 0).toFixed(1) + ' StarSoul' : ''"></span>
                            </span>
                            <span class="badge rounded-pill"
                                  style="background:rgba(255,255,255,0.15); color:#fff; font-size:10px;"
                                  x-text="selectedCreator ? creatorStatusLabel(selectedCreator.starsoul_status) : ''"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <!-- Loading spinner -->
                    <div x-show="detailLoading" class="text-center py-4">
                        <div class="spinner-border" style="color:var(--ns-primary);"></div>
                        <p class="mt-2 text-muted" style="font-size:13px;">Memuat detail kreator...</p>
                    </div>

                    <!-- Detail Content -->
                    <div x-show="!detailLoading && selectedCreator">

                        <!-- Stats Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Status Akun</div>
                                    <div class="mt-1">
                                        <span class="badge rounded-pill"
                                              :class="{
                                                  'badge-active':       String(selectedCreator?.is_active) === '1',
                                                  'badge-warning-soft': String(selectedCreator?.is_active) === '0',
                                                  'badge-danger-soft':  String(selectedCreator?.is_active) === '-1',
                                              }"
                                              x-text="accountStatusLabel(selectedCreator?.is_active)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Saldo CC</div>
                                    <div class="mt-1 fw-bold" style="font-size:1.1rem; color:var(--ns-primary);"
                                         x-text="Number(selectedCreator?.cc_balance ?? 0).toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Total Karya</div>
                                    <div class="mt-1 fw-bold" style="font-size:1.1rem; color:#111827;"
                                         x-text="selectedCreator?.stats?.works_count ?? '—'"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Total Views</div>
                                    <div class="mt-1 fw-bold" style="font-size:1.1rem; color:#111827;"
                                         x-text="Number(selectedCreator?.stats?.total_views ?? 0).toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Extra stats row -->
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <i class="bi bi-book-half" style="color:var(--ns-accent); font-size:18px;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size:14px;" x-text="selectedCreator?.stats?.chapters_count ?? 0"></div>
                                        <div class="text-muted" style="font-size:10px;">Total Bab</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <i class="bi bi-people-fill" style="color:#10B981; font-size:18px;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size:14px;" x-text="Number(selectedCreator?.stats?.follower_count ?? 0).toLocaleString('id-ID')"></div>
                                        <div class="text-muted" style="font-size:10px;">Pengikut</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <i class="bi bi-coin" style="color:#F59E0B; font-size:18px;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size:14px;" x-text="Number(selectedCreator?.stats?.cc_earned ?? 0).toLocaleString('id-ID') + ' CC'"></div>
                                        <div class="text-muted" style="font-size:10px;">CC Diperoleh</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div style="font-size:11px; color:#6B7280; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">Email</div>
                                <div style="font-size:13px;" x-text="selectedCreator?.email"></div>
                            </div>
                            <div class="col-md-6">
                                <div style="font-size:11px; color:#6B7280; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">Bergabung</div>
                                <div style="font-size:13px;" x-text="selectedCreator ? formatDate(selectedCreator.created_at) : ''"></div>
                            </div>
                        </div>

                        <template x-if="selectedCreator && selectedCreator.bio">
                            <div class="mb-3">
                                <div style="font-size:11px; color:#6B7280; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;">Bio</div>
                                <p class="mb-0 p-3 rounded-3" style="font-size:13px; background:#F8F9FB; border:1px solid #E5E7EB; font-style:italic;"
                                   x-text="selectedCreator.bio"></p>
                            </div>
                        </template>

                        <!-- StarSoul Metrics -->
                        <div class="row g-2 mb-4">
                            <div class="col-12">
                                <div style="font-size:11px; color:#6B7280; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px;">Metrik StarSoul</div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div style="font-size:10px; color:#6B7280;">Engagement</div>
                                    <div class="fw-bold" style="color:var(--ns-primary);" x-text="selectedCreator?.engagement ?? 0"></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div style="font-size:10px; color:#6B7280;">Commitment</div>
                                    <div class="fw-bold" style="color:var(--ns-accent);" x-text="selectedCreator?.commitment ?? 0"></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded-3" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div style="font-size:10px; color:#6B7280;">Behavior</div>
                                    <div class="fw-bold" style="color:#10B981;" x-text="Number(selectedCreator?.behavior ?? 0).toFixed(2)"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-3">
                            <!-- Approve jika pending -->
                            <button x-show="selectedCreator?.starsoul_status === 'probation' && String(selectedCreator?.is_active) === '1'"
                                    @click="quickApprove(selectedCreator); _detailModal.hide();"
                                    class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                <i class="bi bi-patch-check-fill"></i> Approve Kreator
                            </button>
                            <!-- Bekukan jika aktif -->
                            <button x-show="selectedCreator?.starsoul_status !== 'probation' && String(selectedCreator?.is_active) === '1'"
                                    @click="_detailModal.hide(); $nextTick(() => openSuspendConfirm(selectedCreator));"
                                    class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                                <i class="bi bi-pause-circle-fill"></i> Bekukan Kreator
                            </button>
                            <!-- Aktifkan jika suspended/banned -->
                            <button x-show="String(selectedCreator?.is_active) !== '1'"
                                    @click="quickStatus(selectedCreator, 'active'); _detailModal.hide();"
                                    class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> Aktifkan Akun
                            </button>
                            <!-- Ban -->
                            <button x-show="String(selectedCreator?.is_active) !== '-1'"
                                    @click="quickStatus(selectedCreator, 'banned'); _detailModal.hide();"
                                    class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                <i class="bi bi-slash-circle-fill"></i> Ban Permanen
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         MODAL: KONFIRMASI BEKUKAN KREATOR
    ═══════════════════════════════════════ -->
    <div class="modal fade" id="suspendCreatorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content" style="border-radius:16px; border:none;">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
                         style="width:56px; height:56px; background:rgba(245,158,11,0.10);">
                        <i class="bi bi-pause-circle-fill" style="font-size:28px; color:#D97706;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Bekukan Kreator?</h5>
                    <p class="text-muted mb-3" style="font-size:13px;">
                        Kreator <strong x-text="'@' + (suspendTarget?.username ?? '')"></strong> akan dibekukan
                        sementara (probation). Mereka tidak dapat mempublikasikan karya baru.
                    </p>
                    <div class="mb-3 text-start">
                        <label class="form-label" style="font-size:12px; font-weight:600;">Alasan Pembekuan</label>
                        <textarea class="form-control" x-model="suspendReason" rows="2"
                                  placeholder="Opsional: tuliskan alasan pembekuan..."
                                  style="font-size:13px; border-radius:8px;"></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Batal</button>
                        <button class="btn btn-warning d-flex align-items-center gap-1" @click="confirmSuspend()"
                                :disabled="actionLoading" style="border-radius:8px;">
                            <span x-show="actionLoading" class="spinner-border spinner-border-sm"></span>
                            <i class="bi bi-pause-circle-fill" x-show="!actionLoading"></i>
                            Bekukan Kreator
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     CSS (Scoped untuk halaman ini)
═══════════════════════════════════════ -->
<style>
    /* Spin animation untuk refresh icon */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spin-icon { animation: spin 0.8s linear infinite; display: inline-block; }

    /* Subtle row hover di tabel kreator */
    #creators-table tbody tr { transition: background 0.12s; }

    /* Badge verified kecil di avatar */
    #creators-table .badge-verified-dot {
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #10B981;
        border: 2px solid #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>

<!-- ══════════════════════════════════════
     ALPINE.JS APP — Creator Management
═══════════════════════════════════════ -->
<script>
function creatorManagementApp() {
    return {
        // ── State ──
        creators:     [],
        loading:      false,
        actionLoading: false,
        searchQuery:  '',
        tabFilter:    '',
        currentPage:  1,
        perPage:      15,

        // Meta pagination (current result)
        meta:          { total: 0, pages: 1 },
        // Meta untuk stat cards (selalu fresh)
        metaAll:       { total: '...' },
        metaPending:   { total: '...' },
        metaActive:    { total: '...' },
        metaSuspended: { total: '...' },

        // Bootstrap modal instances (backdrop:false agar tidak ghosting)
        _detailModal:  null,
        _suspendModal: null,

        // Tab filter options
        tabFilters: [
            { label: 'Semua',    value: '',          color: 'secondary' },
            { label: 'Pending',  value: 'pending',   color: 'warning'   },
            { label: 'Aktif',    value: 'active',    color: 'success'   },
            { label: 'Dibekukan',value: 'suspended', color: 'danger'    },
        ],

        // Toast
        toast: { show: false, message: '', type: 'success', _timer: null },

        // Selected creator & modals
        selectedCreator: null,
        detailLoading:   false,
        suspendTarget:   null,
        suspendReason:   '',

        // ── Init ──
        init() {
            this._detailModal  = new bootstrap.Modal(document.getElementById('creatorDetailModal'),  { backdrop: false });
            this._suspendModal = new bootstrap.Modal(document.getElementById('suspendCreatorModal'), { backdrop: false });
            this.fetchCreators(1);
            this.fetchStatCounts();
        },

        // ── Fetch stat counts untuk stat cards ──
        async fetchStatCounts() {
            const tabs = ['', 'pending', 'active', 'suspended'];
            const keys = ['metaAll', 'metaPending', 'metaActive', 'metaSuspended'];
            for (let i = 0; i < tabs.length; i++) {
                try {
                    const r = await fetch(`<?= base_url('alpha-admin/api/creators') ?>?tab=${tabs[i]}&per_page=1&page=1`);
                    const d = await r.json();
                    if (d.success) this[keys[i]] = { total: d.meta.total };
                } catch(e) {
                    this[keys[i]] = { total: '?' };
                }
            }
        },

        // ── Fetch creators list ──
        async fetchCreators(page = 1) {
            this.loading = true;
            this.currentPage = page;
            try {
                const params = new URLSearchParams({
                    search:   this.searchQuery,
                    tab:      this.tabFilter,
                    page:     page,
                    per_page: this.perPage,
                });
                const r = await fetch(`<?= base_url('alpha-admin/api/creators') ?>?${params}`);
                const d = await r.json();
                if (d.success) {
                    this.creators = d.data;
                    this.meta     = d.meta;
                } else {
                    this.showToast('Gagal memuat data kreator.', 'error');
                }
            } catch(e) {
                this.showToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ── Open Detail Modal ──
        async openDetail(creator) {
            this.selectedCreator = creator;
            this.detailLoading   = true;
            this._detailModal.show();
            try {
                const r = await fetch(`<?= base_url('alpha-admin/api/creators') ?>/${creator.id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const d = await r.json();
                if (d.success) {
                    this.selectedCreator = d.data;
                }
            } catch(e) {
                // Gunakan data yang sudah ada jika gagal fetch detail
            } finally {
                this.detailLoading = false;
            }
        },

        // ── Quick Actions ──

        async quickStatus(creator, status) {
            this.actionLoading = true;
            try {
                const r = await fetch(`<?= base_url('alpha-admin/api/creators') ?>/${creator.id}/status`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({ status }),
                });
                const d = await r.json();
                if (d.success) {
                    const labels = { active: 'diaktifkan', suspended: 'disuspend', banned: 'dibanned' };
                    this.showToast(`Kreator @${creator.username} berhasil ${labels[status]}.`, 'success');
                    this.fetchCreators(this.currentPage);
                    this.fetchStatCounts();
                } else {
                    this.showToast(d.message || 'Gagal mengubah status.', 'error');
                }
            } catch(e) {
                this.showToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.actionLoading = false;
            }
        },

        async quickStarsoul(creator, starsoulStatus) {
            this.actionLoading = true;
            try {
                const r = await fetch(`<?= base_url('alpha-admin/api/creators') ?>/${creator.id}/starsoul-status`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({ starsoul_status: starsoulStatus }),
                });
                const d = await r.json();
                if (d.success) {
                    const labels = { normal: 'di-approve', warning: 'diberi peringatan', probation: 'dibekukan' };
                    this.showToast(`Kreator @${creator.username} berhasil ${labels[starsoulStatus]}.`, 'success');
                    this.fetchCreators(this.currentPage);
                    this.fetchStatCounts();
                } else {
                    this.showToast(d.message || 'Gagal mengubah status.', 'error');
                }
            } catch(e) {
                this.showToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.actionLoading = false;
            }
        },

        // Approve kreator (ubah dari probation → normal)
        async quickApprove(creator) {
            await this.quickStarsoul(creator, 'normal');
        },

        // Suspend Modal
        openSuspendConfirm(creator) {
            this.suspendTarget = creator;
            this.suspendReason = '';
            this._suspendModal.show();
        },

        async confirmSuspend() {
            if (!this.suspendTarget) return;
            await this.quickStarsoul(this.suspendTarget, 'probation');
            this._suspendModal.hide();
            this.suspendTarget = null;
        },

        // ── Pagination ──
        get visiblePages() {
            const total = this.meta.pages || 1;
            const cur   = this.currentPage;
            if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = [1];
            if (cur > 3) pages.push('...');
            for (let p = Math.max(2, cur - 1); p <= Math.min(total - 1, cur + 1); p++) pages.push(p);
            if (cur < total - 2) pages.push('...');
            pages.push(total);
            return pages;
        },

        // ── Count helpers (untuk stat cards dalam tabel) ──
        countByStatus(isActive) {
            return this.creators.filter(c => String(c.is_active) === String(isActive)).length;
        },

        // ── Label helpers ──
        accountStatusLabel(isActive) {
            const map = { '1': 'Aktif', '0': 'Suspended', '-1': 'Banned' };
            return map[String(isActive)] ?? 'Unknown';
        },

        creatorStatusLabel(starsoulStatus) {
            const map = {
                'normal':    'Terverifikasi',
                'warning':   'Peringatan',
                'probation': 'Probation',
            };
            return map[starsoulStatus] ?? starsoulStatus ?? '—';
        },

        // ── Utilities ──
        avatarColor(username) {
            const palette = [
                '#6366F1','#8B5CF6','#EC4899','#14B8A6','#F59E0B',
                '#3B82F6','#10B981','#EF4444','#06B6D4','#F97316',
            ];
            let hash = 0;
            for (let i = 0; i < (username || '').length; i++) {
                hash = username.charCodeAt(i) + ((hash << 5) - hash);
            }
            return palette[Math.abs(hash) % palette.length];
        },

        formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        showToast(message, type = 'success', duration = 3500) {
            clearTimeout(this.toast._timer);
            this.toast = { show: true, message, type, _timer: null };
            this.toast._timer = setTimeout(() => { this.toast.show = false; }, duration);
        },
    };
}
</script>
