/**
 * NeumoLav - Hybrid SPA Navigation Engine
 * Provides seamless client-side page transitions across all views without full-page reloads,
 * keeping the Header, Sidebar, and Footer persistent while dynamically updating page content,
 * title, active sidebar links, and mounting target Vue modules.
 */

class SpaEngine {
    constructor() {
        this.progressBar = null;
        this.progressTimer = null;
        this.currentProgress = 0;
        this.isNavigating = false;
        this.cache = new Map();
        this.activeAbortController = null;
    }

    init() {
        this.createProgressBar();
        this.bindEvents();
        this.updateActiveSidebar(window.location.href);
        this.updateNavbarTitle();
    }

    createProgressBar() {
        if (document.getElementById('spa-progress-bar')) {
            this.progressBar = document.getElementById('spa-progress-bar');
            return;
        }
        const bar = document.createElement('div');
        bar.id = 'spa-progress-bar';
        bar.className = 'spa-progress-bar';
        document.body.appendChild(bar);
        this.progressBar = bar;
    }

    startProgress() {
        if (!this.progressBar) return;
        this.currentProgress = 15;
        this.progressBar.style.opacity = '1';
        this.progressBar.style.width = `${this.currentProgress}%`;

        clearInterval(this.progressTimer);
        this.progressTimer = setInterval(() => {
            if (this.currentProgress < 85) {
                this.currentProgress += Math.floor(Math.random() * 12) + 5;
                if (this.currentProgress > 85) this.currentProgress = 85;
                this.progressBar.style.width = `${this.currentProgress}%`;
            }
        }, 120);
    }

    finishProgress() {
        clearInterval(this.progressTimer);
        if (!this.progressBar) return;
        this.progressBar.style.width = '100%';
        setTimeout(() => {
            this.progressBar.style.opacity = '0';
            setTimeout(() => {
                this.progressBar.style.width = '0%';
                this.currentProgress = 0;
            }, 250);
        }, 180);
    }

    bindEvents() {
        // Intercept internal link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                return;
            }

            // Exclude logout, downloads, external, target="_blank", or explicitly excluded links
            if (
                link.classList.contains('exit-system') ||
                link.hasAttribute('download') ||
                link.getAttribute('target') === '_blank' ||
                link.hasAttribute('data-no-spa') ||
                link.closest('[data-no-spa]')
            ) {
                return;
            }

            // Check if URL belongs to the same origin and is an admin view
            try {
                const targetUrl = new URL(href, window.location.origin);
                if (targetUrl.origin !== window.location.origin) return;

                // If user held Ctrl/Cmd/Shift/Alt or middle click, allow browser default
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

                // Check if target is same as current URL
                if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) {
                    e.preventDefault();
                    return;
                }

