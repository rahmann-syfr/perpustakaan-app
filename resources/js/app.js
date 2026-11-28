//
// PerpusApp - Script Utama
//
// Berisi pustaka UI ringan (vanilla JS, tanpa dependensi tambahan) yang
// dipakai bersama oleh seluruh halaman.
//
// Catatan: DataTables sengaja TIDAK dipakai pada tabel yang memakai
// pagination Laravel karena keduanya saling bertabrakan (dua kontrol
// paginasi & batas 10 baris per halaman yang membingungkan).
// Sebagai gantinya tersedia live search + filter client-side.
//

/**
 * Namespace global sederhana agar tidak menabrak variabel lain.
 */
window.Perpus = window.Perpus || {};

(function (Perpus) {
    'use strict';

    /* ------------------------------------------------------------------
     | Toast notification
     |------------------------------------------------------------------*/
    const TOAST_ICONS = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-octagon-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill',
    };

    /**
     * Menampilkan toast ringkas di pojok kanan atas.
     *
     * @param {string} message
     * @param {'success'|'error'|'warning'|'info'} type
     */
    Perpus.toast = function (message, type) {
        if (!message) return;

        let host = document.getElementById('perpus-toast-host');

        if (!host) {
            host = document.createElement('div');
            host.id = 'perpus-toast-host';
            host.className = 'perpus-toast-host';
            host.setAttribute('aria-live', 'polite');
            host.setAttribute('aria-atomic', 'true');
            document.body.appendChild(host);
        }

        const toast = document.createElement('div');
        toast.className = 'perpus-toast perpus-toast--' + (type || 'info');
        toast.setAttribute('role', 'status');

        const icon = document.createElement('i');
        icon.className = 'bi ' + (TOAST_ICONS[type] || TOAST_ICONS.info);
        icon.setAttribute('aria-hidden', 'true');

        const text = document.createElement('span');
        text.textContent = message;

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'perpus-toast__close';
        close.setAttribute('aria-label', 'Tutup notifikasi');
        close.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';

        toast.append(icon, text, close);
        host.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });

        let timer = setTimeout(dismiss, 4200);

        function dismiss() {
            clearTimeout(timer);
            toast.classList.remove('is-visible');
            setTimeout(function () {
                toast.remove();
            }, 350);
        }

        close.addEventListener('click', dismiss);
        toast.addEventListener('mouseenter', function () { clearTimeout(timer); });
        toast.addEventListener('mouseleave', function () {
            timer = setTimeout(dismiss, 2000);
        });
    };

    /* ------------------------------------------------------------------
     | Sidebar (mobile)
     |------------------------------------------------------------------*/
    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const burger = document.getElementById('burgerBtn');

        if (!sidebar) return;

        function open() {
            sidebar.classList.add('is-open');
            sidebar.classList.remove('-translate-x-full');
            backdrop?.classList.remove('hidden');
            backdrop?.classList.add('is-visible');
            document.body.classList.add('perpus-no-scroll', 'overflow-hidden');
        }

        function close() {
            sidebar.classList.remove('is-open');
            sidebar.classList.add('-translate-x-full');
            backdrop?.classList.add('hidden');
            backdrop?.classList.remove('is-visible');
            document.body.classList.remove('perpus-no-scroll', 'overflow-hidden');
        }

        burger?.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.contains('is-open') ? close() : open();
        });

        document.querySelectorAll('.sidebar-close-btn, .sidebar-hide').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                close();
            });
        });

        backdrop?.addEventListener('click', close);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });

        // Menutup sidebar otomatis saat navigasi ke halaman baru (mobile).
        sidebar.querySelectorAll('.perpus-nav-item, .perpus-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1024) close();
            });
        });
    }

    function initNeoModals() {
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        window.PerpusModal = {
            open: openModal,
            close: function (id) {
                closeModal(document.getElementById(id));
            },
        };

        document.addEventListener('click', function (e) {
            const opener = e.target.closest('[data-modal-open]');
            if (opener) {
                e.preventDefault();
                openModal(opener.getAttribute('data-modal-open'));
                return;
            }

            const closer = e.target.closest('[data-modal-close]');
            if (closer) {
                e.preventDefault();
                closeModal(document.getElementById(closer.getAttribute('data-modal-close')));
                return;
            }

            const backdrop = e.target.closest('[data-neo-modal]');
            if (backdrop && e.target === backdrop) {
                closeModal(backdrop);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-neo-modal].flex').forEach(closeModal);
            }
        });
    }

    /* ------------------------------------------------------------------
     | Live search pada tabel
     |
     | Menggantikan DataTables agar tidak bentrok dengan pagination Laravel.
     | Filtering dilakukan di sisi klien pada data yang sudah dimuat.
     |------------------------------------------------------------------*/
    Perpus.tableSearch = function (options) {
        const config = Object.assign({
            input: '#tableSearch',
            table: '#table1',
            empty: '#tableEmpty',
            counter: '#tableCounter',
            rows: 'tbody tr',
            columns: null, // array index kolom yang dicari; null = semua kolom
        }, options || {});

        const input = document.querySelector(config.input);
        const table = document.querySelector(config.table);

        if (!input || !table) return;

        const emptyState = document.querySelector(config.empty);
        const counter = document.querySelector(config.counter);
        const rows = Array.prototype.slice.call(table.querySelectorAll(config.rows));
        const dataRows = rows.filter(function (row) {
            return row.dataset.searchable !== 'false';
        });

        function cellText(row) {
            if (!config.columns) {
                return (row.textContent || '').toLowerCase();
            }

            return config.columns
                .map(function (index) {
                    const cell = row.cells[index];
                    return cell ? (cell.textContent || '').toLowerCase() : '';
                })
                .join(' ');
        }

        function filter(term) {
            const query = term.trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                // Baris "belum ada data" hanya tampil saat tidak ada kata kunci pencarian.
                if (row.dataset.searchable === 'false') {
                    row.hidden = query !== '';
                    return;
                }

                const match = query === '' || cellText(row).includes(query);
                row.hidden = !match;
                if (match) {
                    visible++;
                }
            });

            if (emptyState) {
                emptyState.hidden = query === '' || visible > 0;
            }

            if (counter) {
                counter.textContent = query === ''
                    ? dataRows.length + ' data'
                    : visible + ' dari ' + dataRows.length + ' data cocok';
            }
        }

        input.addEventListener('input', function () {
            filter(input.value);
        });

        // Mendukung parameter ?search= dari navigasi server.
        const urlSearch = new URLSearchParams(window.location.search).get('search');
        if (urlSearch) {
            input.value = urlSearch;
        }

        filter(input.value);
    };

    /* ------------------------------------------------------------------
     | Filter dropdown untuk tabel
     |------------------------------------------------------------------*/
    Perpus.tableFilter = function (options) {
        const config = Object.assign({
            select: '#tableFilter',
            table: '#table1',
            rows: 'tbody tr',
            column: null, // index kolom yang diperiksa; null = cari di semua kolom
        }, options || {});

        const select = document.querySelector(config.select);
        const table = document.querySelector(config.table);

        if (!select || !table) return;

        const rows = Array.prototype.slice.call(table.querySelectorAll(config.rows));

        select.addEventListener('change', function () {
            const value = select.value;

            rows.forEach(function (row) {
                if (value === '') {
                    row.hidden = false;
                    return;
                }

                const cell = config.column !== null ? row.cells[config.column] : null;
                const text = cell ? (cell.textContent || '').trim() : '';

                row.hidden = !text.includes(value);
            });
        });
    };

    /* ------------------------------------------------------------------
     | Dialog konfirmasi kustom
     |
     | Menggantikan window.confirm agar tampilannya konsisten dengan tema
     | aplikasi dan tidak memblokir thread browser.
     |------------------------------------------------------------------*/
    Perpus.confirm = function (options) {
        const config = Object.assign({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin?',
            confirmText: 'Ya, Lanjutkan',
            cancelText: 'Batal',
            variant: 'danger',
            icon: 'bi-question-circle-fill',
        }, options || {});

        return new Promise(function (resolve) {
            const backdrop = document.createElement('div');
            backdrop.className = 'perpus-modal-backdrop';
            backdrop.setAttribute('role', 'dialog');
            backdrop.setAttribute('aria-modal', 'true');
            backdrop.setAttribute('aria-label', config.title);

            const iconEl = document.createElement('div');
            iconEl.className = 'perpus-modal__icon perpus-modal__icon--' + config.variant;
            iconEl.innerHTML = '<i class="bi ' + config.icon + '" aria-hidden="true"></i>';

            const titleEl = document.createElement('h3');
            titleEl.className = 'perpus-modal__title';
            titleEl.textContent = config.title;

            const textEl = document.createElement('p');
            textEl.className = 'perpus-modal__text';
            textEl.textContent = config.text;

            const actions = document.createElement('div');
            actions.className = 'perpus-modal__actions';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-light-secondary';
            cancelBtn.textContent = config.cancelText;

            const confirmBtn = document.createElement('button');
            confirmBtn.type = 'button';
            confirmBtn.className = 'btn btn-' + (config.variant === 'danger' ? 'danger' : 'primary');
            confirmBtn.textContent = config.confirmText;

            actions.append(cancelBtn, confirmBtn);

            const dialog = document.createElement('div');
            dialog.className = 'perpus-modal';
            dialog.append(iconEl, titleEl, textEl, actions);

            backdrop.appendChild(dialog);
            document.body.appendChild(backdrop);

            requestAnimationFrame(function () {
                backdrop.classList.add('is-visible');
            });

            function close(result) {
                backdrop.classList.remove('is-visible');
                document.removeEventListener('keydown', onKeydown);
                setTimeout(function () {
                    backdrop.remove();
                }, 250);
                resolve(result);
            }

            function onKeydown(e) {
                if (e.key === 'Escape') close(false);
            }

            cancelBtn.addEventListener('click', function () { close(false); });
            confirmBtn.addEventListener('click', function () { close(true); });
            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) close(false);
            });
            document.addEventListener('keydown', onKeydown);

            confirmBtn.focus();
        });
    };

    /* ------------------------------------------------------------------
     | Konfirmasi hapus (dipakai oleh form bertanda `form-delete`)
     |------------------------------------------------------------------*/
    Perpus.bindDeleteForms = function (options) {
        const config = Object.assign({
            title: 'Hapus data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            confirmText: 'Ya, Hapus!',
            cancelText: 'Batal',
        }, options || {});

        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) return;
            if (!form.classList.contains('form-delete')) return;
            if (form.dataset.confirmed === '1') return;

            event.preventDefault();

            Perpus.confirm({
                title: config.title,
                text: config.text,
                confirmText: config.confirmText,
                cancelText: config.cancelText,
                variant: 'danger',
                icon: 'bi-trash-fill',
            }).then(function (confirmed) {
                if (!confirmed) return;

                form.dataset.confirmed = '1';
                form.submit();
            });
        });
    };

    /* ------------------------------------------------------------------
     | Anti double-submit
     |------------------------------------------------------------------*/
    function initSubmitGuards() {
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) return;
            if (form.classList.contains('form-delete')) return;
            if (form.classList.contains('no-spinner')) return;
            if ((form.getAttribute('method') || 'GET').toUpperCase() === 'GET') return;

            const button = form.querySelector('button[type="submit"]:not([data-guard])');

            if (!button || button.disabled) return;

            button.dataset.guard = '1';
            button.disabled = true;

            const originalHtml = button.innerHTML;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

            // Kembalikan tampilan bila request gagal (mis. validasi server).
            window.setTimeout(function () {
                button.disabled = false;
                button.innerHTML = originalHtml;
                delete button.dataset.guard;
            }, 12000);
        }, true);
    }

    /* ------------------------------------------------------------------
     | Animasi masuk bertahap
     |------------------------------------------------------------------*/
    function initEntranceAnimation() {
        document.querySelectorAll('.section .card').forEach(function (card, index) {
            card.classList.add('perpus-animate-in');
            card.style.animationDelay = Math.min(index * 0.05, 0.4) + 's';
        });
    }

    /* ------------------------------------------------------------------
     | Tooltip
     |------------------------------------------------------------------*/
    function initTooltips() {
        if (typeof window.bootstrap === 'undefined') return;

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new window.bootstrap.Tooltip(el);
        });
    }

    /* ------------------------------------------------------------------
     | Angka pada statistik (animasi count-up)
     |------------------------------------------------------------------*/
    Perpus.countUp = function (element, target, options) {
        const config = Object.assign({ duration: 900, suffix: '', prefix: '' }, options || {});
        const value = Number(target) || 0;

        if (!element) return;

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const start = performance.now();

            function step(now) {
                const progress = Math.min((now - start) / config.duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);

                element.textContent = config.prefix + Math.round(value * eased) + config.suffix;

                if (progress < 1) requestAnimationFrame(step);
            }

            requestAnimationFrame(step);
        } else {
            element.textContent = config.prefix + value + config.suffix;
        }
    };

    /* ------------------------------------------------------------------
     | Inisialisasi
     |------------------------------------------------------------------*/
    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
        initNeoModals();
        initEntranceAnimation();
        initTooltips();
        initSubmitGuards();
        Perpus.bindDeleteForms();

        // Flash message dari server (Laravel session).
        const flash = document.getElementById('flash-messages');
        if (flash) {
            const data = flash.dataset;

            if (data.success) Perpus.toast(data.success, 'success');
            if (data.error) Perpus.toast(data.error, 'error');
            if (data.warning) Perpus.toast(data.warning, 'warning');
            if (data.validation) Perpus.toast(data.validation, 'error');
        }
    });
})(window.Perpus);

