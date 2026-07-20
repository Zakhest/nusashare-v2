<div x-data="userManagementApp()" x-init="init()">

    <!-- ══════════════════════════════════════
         STAT SUMMARY BAR
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(99,102,241,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:var(--ns-primary);" x-text="meta.total ?? '—'"></div>
                <div class="text-muted" style="font-size:11px;">Total User</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(16,185,129,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#059669;" x-text="countByStatus('1')"></div>
                <div class="text-muted" style="font-size:11px;">Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(245,158,11,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#D97706;" x-text="countByStatus('0')"></div>
                <div class="text-muted" style="font-size:11px;">Suspended</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 px-2 border-0" style="background:rgba(239,68,68,0.06); border-radius:12px;">
                <div class="fw-800 mb-0" style="font-size:1.6rem; color:#DC2626;" x-text="countByStatus('-1')"></div>
                <div class="text-muted" style="font-size:11px;">Banned</div>
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
                               id="user-search-input"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari username, email, atau nama..."
                               x-model="searchQuery"
                               @input.debounce.400ms="fetchUsers(1)"
                               style="font-size:13px;">
                        <button class="btn btn-outline-secondary" type="button"
                                x-show="searchQuery" @click="searchQuery=''; fetchUsers(1)"
                                title="Hapus pencarian">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <!-- Status Filter Pills -->
                <div class="col-12 col-md-5 d-flex flex-wrap gap-1 align-items-center">
                    <span class="text-muted me-1" style="font-size:12px; font-weight:600;">Filter:</span>
                    <template x-for="f in filters" :key="f.value">
                        <button class="btn btn-sm"
                                :class="statusFilter === f.value
                                    ? 'btn-' + f.color
                                    : 'btn-outline-' + f.color + ' text-muted'"
                                @click="statusFilter = f.value; fetchUsers(1)"
                                style="font-size:11px; border-radius:20px; padding:2px 12px;"
                                x-text="f.label">
                        </button>
                    </template>
                </div>

                <!-- Per page + Refresh -->
                <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                    <select class="form-select form-select-sm" x-model="perPage" @change="fetchUsers(1)"
                            style="font-size:12px; width:80px;">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" @click="fetchUsers(currentPage)"
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

            <!-- Loading Overlay — pakai x-if bukan x-show karena d-flex Bootstrap punya display:flex !important -->
            <template x-if="loading">
                <div class="text-center py-5">
                    <div class="spinner-border spinner-border-sm me-2" style="color:var(--ns-primary);"></div>
                    <span class="text-muted" style="font-size:13px;">Memuat data...</span>
                </div>
            </template>

            <!-- Table -->
            <div x-show="!loading" class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="users-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:40px;">#</th>
                            <th>Pengguna</th>
                            <th>Email</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Saldo CC</th>
                            <th class="text-center">Tgl Bergabung</th>
                            <th class="text-center pe-3" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="users.length === 0 && !loading">
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                    Tidak ada data user yang ditemukan.
                                </td>
                            </tr>
                        </template>

                        <template x-for="(user, idx) in users" :key="user.id">
                            <tr class="user-row" :class="user.is_active == -1 ? 'table-danger bg-opacity-10' : ''">

                                <!-- No -->
                                <td class="ps-3 text-muted" style="font-size:12px;"
                                    x-text="(currentPage - 1) * perPage + idx + 1"></td>

                                <!-- User -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2 fw-bold"
                                             style="width:34px; height:34px; font-size:12px; color:#fff;"
                                             :style="'background:' + avatarColor(user.username)">
                                            <span x-text="user.username.substring(0,2).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:13px;" x-text="user.username"></div>
                                            <div class="text-muted" style="font-size:10px;"
                                                 x-text="user.display_name ? user.display_name : 'Belum ada nama tampilan'"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="text-muted" style="font-size:12px;" x-text="user.email"></td>

                                <!-- Role -->
                                <td class="text-center">
                                    <span class="badge rounded-pill"
                                          :class="user.role === 'creator' ? 'badge-ns-primary' : 'badge-muted-soft'"
                                          x-text="user.role === 'creator' ? 'Kreator' : 'User'"></span>
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    <span class="badge rounded-pill"
                                          :class="{
                                              'badge-active':       String(user.is_active) === '1',
                                              'badge-warning-soft': String(user.is_active) === '0',
                                              'badge-danger-soft':  String(user.is_active) === '-1',
                                          }">
                                        <span x-text="statusLabel(user.is_active)"></span>
                                    </span>
                                </td>

                                <!-- CC Balance -->
                                <td class="text-end fw-bold" style="font-size:13px; color:var(--ns-primary);"
                                    x-text="Number(user.cc_balance).toLocaleString('id-ID') + ' CC'"></td>

                                <!-- Joined Date -->
                                <td class="text-center text-muted" style="font-size:11px;"
                                    x-text="formatDate(user.created_at)"></td>

                                <!-- Actions -->
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Detail -->
                                        <button class="btn btn-sm btn-outline-secondary btn-icon-xs"
                                                @click="openDetail(user)"
                                                title="Lihat Detail" data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Adjust CC -->
                                        <button class="btn btn-sm btn-icon-xs"
                                                style="background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.25); color:var(--ns-primary);"
                                                @click="openAdjustCC(user)"
                                                title="Sesuaikan Saldo CC">
                                            <i class="bi bi-currency-exchange"></i>
                                        </button>
                                        <!-- Quick Status Toggle -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon-xs btn-outline-secondary dropdown-toggle dropdown-toggle-nosplit"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="padding:0 6px;" title="Ubah Status">
                                                <i class="bi bi-three-dots-vertical" style="font-size:11px;"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:12px; min-width:160px; border-radius:10px;">
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                            @click="quickStatus(user, 'active')"
                                                            x-show="String(user.is_active) !== '1'">
                                                        <i class="bi bi-check-circle-fill"></i> Aktifkan Akun
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                            @click="quickStatus(user, 'suspended')"
                                                            x-show="String(user.is_active) !== '0'">
                                                        <i class="bi bi-pause-circle-fill"></i> Suspend Akun
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                            @click="quickStatus(user, 'banned')"
                                                            x-show="String(user.is_active) !== '-1'">
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
                    dari <strong x-text="meta.total"></strong> pengguna
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <li class="page-item" :class="currentPage === 1 ? 'disabled' : ''">
                            <button class="page-link" style="border-radius:6px;" @click="fetchUsers(currentPage - 1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </li>
                        <template x-for="p in visiblePages" :key="p">
                            <li class="page-item" :class="p === currentPage ? 'active' : ''">
                                <button class="page-link" style="border-radius:6px;
                                               background: p === currentPage ? 'var(--ns-primary)' : '';
                                               border-color: p === currentPage ? 'var(--ns-primary)' : '';"
                                        @click="typeof p === 'number' ? fetchUsers(p) : null"
                                        :disabled="typeof p !== 'number'"
                                        x-text="p">
                                </button>
                            </li>
                        </template>
                        <li class="page-item" :class="currentPage === meta.pages ? 'disabled' : ''">
                            <button class="page-link" style="border-radius:6px;" @click="fetchUsers(currentPage + 1)">
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
         MODAL: USER DETAIL
    ═══════════════════════════════════════ -->
    <div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none;">

                <!-- Header gradient -->
                <div style="background: linear-gradient(135deg, var(--ns-primary) 0%, var(--ns-accent) 100%); padding:20px 24px; position:relative;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 fw-bold"
                             style="width:48px; height:48px; font-size:16px; color:#fff; background:rgba(255,255,255,0.20);"
                             x-text="selectedUser ? selectedUser.username.substring(0,2).toUpperCase() : ''"></div>
                        <div>
                            <h5 class="mb-0 text-white fw-bold" x-text="selectedUser ? '@' + selectedUser.username : ''"></h5>
                            <small class="text-white opacity-75" x-text="selectedUser ? (selectedUser.display_name || 'Tidak ada nama tampilan') : ''"></small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge rounded-pill"
                                  style="background:rgba(255,255,255,0.20); color:#fff; font-size:11px;"
                                  x-text="selectedUser ? (selectedUser.role === 'creator' ? '⭐ Kreator' : '👤 User') : ''"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <!-- Loading spinner -->
                    <div x-show="detailLoading" class="text-center py-4">
                        <div class="spinner-border" style="color:var(--ns-primary);"></div>
                        <p class="mt-2 text-muted" style="font-size:13px;">Memuat detail user...</p>
                    </div>

                    <!-- Detail Content -->
                    <div x-show="!detailLoading && selectedUser">

                        <!-- Info Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Status</div>
                                    <div class="mt-1">
                                        <span class="badge rounded-pill fs-6"
                                              :class="{
                                                  'badge-active':       String(selectedUser?.is_active) === '1',
                                                  'badge-warning-soft': String(selectedUser?.is_active) === '0',
                                                  'badge-danger-soft':  String(selectedUser?.is_active) === '-1',
                                              }"
                                              x-text="statusLabel(selectedUser?.is_active)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Saldo CC</div>
                                    <div class="fw-bold mt-1" style="color:var(--ns-primary); font-size:15px;"
                                         x-text="Number(selectedUser?.cc_balance || 0).toLocaleString('id-ID') + ' CC'"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Total Transaksi</div>
                                    <div class="fw-bold mt-1" style="font-size:15px;"
                                         x-text="Number(selectedUser?.stats?.tx_count || 0).toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background:#F8F9FB; border:1px solid #E5E7EB;">
                                    <div class="text-muted" style="font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:.05em;">Karya Dibuka</div>
                                    <div class="fw-bold mt-1" style="font-size:15px;"
                                         x-text="Number(selectedUser?.stats?.works_unlocked || 0).toLocaleString('id-ID')"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Info -->
                        <div class="row g-2 mb-4">
                            <div class="col-md-6">
                                <label class="form-label mb-1 text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Email</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control bg-light" readonly :value="selectedUser?.email">
                                    <button class="btn btn-outline-secondary" type="button"
                                            @click="copyText(selectedUser?.email)" title="Salin email">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1 text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">User ID</label>
                                <input type="text" class="form-control form-control-sm bg-light" readonly :value="selectedUser?.id">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1 text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Bergabung</label>
                                <input type="text" class="form-control form-control-sm bg-light" readonly :value="formatDate(selectedUser?.created_at)">
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-4" x-show="selectedUser?.bio">
                            <label class="form-label mb-1 text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Bio</label>
                            <div class="p-3 rounded-3 text-muted" style="background:#F8F9FB; border:1px solid #E5E7EB; font-size:13px;" x-text="selectedUser?.bio"></div>
                        </div>

                        <!-- Moderation Actions -->
                        <div class="border-top pt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0" style="font-size:13px;">
                                    <i class="bi bi-shield-lock-fill me-1" style="color:var(--ns-primary);"></i>
                                    Tindakan Moderasi
                                </h6>
                                <small class="text-muted" style="font-size:11px;">Dicatat dalam Audit Log</small>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm d-flex align-items-center gap-1"
                                        style="background:rgba(16,185,129,0.10); color:#059669; border:1px solid rgba(16,185,129,0.25); border-radius:8px;"
                                        x-show="String(selectedUser?.is_active) !== '1'"
                                        @click="changeStatusFromModal('active')"
                                        :disabled="statusUpdating">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span x-show="!statusUpdating">Aktifkan Akun</span>
                                    <span x-show="statusUpdating" class="spinner-border spinner-border-sm"></span>
                                </button>
                                <button class="btn btn-sm d-flex align-items-center gap-1"
                                        style="background:rgba(245,158,11,0.10); color:#D97706; border:1px solid rgba(245,158,11,0.25); border-radius:8px;"
                                        x-show="String(selectedUser?.is_active) !== '0'"
                                        @click="changeStatusFromModal('suspended')"
                                        :disabled="statusUpdating">
                                    <i class="bi bi-pause-circle-fill"></i> Suspend
                                </button>
                                <button class="btn btn-sm d-flex align-items-center gap-1"
                                        style="background:rgba(239,68,68,0.10); color:#DC2626; border:1px solid rgba(239,68,68,0.25); border-radius:8px;"
                                        x-show="String(selectedUser?.is_active) !== '-1'"
                                        @click="changeStatusFromModal('banned')"
                                        :disabled="statusUpdating">
                                    <i class="bi bi-slash-circle-fill"></i> Ban Permanen
                                </button>
                                <button class="btn btn-sm d-flex align-items-center gap-1 ms-auto"
                                        style="background:rgba(99,102,241,0.08); color:var(--ns-primary); border:1px solid rgba(99,102,241,0.20); border-radius:8px;"
                                        @click="hideDetailOpenCC()">
                                    <i class="bi bi-currency-exchange"></i> Sesuaikan CC
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         MODAL: ADJUST CC BALANCE
    ═══════════════════════════════════════ -->
    <div class="modal fade" id="adjustCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
            <div class="modal-content" style="border-radius:16px; border:none;">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" style="font-size:15px;">
                            <i class="bi bi-currency-exchange me-2" style="color:var(--ns-primary);"></i>
                            Penyesuaian Saldo CC
                        </h5>
                        <small class="text-muted" x-text="adjustCCUser ? 'Target: @' + adjustCCUser.username : ''"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">

                    <!-- User info banner -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4"
                         style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15);">
                        <div class="d-flex align-items-center justify-content-center rounded-2 fw-bold"
                             style="width:40px; height:40px; color:#fff; font-size:13px;"
                             :style="'background:' + avatarColor(adjustCCUser?.username || '')"
                             x-text="adjustCCUser ? adjustCCUser.username.substring(0,2).toUpperCase() : ''"></div>
                        <div>
                            <div class="fw-semibold" style="font-size:13px;" x-text="adjustCCUser?.username"></div>
                            <div class="text-muted" style="font-size:12px;">
                                Saldo saat ini:
                                <strong style="color:var(--ns-primary);" x-text="Number(adjustCCUser?.cc_balance || 0).toLocaleString('id-ID') + ' CC'"></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Type Toggle -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#374151;">Tipe Penyesuaian</label>
                        <div class="d-grid" style="grid-template-columns:1fr 1fr; display:grid; gap:8px;">
                            <button type="button"
                                    class="btn"
                                    :class="adjustCCType === 'add'
                                        ? 'btn-primary'
                                        : 'btn-outline-secondary'"
                                    @click="adjustCCType = 'add'"
                                    style="border-radius:10px; font-size:13px;">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Saldo
                            </button>
                            <button type="button"
                                    class="btn"
                                    :class="adjustCCType === 'deduct'
                                        ? 'btn-danger'
                                        : 'btn-outline-secondary'"
                                    @click="adjustCCType = 'deduct'"
                                    style="border-radius:10px; font-size:13px;">
                                <i class="bi bi-dash-circle me-1"></i> Kurangi Saldo
                            </button>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#374151;">Jumlah CC</label>
                        <div class="input-group">
                            <span class="input-group-text" :class="adjustCCType === 'deduct' ? 'text-danger' : 'text-success'">
                                <i :class="adjustCCType === 'deduct' ? 'bi bi-dash-lg' : 'bi bi-plus-lg'"></i>
                            </span>
                            <input type="number" class="form-control" x-model="adjustCCAmount"
                                   min="1" step="1" placeholder="Contoh: 500"
                                   style="font-size:15px; font-weight:700;">
                            <span class="input-group-text text-muted">CC</span>
                        </div>
                        <!-- Quick amounts -->
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <template x-for="quick in [50, 100, 250, 500, 1000]">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        style="font-size:11px; border-radius:6px;"
                                        @click="adjustCCAmount = quick"
                                        :class="adjustCCAmount == quick ? 'active' : ''"
                                        x-text="quick + ' CC'">
                                </button>
                            </template>
                        </div>
                        <!-- Preview -->
                        <div class="mt-2 text-muted" style="font-size:11px;">
                            Saldo setelah penyesuaian:
                            <strong :style="adjustCCType === 'deduct' ? 'color:#DC2626' : 'color:#059669'"
                                    x-text="computeNewBalance() + ' CC'"></strong>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:12px; color:#374151;">Alasan Penyesuaian</label>
                        <textarea class="form-control" x-model="adjustCCReason" rows="2"
                                  placeholder="Contoh: Kompensasi downtime gateway / Reward konten pilihan"
                                  style="font-size:13px; resize:none;"></textarea>
                    </div>

                </div>

                <div class="modal-footer border-top pt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                            :class="adjustCCType === 'deduct' ? 'btn-danger' : 'btn-primary'"
                            @click="submitCCAdjust()"
                            :disabled="ccAdjusting || adjustCCAmount <= 0"
                            style="min-width:140px;">
                        <span class="spinner-border spinner-border-sm" x-show="ccAdjusting"></span>
                        <span x-show="!ccAdjusting">
                            <i class="bi bi-send-fill me-1"></i>
                            Simpan Perubahan
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     STYLES
═══════════════════════════════════════ -->
<style>
    .user-row { transition: background 0.15s; }
    .spin-icon { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .dropdown-toggle-nosplit::after { display: none; }
    .page-item.active .page-link { background-color: var(--ns-primary); border-color: var(--ns-primary); }
    .page-link { color: var(--ns-primary); }
    .page-link:hover { color: var(--ns-primary-dark); }
    .fw-800 { font-weight: 800; }
</style>

<!-- ══════════════════════════════════════
     ALPINE.JS COMPONENT
═══════════════════════════════════════ -->
<script>
function userManagementApp() {
    return {
        // ── State ──
        users:        [],
        meta:         { total: 0, pages: 1, page: 1 },
        currentPage:  1,
        perPage:      15,
        searchQuery:  '',
        statusFilter: '',
        loading:      false,

        // Modal state
        selectedUser:   null,
        detailLoading:  false,
        statusUpdating: false,
        adjustCCUser:   null,
        adjustCCType:   'add',
        adjustCCAmount: 100,
        adjustCCReason: 'Penyesuaian admin manual',
        ccAdjusting:    false,

        // Toast
        toast: { show: false, message: '', type: 'success', _timer: null },

        // Bootstrap modal instances
        _detailModal:   null,
        _ccModal:       null,

        // Filter buttons config
        filters: [
            { label: 'Semua',     value: '',          color: 'secondary' },
            { label: 'Aktif',     value: 'active',    color: 'success'   },
            { label: 'Suspended', value: 'suspended', color: 'warning'   },
            { label: 'Banned',    value: 'banned',    color: 'danger'    },
            { label: 'Kreator',   value: 'creator',   color: 'primary'   },
        ],

        // Cache status counts per page load
        _statusCache: {},

        // ── Init ──
        init() {
            this._detailModal = new bootstrap.Modal(document.getElementById('userDetailModal'), { backdrop: false });
            this._ccModal     = new bootstrap.Modal(document.getElementById('adjustCCModal'),   { backdrop: false });
            this.fetchUsers(1);
        },

        // ── Fetch user list ──
        async fetchUsers(page = 1) {
            this.loading     = true;
            this.currentPage = page;

            const params = new URLSearchParams({
                search:   this.searchQuery,
                status:   this.statusFilter,
                page:     page,
                per_page: this.perPage,
            });

            try {
                const res  = await fetch(`<?= base_url('alpha-admin/api/users') ?>?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();

                if (json.success) {
                    this.users = json.data;
                    this.meta  = json.meta;
                    this._statusCache = {};
                } else {
                    this.showToast('Gagal memuat data pengguna.', 'error');
                }
            } catch (e) {
                this.showToast('Koneksi gagal: ' + e.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        // ── Open detail modal ──
        async openDetail(user) {
            this.selectedUser  = user;
            this.detailLoading = true;
            this._detailModal.show();

            try {
                const res  = await fetch(`<?= base_url('alpha-admin/api/users') ?>/${user.id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();

                if (json.success) {
                    this.selectedUser = json.data;
                }
            } catch (e) {
                this.showToast('Gagal memuat detail user.', 'error');
            } finally {
                this.detailLoading = false;
            }
        },

        // ── Quick status change from table row ──
        async quickStatus(user, status) {
            const labels = { active: 'mengaktifkan', suspended: 'men-suspend', banned: 'mem-ban' };
            if (!confirm(`Yakin ingin ${labels[status]} akun @${user.username}?`)) return;

            this.loading = true;
            try {
                const res  = await fetch(`<?= base_url('alpha-admin/api/users') ?>/${user.id}/status`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body:    JSON.stringify({ status }),
                });
                const json = await res.json();

                if (json.success) {
                    this.showToast('Status akun berhasil diperbarui.', 'success');
                    await this.fetchUsers(this.currentPage);
                } else {
                    this.showToast(json.message || 'Gagal memperbarui status.', 'error');
                }
            } catch (e) {
                this.showToast('Koneksi gagal.', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ── Status change from inside detail modal ──
        async changeStatusFromModal(status) {
            if (!this.selectedUser) return;
            if (!confirm(`Yakin ingin mengubah status akun @${this.selectedUser.username}?`)) return;

            this.statusUpdating = true;
            try {
                const res  = await fetch(`<?= base_url('alpha-admin/api/users') ?>/${this.selectedUser.id}/status`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body:    JSON.stringify({ status }),
                });
                const json = await res.json();

                if (json.success) {
                    const mapToNum = { active: 1, suspended: 0, banned: -1 };
                    this.selectedUser.is_active = mapToNum[status];
                    // Update table row too
                    const row = this.users.find(u => u.id === this.selectedUser.id);
                    if (row) row.is_active = mapToNum[status];
                    this.showToast('Status berhasil diperbarui.', 'success');
                } else {
                    this.showToast(json.message || 'Gagal memperbarui status.', 'error');
                }
            } catch (e) {
                this.showToast('Koneksi gagal.', 'error');
            } finally {
                this.statusUpdating = false;
            }
        },

        // ── Open CC adjustment modal ──
        openAdjustCC(user) {
            this.adjustCCUser   = user;
            this.adjustCCType   = 'add';
            this.adjustCCAmount = 100;
            this.adjustCCReason = 'Penyesuaian admin manual';
            this._ccModal.show();
        },

        // ── From detail modal → CC modal ──
        hideDetailOpenCC() {
            this._detailModal.hide();
            setTimeout(() => {
                this.adjustCCUser   = this.selectedUser;
                this.adjustCCType   = 'add';
                this.adjustCCAmount = 100;
                this.adjustCCReason = 'Penyesuaian admin manual';
                this._ccModal.show();
            }, 350);
        },

        // ── Submit CC adjustment ──
        async submitCCAdjust() {
            if (!this.adjustCCUser || this.adjustCCAmount <= 0) return;

            this.ccAdjusting = true;
            try {
                const res  = await fetch(`<?= base_url('alpha-admin/api/users') ?>/${this.adjustCCUser.id}/adjust-cc`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body:    JSON.stringify({
                        amount: this.adjustCCAmount,
                        reason: this.adjustCCReason,
                        type:   this.adjustCCType,
                    }),
                });
                const json = await res.json();

                if (json.success) {
                    // Update table
                    const row = this.users.find(u => u.id === this.adjustCCUser.id);
                    if (row) row.cc_balance = json.new_balance;
                    if (this.selectedUser?.id === this.adjustCCUser.id) {
                        this.selectedUser.cc_balance = json.new_balance;
                    }
                    this._ccModal.hide();
                    this.showToast(`Saldo CC @${this.adjustCCUser.username} berhasil diperbarui.`, 'success');
                } else {
                    this.showToast(json.message || 'Gagal menyesuaikan CC.', 'error');
                }
            } catch (e) {
                this.showToast('Koneksi gagal.', 'error');
            } finally {
                this.ccAdjusting = false;
            }
        },

        // ── Compute preview balance ──
        computeNewBalance() {
            const current = parseInt(this.adjustCCUser?.cc_balance || 0);
            const amount  = parseInt(this.adjustCCAmount || 0);
            const result  = this.adjustCCType === 'deduct' ? current - amount : current + amount;
            return Math.max(0, result).toLocaleString('id-ID');
        },

        // ── Count by status (within current page) ──
        countByStatus(statusVal) {
            return this.users.filter(u => String(u.is_active) === statusVal).length;
        },

        // ── Pagination pages list ──
        get visiblePages() {
            const total = this.meta.pages;
            const cur   = this.currentPage;
            if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

            const pages = [];
            if (cur > 3) pages.push(1, '…');
            for (let p = Math.max(1, cur - 2); p <= Math.min(total, cur + 2); p++) pages.push(p);
            if (cur < total - 2) pages.push('…', total);
            return pages;
        },

        // ── Helpers ──
        statusLabel(isActive) {
            const map = { '1': 'Aktif', '0': 'Suspended', '-1': 'Banned' };
            return map[String(isActive)] ?? 'Unknown';
        },

        formatDate(dt) {
            if (!dt) return '—';
            return new Date(dt).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
        },

        avatarColor(username) {
            const colors = [
                '#6366F1','#8B5CF6','#EC4899','#06B6D4',
                '#10B981','#F59E0B','#EF4444','#3B82F6',
            ];
            let hash = 0;
            for (let i = 0; i < username.length; i++) hash = username.charCodeAt(i) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        },

        copyText(text) {
            if (!text) return;
            navigator.clipboard?.writeText(text).then(() => {
                this.showToast('Email disalin ke clipboard.', 'success');
            });
        },

        showToast(message, type = 'success') {
            clearTimeout(this.toast._timer);
            this.toast.show    = false;
            this.$nextTick(() => {
                this.toast.message = message;
                this.toast.type    = type;
                this.toast.show    = true;
                this.toast._timer  = setTimeout(() => { this.toast.show = false; }, 4000);
            });
        },
    };
}
</script>
