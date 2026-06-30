/**
 * BeautyDen Premium Admin — Core Interactions
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'pa_sidebar_collapsed';
    const shell = document.getElementById('paShell');
    if (!shell) return;

    const sidebarToggle = document.getElementById('paSidebarToggle');
    const mobileToggle = document.getElementById('paMobileToggle');
    const overlay = document.getElementById('paOverlay');
    const searchOverlay = document.getElementById('paSearchOverlay');
    const searchModalInput = document.getElementById('paSearchModalInput');
    const searchTrigger = document.getElementById('paSearchTrigger');

    /* ── Sidebar collapse (desktop) ── */
    function initSidebar() {
        const collapsed = localStorage.getItem(STORAGE_KEY) === '1';
        if (collapsed && window.innerWidth > 991) {
            shell.classList.add('sidebar-collapsed');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                if (window.innerWidth <= 991) {
                    closeMobileSidebar();
                    return;
                }
                shell.classList.toggle('sidebar-collapsed');
                localStorage.setItem(
                    STORAGE_KEY,
                    shell.classList.contains('sidebar-collapsed') ? '1' : '0'
                );
            });
        }
    }

    /* ── Mobile sidebar ── */
    function openMobileSidebar() {
        shell.classList.add('sidebar-mobile-open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        shell.classList.remove('sidebar-mobile-open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            if (shell.classList.contains('sidebar-mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
            closeMobileSidebar();
        }
    });

    /* ── Services / Products toggle ── */
    function initMenuTabs() {
        const showServices = document.getElementById('show-services');
        const showProducts = document.getElementById('show-products');
        const servicesSection = document.querySelector('.services-menu-section');
        const productsSection = document.querySelector('.products-menu-section');

        if (!showServices || !showProducts) return;

        showServices.addEventListener('click', function () {
            showServices.classList.add('active');
            showProducts.classList.remove('active');
            if (servicesSection) servicesSection.style.display = '';
            if (productsSection) productsSection.style.display = 'none';
        });

        showProducts.addEventListener('click', function () {
            showProducts.classList.add('active');
            showServices.classList.remove('active');
            if (servicesSection) servicesSection.style.display = 'none';
            if (productsSection) productsSection.style.display = '';
        });

        if (window.location.href.indexOf('product') > -1) {
            showProducts.click();
        }
    }

    /* ── Global search overlay ── */
    function openSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.add('active');
        if (searchModalInput) {
            setTimeout(function () { searchModalInput.focus(); }, 100);
        }
    }

    function closeSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.remove('active');
        if (searchModalInput) searchModalInput.value = '';
    }

    if (searchTrigger) {
        searchTrigger.addEventListener('click', openSearch);
    }

    if (searchOverlay) {
        searchOverlay.addEventListener('click', function (e) {
            if (e.target === searchOverlay) closeSearch();
        });
    }

    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape') {
            closeSearch();
            closeMobileSidebar();
        }
    });

    /* ── Search filter ── */
    if (searchModalInput) {
        searchModalInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.pa-search-result').forEach(function (el) {
                const text = el.textContent.toLowerCase();
                el.style.display = !q || text.includes(q) ? '' : 'none';
            });
        });
    }

    /* ── Timeframe toggles ── */
    document.querySelectorAll('.pa-timeframe').forEach(function (group) {
        group.querySelectorAll('button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                group.querySelectorAll('button').forEach(function (b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
            });
        });
    });

    /* ── Feather icons refresh ── */
    function refreshIcons() {
        if (window.feather) {
            window.feather.replace({ width: 18, height: 18 });
        }
    }

    /* ── Active nav tooltips on collapsed sidebar ── */
    function initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        }
    }

    /* ── Sync legacy page titles into topbar ── */
    function syncPageTitle() {
        const topTitle = document.querySelector('.pa-page-title');
        if (!topTitle) return;

        if (document.querySelector('.pa-crud-page-header')) {
            topTitle.style.display = 'none';
            return;
        }

        if (document.querySelector('.pa-list-page')) {
            topTitle.style.display = 'none';
            return;
        }

        const sectionTitle = (document.body.dataset.pageHeading || '').trim();
        if (sectionTitle) {
            topTitle.textContent = sectionTitle;
            topTitle.style.display = '';
            return;
        }

        const legacyTitle = document.querySelector('.content-header-title');
        if (legacyTitle) {
            topTitle.textContent = legacyTitle.textContent.trim();
            topTitle.style.display = '';
        }
    }

    /* ── Init ── */
    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
        initMenuTabs();
        initTooltips();
        syncPageTitle();
        refreshIcons();
    });

    window.PremiumAdmin = {
        openSearch: openSearch,
        closeSearch: closeSearch,
        refreshIcons: refreshIcons
    };
})();
