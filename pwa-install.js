// ============================================
// GoalTicket — PWA Install Helper
// ============================================
// Shows a friendly "Install" button on supported browsers (Chrome/Android),
// and an instructional banner on iOS Safari (which doesn't fire the event).

(function() {
    'use strict';

    let deferredPrompt = null;

    // Detect iOS Safari
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches
                            || window.navigator.standalone === true;

    // Don't show anything if already installed
    if (isInStandaloneMode) {
        console.log('[PWA] Running as installed app — skipping install prompts');
        return;
    }

    // Don't show again if user dismissed within the last 7 days
    const dismissedAt = localStorage.getItem('pwa-install-dismissed');
    if (dismissedAt && (Date.now() - parseInt(dismissedAt)) < 7 * 24 * 60 * 60 * 1000) {
        console.log('[PWA] User dismissed install banner recently');
        return;
    }

    // ============================================
    // CASE 1: Chrome / Android — beforeinstallprompt event
    // ============================================
    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallBanner('chromium');
    });

    // ============================================
    // CASE 2: iOS Safari — show manual instructions
    // ============================================
    if (isIOS) {
        // Slight delay so it doesn't appear at the same instant as the page
        setTimeout(() => showInstallBanner('ios'), 1500);
    }

    // ============================================
    // Banner UI
    // ============================================
    function showInstallBanner(type) {
        // Avoid duplicates
        if (document.getElementById('pwa-install-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.className = 'pwa-banner';

        if (type === 'chromium') {
            banner.innerHTML = `
                <div class="pwa-banner-content">
                    <div class="pwa-banner-icon">📲</div>
                    <div class="pwa-banner-text">
                        <strong>Install GoalTicket</strong>
                        <span>Add to your home screen for faster access</span>
                    </div>
                    <button class="pwa-banner-btn" id="pwa-install-btn">Install</button>
                    <button class="pwa-banner-close" id="pwa-banner-close" aria-label="Dismiss">×</button>
                </div>
            `;
        } else if (type === 'ios') {
            banner.innerHTML = `
                <div class="pwa-banner-content">
                    <div class="pwa-banner-icon">📲</div>
                    <div class="pwa-banner-text">
                        <strong>Install GoalTicket</strong>
                        <span>Tap <b>Share</b> below, then <b>Add to Home Screen</b></span>
                    </div>
                    <button class="pwa-banner-close" id="pwa-banner-close" aria-label="Dismiss">×</button>
                </div>
            `;
        }

        document.body.appendChild(banner);

        // Slide in
        setTimeout(() => banner.classList.add('show'), 100);

        // Wire up the install button (chromium only)
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                const result = await deferredPrompt.userChoice;
                console.log('[PWA] User choice:', result.outcome);
                deferredPrompt = null;
                dismissBanner();
            });
        }

        // Wire up the close button
        document.getElementById('pwa-banner-close').addEventListener('click', () => {
            localStorage.setItem('pwa-install-dismissed', Date.now().toString());
            dismissBanner();
        });
    }

    function dismissBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (!banner) return;
        banner.classList.remove('show');
        setTimeout(() => banner.remove(), 300);
    }

    // ============================================
    // Detect successful install (Chrome fires this)
    // ============================================
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App installed successfully');
        dismissBanner();
    });

})();
