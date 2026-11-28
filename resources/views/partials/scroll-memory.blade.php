<script id="scroll-memory">
    (function () {
        const SIDEBAR_KEY = 'perpus:sidebar-scroll';
        const PAGE_KEY = 'perpus:scroll:' + window.location.pathname;
        const sidebarNav = document.querySelector('.perpus-sidebar__nav');

        function readStorage(key) {
            try {
                const raw = window.sessionStorage.getItem(key);
                if (raw === null) {
                    return null;
                }
                const parsed = parseInt(raw, 10);
                return Number.isFinite(parsed) && parsed >= 0 ? parsed : null;
            } catch (e) {
                return null;
            }
        }

        function writeStorage(key, value) {
            try {
                window.sessionStorage.setItem(key, String(Math.max(0, Math.round(value))));
            } catch (e) {}
        }

        // Pulihkan posisi scroll sidebar segera sebelum paint agar tidak berkedip.
        if (sidebarNav) {
            const savedSidebarTop = readStorage(SIDEBAR_KEY);
            if (savedSidebarTop !== null) {
                const maxSidebarTop = Math.max(0, sidebarNav.scrollHeight - sidebarNav.clientHeight);
                sidebarNav.scrollTop = Math.min(savedSidebarTop, maxSidebarTop);
            }

            const activeItem = sidebarNav.querySelector('.perpus-nav-item.active');
            if (activeItem) {
                const navRect = sidebarNav.getBoundingClientRect();
                const itemRect = activeItem.getBoundingClientRect();
                if (itemRect.top < navRect.top) {
                    sidebarNav.scrollTop -= (navRect.top - itemRect.top) + 12;
                } else if (itemRect.bottom > navRect.bottom) {
                    sidebarNav.scrollTop += (itemRect.bottom - navRect.bottom) + 12;
                }
            }
        }

        function saveAll() {
            if (sidebarNav) {
                writeStorage(SIDEBAR_KEY, sidebarNav.scrollTop);
            }

            if (!document.body.classList.contains('perpus-no-scroll')) {
                writeStorage(PAGE_KEY, window.scrollY || window.pageYOffset || 0);
            }
        }

        function restoreWindowScroll() {
            if (window.location.hash) {
                return;
            }

            if (document.body.classList.contains('perpus-no-scroll')) {
                return;
            }

            const savedTop = readStorage(PAGE_KEY);
            if (savedTop === null) {
                return;
            }

            const maxTop = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            const targetTop = Math.min(savedTop, maxTop);

            if (Math.abs((window.scrollY || window.pageYOffset || 0) - targetTop) > 1) {
                window.scrollTo({ top: targetTop, behavior: 'instant' });
            }
        }

        let sidebarRaf = null;
        if (sidebarNav) {
            sidebarNav.addEventListener('scroll', function () {
                if (sidebarRaf !== null) {
                    return;
                }
                sidebarRaf = window.requestAnimationFrame(function () {
                    sidebarRaf = null;
                    writeStorage(SIDEBAR_KEY, sidebarNav.scrollTop);
                });
            }, { passive: true });
        }

        let windowRaf = null;
        window.addEventListener('scroll', function () {
            if (windowRaf !== null || document.body.classList.contains('perpus-no-scroll')) {
                return;
            }
            windowRaf = window.requestAnimationFrame(function () {
                windowRaf = null;
                if (!document.body.classList.contains('perpus-no-scroll')) {
                    writeStorage(PAGE_KEY, window.scrollY || window.pageYOffset || 0);
                }
            });
        }, { passive: true });

        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');
            if (!link) {
                return;
            }

            const href = link.getAttribute('href') || '';
            if (href === '#' || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            try {
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin) {
                    saveAll();
                }
            } catch (e) {}
        }, { capture: true });

        window.addEventListener('pagehide', saveAll);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', restoreWindowScroll, { once: true });
        } else {
            restoreWindowScroll();
        }

        window.addEventListener('load', restoreWindowScroll, { once: true });
    })();
</script>