                // Intercept navigation
                e.preventDefault();
                this.navigate(targetUrl.href);
            } catch (err) {
                // Fallback to normal navigation
            }
        });

        // Browser history (Back / Forward)
        window.addEventListener('popstate', () => {
            this.navigate(window.location.href, false);
        });
    }

    async navigate(targetUrl, pushState = true) {
        if (this.isNavigating && this.activeAbortController) {
            this.activeAbortController.abort();
        }

        this.isNavigating = true;
        this.activeAbortController = new AbortController();
        const signal = this.activeAbortController.signal;

        const currentContent = document.getElementById('spa-content-container') || document.querySelector('.content-wrapper');
        if (currentContent) {
            currentContent.classList.add('spa-transitioning');
        }

        this.startProgress();

        try {
            const response = await fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-SPA-Request': '1',
                },
                signal,
            });

            // Redirect handling (e.g., session timeout redirected to /login)
            if (response.redirected && response.url) {
                window.location.href = response.url;
                return;
            }

            if (!response.ok) {
                window.location.href = targetUrl;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            const newContent = newDoc.getElementById('spa-content-container') || newDoc.querySelector('.content-wrapper');
            if (!newContent || !currentContent) {
                window.location.href = targetUrl;
                return;
            }

            // 1. Update Title
            if (newDoc.title) {
                document.title = newDoc.title;
            }

            // 2. Update Browser History
            if (pushState) {
                window.history.pushState({ spa: true, url: targetUrl }, '', targetUrl);
            }

            // 3. Close open modals, popovers, or backdrops from previous view
            if (window.jQuery) {
                window.jQuery('.modal.show').modal('hide');
                window.jQuery('.modal-backdrop').remove();
                window.jQuery('body').removeClass('modal-open').css('padding-right', '');
                window.jQuery('.dropdown-menu.show').removeClass('show');
            }
            if (window.Swal && typeof window.Swal.isVisible === 'function' && window.Swal.isVisible()) {
                window.Swal.close();
            }

            // 4. Swap Content
            currentContent.innerHTML = newContent.innerHTML;

            // 5. Inject View-specific Styles if new
            const newStyles = newDoc.querySelectorAll('link[rel="stylesheet"], head style');
            newStyles.forEach((styleTag) => {
                const href = styleTag.getAttribute('href');
                if (href) {
                    const exists = document.head.querySelector(`link[href="${href}"]`);
                    if (!exists) {
                        const clone = document.createElement('link');
                        clone.rel = 'stylesheet';
                        clone.href = href;
                        document.head.appendChild(clone);
                    }
                }
            });

            // 6. Execute Inline Scripts & Load Target View Module
            this.executePageScripts(newDoc, newContent);

            // 7. Update Sidebar Active Item
            this.updateActiveSidebar(targetUrl);

            // 8. Update Navbar Active Title
            this.updateNavbarTitle();

            // 9. Reset View Scroll
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });

            // 10. Notify AdminLTE Layout
            if (window.jQuery) {
                window.jQuery(window).trigger('resize');
            }

            // 11. Dispatch custom SPA event for any listening legacy hooks
            window.dispatchEvent(new CustomEvent('spa:navigated', { detail: { url: targetUrl } }));

        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('SPA navigation error, falling back to full reload:', err);
                window.location.href = targetUrl;
            }
        } finally {
            this.finishProgress();
            this.isNavigating = false;
            if (currentContent) {
                currentContent.classList.remove('spa-transitioning');
                currentContent.classList.add('spa-entered');
                setTimeout(() => currentContent.classList.remove('spa-entered'), 300);
            }
        }
    }

    executePageScripts(newDoc, newContent) {
        const scriptsToExecute = [];

        // 1. Scripts inside #spa-page-scripts (from @stack('scripts'))
        const pageScriptsContainer = newDoc.getElementById('spa-page-scripts');
        if (pageScriptsContainer) {
            pageScriptsContainer.querySelectorAll('script').forEach((s) => scriptsToExecute.push(s));
        }

        // 2. Scripts inside content container
        if (newContent) {
            newContent.querySelectorAll('script').forEach((s) => scriptsToExecute.push(s));
        }

        // 3. Fallback: if neither had scripts, inspect body scripts excluding layout globals
        if (scriptsToExecute.length === 0) {
            newDoc.querySelectorAll('body script').forEach((s) => {
                const src = s.getAttribute('src') || '';
                if (
                    src.includes('jquery') ||
                    src.includes('adminlte') ||
                    src.includes('bootstrap') ||
                    src.includes('datatables') ||
                    src.includes('highcharts') ||
                    src.includes('funciones.js') ||
                    src.includes('app.js') ||
                    src.includes('quickview.js')
                ) {
                    return;
                }
                scriptsToExecute.push(s);
            });
        }

        const inlineScripts = [];
        const moduleScripts = [];

        scriptsToExecute.forEach((s) => {
            const type = s.getAttribute('type') || '';
            const src = s.getAttribute('src');

            if (src) {
                // Avoid re-executing core app or layout scripts
                if (
                    src.includes('app.js') ||
                    src.includes('quickview.js') ||
                    src.includes('jquery') ||
                    src.includes('adminlte') ||
                    src.includes('bootstrap') ||
                    src.includes('@vite/client')
                ) {
                    return;
                }
                moduleScripts.push({ src, type });
            } else if (s.textContent && s.textContent.trim()) {
                // Ignore layout inline scripts
                if (s.textContent.includes('toggleMenuIcon') || s.textContent.includes('uibutton')) {
                    return;
                }
                inlineScripts.push(s.textContent);
            }
        });

        // 1. Run inline scripts (window.__*__STATE__)
        inlineScripts.forEach((code) => {
            try {
                const fn = new Function(code);
                fn.call(window);
            } catch (err) {
                console.warn('SPA inline script evaluation error:', err);
            }
        });

        // 2. Load module scripts (mounts Vue components)
        moduleScripts.forEach(({ src, type }) => {
            const separator = src.includes('?') ? '&' : '?';
            const freshSrc = `${src}${separator}_spa=${Date.now()}`;

            if (type === 'module') {
                import(/* @vite-ignore */ freshSrc).catch(() => {
                    const tag = document.createElement('script');
                    tag.type = 'module';
                    tag.src = freshSrc;
                    document.body.appendChild(tag);
                });
            } else {
                const tag = document.createElement('script');
                tag.src = freshSrc;
                document.body.appendChild(tag);
            }
        });
    }

    updateActiveSidebar(targetUrl) {
        try {
            const url = new URL(targetUrl, window.location.origin);
            const currentPath = url.pathname.replace(/\/+$/, '');

            const sidebarLinks = document.querySelectorAll('.main-sidebar .nav-sidebar .nav-link');
            sidebarLinks.forEach((link) => {
                const href = link.getAttribute('href');
                if (!href) return;

                const linkUrl = new URL(href, window.location.origin);
                const linkPath = linkUrl.pathname.replace(/\/+$/, '');

                const isExact = currentPath === linkPath;
                const isSubpath = linkPath !== '/admin/home' && currentPath.startsWith(linkPath + '/');

                if (isExact || isSubpath) {
                    link.classList.add('active');

                    // If inside a treeview, expand parent
                    const parentTree = link.closest('.nav-treeview');
                    if (parentTree) {
                        parentTree.style.display = 'block';
                        const parentItem = parentTree.closest('.nav-item');
                        if (parentItem) {
                            parentItem.classList.add('menu-open', 'menu-is-opening');
                            const parentLink = parentItem.querySelector(':scope > .nav-link');
                            if (parentLink) parentLink.classList.add('active');
                        }
                    }
                } else {
                    link.classList.remove('active');
                    // Check if parent treeview has no other active children
                    const parentTree = link.closest('.nav-treeview');
                    if (parentTree && !parentTree.querySelector('.nav-link.active')) {
                        const parentItem = parentTree.closest('.nav-item');
                        if (parentItem) {
                            parentItem.classList.remove('menu-open', 'menu-is-opening');
                            parentTree.style.display = 'none';
                            const parentLink = parentItem.querySelector(':scope > .nav-link');
                            if (parentLink) parentLink.classList.remove('active');
                        }
                    }
                }
            });
        } catch (e) {
            // ignore
        }
    }

    updateNavbarTitle() {
        const navbarTitle = document.getElementById('spa-current-title');
        if (!navbarTitle) return;

        const headerTitle = document.querySelector('.content-header h1, .content-header .m-0, .content-header h2');
        if (headerTitle && headerTitle.textContent.trim()) {
            navbarTitle.textContent = headerTitle.textContent.trim();
            return;
        }

        if (document.title) {
            const parts = document.title.split('-');
            const titlePart = (parts.length > 1 ? parts[parts.length - 1] : parts[0]).trim();
            if (titlePart) {
                navbarTitle.textContent = titlePart;
            }
        }
    }
}

export const spaEngine = new SpaEngine();
