/**
 * NusaShare - Main Gallery Script
 * Extracted from show.php
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Lightbox Initialization ---
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    // Watermark text should be provided via global config
    const watermarkText = window.nusaAppData.watermarkText;
    
    lightbox.innerHTML = `
        <div class="lightbox-close">
            <span class="material-symbols-outlined">close</span>
        </div>
        <div class="lightbox-nav" id="lightbox-prev">
            <span class="material-symbols-outlined text-4xl">chevron_left</span>
        </div>
        <div class="lightbox-nav" id="lightbox-next">
            <span class="material-symbols-outlined text-4xl">chevron_right</span>
        </div>
        <div class="lightbox-content">
            <div class="lightbox-img-container">
                <img id="lightbox-img" src="" alt="Zoom" draggable="false">
                <!-- Lightbox Watermark -->
                <div id="lightbox-watermark" class="absolute inset-0 z-20 pointer-events-none flex flex-col justify-between items-center py-12 overflow-hidden watermark-container">
                    <div class="text-white/40 text-4xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-2 border-2 border-white/20 rounded-xl select-none">
                        ${watermarkText}
                    </div>
                    <div class="text-white/40 text-7xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-4 border-4 border-white/20 rounded-2xl select-none scale-125">
                        ${watermarkText}
                    </div>
                    <div class="text-white/40 text-4xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-2 border-2 border-white/20 rounded-xl select-none">
                        ${watermarkText}
                    </div>
                </div>
            </div>
        </div>
        <style>
             img {
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -o-user-select: none;
            user-select: none;
        }
        </style>
    `;
    document.body.appendChild(lightbox);

    // Robust right-click prevention for the entire lightbox
    lightbox.oncontextmenu = (e) => e.preventDefault();

    let currentImageIndex = 0;
    const galleryImages = Array.from(document.querySelectorAll('.gallery-image'));

    // Global toggle function
    window.openLightbox = function(imageId) {
        const img = document.getElementById(`content-img-${imageId}`);
        if (img.classList.contains('blur-xl')) return; // Exit if locked

        currentImageIndex = galleryImages.findIndex(el => parseInt(el.dataset.imgId) === imageId);
        updateLightbox();
        
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightbox() {
        const imgData = galleryImages[currentImageIndex];
        const lbImg = document.getElementById('lightbox-img');
        lbImg.src = imgData.dataset.imgUrl;
        
        // Sync Watermark visibility
        const lbWatermark = document.getElementById('lightbox-watermark');
        if (imgData.classList.contains('blur-xl')) {
            lbWatermark.classList.add('opacity-0');
        } else {
            lbWatermark.classList.remove('opacity-0');
        }

        // --- Preload Next Image for instant feel ---
        const nextIndex = (currentImageIndex + 1) % galleryImages.length;
        const nextImgData = galleryImages[nextIndex];
        if (nextImgData && !nextImgData.classList.contains('blur-xl')) {
            const preload = new Image();
            preload.src = nextImgData.dataset.imgUrl;
        }
    }

    function navigateLightbox(step) {
        currentImageIndex += step;
        if (currentImageIndex < 0) currentImageIndex = galleryImages.length - 1;
        if (currentImageIndex >= galleryImages.length) currentImageIndex = 0;
        
        // Skip locked images when navigating
        if (galleryImages[currentImageIndex].classList.contains('blur-xl')) {
            if (galleryImages.length > 1) {
                navigateLightbox(step);
            } else {
                closeLightbox();
            }
            return;
        }
        
        updateLightbox();
    }

    // Event Listeners for Lightbox
    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    document.getElementById('lightbox-prev').addEventListener('click', (e) => {
        e.stopPropagation();
        navigateLightbox(-1);
    });
    document.getElementById('lightbox-next').addEventListener('click', (e) => {
        e.stopPropagation();
        navigateLightbox(1);
    });
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.classList.contains('lightbox-content')) {
            closeLightbox();
        }
    });

    // Keyboard Navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    // --- Like Functionality ---
    const likeBtn = document.getElementById('likeBtn');
    const likeIcon = document.getElementById('likeIcon');
    const likeCount = document.getElementById('likeCount');
    let isLoading = false;

    if (likeBtn) {
        likeBtn.addEventListener('click', async () => {
            if (isLoading) return;
            
            const workId = likeBtn.dataset.workId;
            isLoading = true;
            likeBtn.classList.add('opacity-50');

            try {
                const response = await fetch(`${window.nusaAppData.baseUrl}works/${workId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.status === 401) {
                    alert(data.message);
                    window.location.href = `${window.nusaAppData.baseUrl}login`;
                    return;
                }

                if (data.status === 'liked') {
                    likeIcon.classList.remove('text-slate-400');
                    likeIcon.classList.add('text-red-500');
                    likeIcon.style.fontVariationSettings = "'FILL' 1";
                    likeCount.classList.remove('text-slate-400');
                    likeCount.classList.add('text-red-500');
                } else {
                    likeIcon.classList.add('text-slate-400');
                    likeIcon.classList.remove('text-red-500');
                    likeIcon.style.fontVariationSettings = "'FILL' 0";
                    likeCount.classList.add('text-slate-400');
                    likeCount.classList.remove('text-red-500');
                }

                likeCount.textContent = data.count.toLocaleString();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                isLoading = false;
                likeBtn.classList.remove('opacity-50');
            }
        });
    }

    // --- Share Functionality ---
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const originalText = shareBtn.querySelector('span:last-child').textContent;
                shareBtn.querySelector('span:last-child').textContent = 'Disalin!';
                shareBtn.querySelector('span:first-child').classList.add('text-indigo-600');
                
                setTimeout(() => {
                    shareBtn.querySelector('span:last-child').textContent = originalText;
                    shareBtn.querySelector('span:first-child').classList.remove('text-indigo-600');
                }, 2000);
            });
        });
    }

    // Initialize protection at the end to include the lightbox
    if (typeof setupWatermarkProtection === 'function') {
        setupWatermarkProtection();
    }
});

// --- Watermark Protection ---
function setupWatermarkProtection() {
    document.body.classList.add('js-enabled');

    const containers = document.querySelectorAll('.watermark-container');
    const backups = new Map();

    containers.forEach((el, index) => {
        const id = el.id || `wm-backup-${index}`;
        el.id = id;
        backups.set(id, {
            html: el.outerHTML,
            parent: el.parentElement
        });
    });

    const observer = new MutationObserver((mutations) => {
        let recoveryNeeded = false;

        mutations.forEach((mutation) => {
            if (mutation.type === 'childList') {
                backups.forEach((data, id) => {
                    if (!document.getElementById(id)) {
                        console.warn(`Watermark ${id} removed. Restoring...`);
                        data.parent.insertAdjacentHTML('beforeend', data.html);
                        recoveryNeeded = true;
                    }
                });

                if (mutation.target.classList && mutation.target.classList.contains('watermark-container')) {
                    if (mutation.removedNodes.length > 0) {
                        const id = mutation.target.id;
                        console.warn(`Inner watermark element removed from ${id}. Restoring...`);
                        mutation.target.outerHTML = backups.get(id).html;
                        recoveryNeeded = true;
                    }
                }
            }

            if (mutation.type === 'attributes') {
                const target = mutation.target;
                const isWatermarkPart = target.classList && (
                    target.classList.contains('watermark-container') || 
                    target.parentElement.classList.contains('watermark-container')
                );

                if (isWatermarkPart) {
                    const container = target.classList.contains('watermark-container') ? target : target.parentElement;
                    const parentImg = container.parentElement.querySelector('img');
                    const isUnlocked = parentImg && !parentImg.classList.contains('blur-xl');

                    if (isUnlocked) {
                        const style = window.getComputedStyle(target);
                        if (target.classList.contains('opacity-0') || 
                            style.display === 'none' || 
                            style.visibility === 'hidden' || 
                            style.opacity === '0') {
                            
                            console.warn('Tampering detected (attribute). Reverting...');
                            if (!target.classList.contains('watermark-container')) {
                                container.outerHTML = backups.get(container.id).html;
                                recoveryNeeded = true;
                            } else {
                                target.classList.remove('opacity-0');
                                target.style.setProperty('display', 'flex', 'important');
                                target.style.setProperty('visibility', 'visible', 'important');
                                target.style.setProperty('opacity', '1', 'important');
                            }
                        }
                    }
                }
            }
        });

        if (recoveryNeeded) {
            document.querySelectorAll('.watermark-container').forEach(el => {
                const parentImg = el.parentElement.querySelector('img');
                if (parentImg && !parentImg.classList.contains('blur-xl')) {
                    el.classList.remove('opacity-0');
                }
            });
        }
    });

    observer.observe(document.body, { 
        childList: true, 
        subtree: true, 
        attributes: true, 
        attributeFilter: ['style', 'class'] 
    });
}

// Global scope for PHP inline calls
window.unlockContent = async function(workId) {
    if (!window.nusaAppData.isLoggedIn) {
        alert('Silahkan login dulu');
        window.location.href = `${window.nusaAppData.baseUrl}login`;
        return;
    }
    const btn = document.querySelector('.unlock-btn');
    if (!btn) return;
    const originalText = btn.textContent;
    
    try {
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        const response = await fetch(`${window.nusaAppData.baseUrl}works/${workId}/unlock`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            if (response.status === 401) {
                alert('Silahkan login dulu');
                window.location.href = `${window.nusaAppData.baseUrl}login`;
                return;
            }
            throw new Error(errorData.message || `HTTP Error ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            const balanceEls = document.querySelectorAll('.cc-balance');
            balanceEls.forEach(el => el.textContent = data.balance.toLocaleString() + ' CC');

            document.querySelectorAll('.unlock-overlay').forEach(el => el.remove());
            document.querySelectorAll('[id^="content-img-"]').forEach(img => {
                img.classList.remove('blur-xl');
                const url = new URL(img.src);
                url.searchParams.set('t', Date.now());
                img.src = url.toString();
                img.dataset.imgUrl = url.toString();
            });
            
            document.querySelectorAll('.watermark-container').forEach(el => {
                el.classList.remove('opacity-0');
            });

            startCountdown(data.timer);
        } else {
            alert(data.message); 
        }
    } catch (error) {
        console.error('Unlock Error:', error);
        alert('Gagal membuka konten: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
};

let countdownTimer;
function startCountdown(seconds) {
    if (countdownTimer) clearInterval(countdownTimer);

    // Remove existing timer UI if any
    const existing = document.getElementById('countdown-timer-ui');
    if (existing) existing.remove();

    const timerContainer = document.createElement('div');
    timerContainer.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 z-[1100] bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-2xl font-bold flex items-center gap-3 animate-bounce';
    timerContainer.id = 'countdown-timer-ui';
    timerContainer.innerHTML = `
        <span class="material-symbols-outlined animate-spin">timer</span>
        <span>Preview berakhir dalam: <span id="preview-timer">${seconds}</span> detik</span>
    `;
    document.body.appendChild(timerContainer);

    let timeLeft = parseInt(seconds) || 0;
    countdownTimer = setInterval(() => {
        timeLeft--;
        
        // Ensure UI updates only if element exists and number is positive
        const timerEl = document.getElementById('preview-timer');
        if (timerEl) {
            timerEl.textContent = Math.max(0, timeLeft);
        }

        if (timeLeft <= 0) {
            clearInterval(countdownTimer);
            // Lock UI interaction to prevent double clicks during reload
            const btn = document.querySelector('.unlock-btn');
            if (btn) btn.disabled = true;

            setTimeout(() => {
                window.location.reload();
            }, 800);
        }
    }, 1000);
}

// --- Sidebar Toggle Logic ---
window.toggleSidebar = function() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
};

window.closeSidebar = function() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar && overlay) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
};
