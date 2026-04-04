export default function initAjaxLoader() {
    console.log('Ajax Loader Initialized');

    const loaderTarget = '.page-wrapper';
    
    // Add a global loading indicator element if it doesn't exist
    if (!document.getElementById('ajax-loading-bar')) {
        const bar = document.createElement('div');
        bar.id = 'ajax-loading-bar';
        bar.style.cssText = 'position: fixed; top: 0; left: 0; height: 3px; background: #206bc4; z-index: 9999; width: 0; transition: width 0.3s ease, opacity 0.3s ease;';
        document.body.appendChild(bar);
    }

    const showLoader = () => {
        const bar = document.getElementById('ajax-loading-bar');
        bar.style.width = '30%';
        bar.style.opacity = '1';
        setTimeout(() => { bar.style.width = '70%'; }, 200);
        document.body.classList.add('loading');
    };

    const hideLoader = () => {
        const bar = document.getElementById('ajax-loading-bar');
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            setTimeout(() => { bar.style.width = '0'; }, 300);
        }, 200);
        document.body.classList.remove('loading');
    };

    const loadContent = async (url, targetSelector, pushState = true) => {
        showLoader();
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newContent = doc.querySelector(targetSelector);
            const currentContent = document.querySelector(targetSelector);

            if (newContent && currentContent) {
                currentContent.innerHTML = newContent.innerHTML;
                
                if (pushState) {
                    window.history.pushState({ path: url }, '', url);
                }
                
                // Re-bind events to the new content
                bindEvents(currentContent);
                
                // Scroll to top of the specific container or page
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Ajax Load Failed:', error);
            window.location.href = url;
        } finally {
            hideLoader();
        }
    };

    const bindEvents = (container = document) => {
        // Intercept GET forms (Filters)
        container.querySelectorAll('form[method="GET"]').forEach(form => {
            if (form.classList.contains('no-ajax')) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const action = form.getAttribute('action') || window.location.pathname;
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = `${action}${action.includes('?') ? '&' : '?'}${params.toString()}`;
                
                loadContent(url, loaderTarget);
            });

            // Handle auto-submit inputs
            form.querySelectorAll('select[onchange], input[onchange]').forEach(input => {
                const onchange = input.getAttribute('onchange');
                if (onchange && onchange.includes('submit()')) {
                    input.removeAttribute('onchange');
                    input.addEventListener('change', () => {
                        form.dispatchEvent(new Event('submit', { cancelable: true }));
                    });
                }
            });
        });

        // Intercept Links (Pagination)
        container.querySelectorAll('.pagination a, a.ajax-filter').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = link.getAttribute('href');
                if (url && url !== '#' && !url.startsWith('javascript:')) {
                    loadContent(url, loaderTarget);
                }
            });
        });
    };

    // Initial binding
    bindEvents();

    // Support back/forward buttons
    window.addEventListener('popstate', (e) => {
        loadContent(window.location.href, loaderTarget, false);
    });
}
