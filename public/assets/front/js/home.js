document.addEventListener('DOMContentLoaded', () => {
    const preloader = document.querySelector('.site-preloader');
    const hidePreloader = () => {
        if (preloader) {
            preloader.classList.add('is-hidden');
        }
    };

    window.addEventListener('load', () => window.setTimeout(hidePreloader, 450), { once: true });
    window.setTimeout(hidePreloader, 2600);

    const clockNodes = Array.from(document.querySelectorAll('[data-clock]'));

    const updateClocks = () => {
        clockNodes.forEach((node) => {
            const timezone = node.dataset.clock;
            const label = node.dataset.label || '';

            try {
                const time = new Intl.DateTimeFormat('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true,
                    timeZone: timezone,
                }).format(new Date());

                node.textContent = `${label} ${time}`;
            } catch (error) {
                node.textContent = `${label} --:--:--`;
            }
        });
    };

    if (clockNodes.length) {
        let activeClock = Math.max(0, clockNodes.findIndex((node) => node.classList.contains('is-active')));
        const showClock = (nextIndex) => {
            activeClock = (nextIndex + clockNodes.length) % clockNodes.length;
            clockNodes.forEach((node, index) => node.classList.toggle('is-active', index === activeClock));
        };

        showClock(activeClock);
        updateClocks();
        window.setInterval(updateClocks, 1000);
        window.setInterval(() => showClock(activeClock + 1), 2600);
    }

    document.querySelectorAll('[data-currency-converter]').forEach((converter) => {
        let rate = null;
        const usdInput = converter.querySelector('[data-usd-input]');
        const jpyInput = converter.querySelector('[data-jpy-input]');

        if (!usdInput || !jpyInput) {
            return;
        }

        const formatValue = (value) => {
            if (!Number.isFinite(value)) {
                return '';
            }

            return value >= 100 ? Math.round(value).toString() : value.toFixed(2).replace(/\.?0+$/, '');
        };

        const convertFromUsd = () => {
            if (!rate) {
                return;
            }

            jpyInput.value = formatValue(Number(usdInput.value) * rate);
        };

        const convertFromJpy = () => {
            if (!rate) {
                return;
            }

            usdInput.value = formatValue(Number(jpyInput.value) / rate);
        };

        usdInput.addEventListener('input', convertFromUsd);
        jpyInput.addEventListener('input', convertFromJpy);

        const rateSources = [
            () => fetch('https://open.er-api.com/v6/latest/USD')
                .then((response) => response.ok ? response.json() : null)
                .then((data) => Number(data?.rates?.JPY)),
            () => fetch('https://api.frankfurter.app/latest?from=USD&to=JPY')
                .then((response) => response.ok ? response.json() : null)
                .then((data) => Number(data?.rates?.JPY)),
            () => fetch('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json')
                .then((response) => response.ok ? response.json() : null)
                .then((data) => Number(data?.usd?.jpy)),
        ];

        const loadRate = (sourceIndex = 0) => {
            const source = rateSources[sourceIndex];

            if (!source) {
                jpyInput.value = '';
                return;
            }

            source()
                .then((forexRate) => {
                if (Number.isFinite(forexRate) && forexRate > 0) {
                    rate = forexRate;
                    converter.dataset.usdJpyRate = String(forexRate);
                    convertFromUsd();
                    return;
                }

                    loadRate(sourceIndex + 1);
                })
                .catch(() => loadRate(sourceIndex + 1));
        };

        loadRate();
    });

    const navToggle = document.querySelector('.nav-toggle');
    const navGrid = document.querySelector('.navgrid');
    const navLinks = document.querySelector('.navlinks');

    if (navToggle && navGrid && navLinks) {
        const setNavOpen = (isOpen) => {
            navGrid.classList.toggle('is-open', isOpen);
            navToggle.classList.toggle('is-active', isOpen);
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            navToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
        };

        navToggle.addEventListener('click', () => {
            setNavOpen(!navGrid.classList.contains('is-open'));
        });

        navLinks.addEventListener('click', (event) => {
            if (event.target.closest('a') && !event.target.closest('.sbk-brand-trigger') && !event.target.closest('.sbk-about-trigger')) {
                setNavOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setNavOpen(false);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                setNavOpen(false);
            }
        });
    }

    document.querySelectorAll('[data-slider]').forEach((slider) => {
        const slides = Array.from(slider.querySelectorAll('[data-slide]'));
        const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
        const prev = slider.querySelector('[data-slider-prev]');
        const next = slider.querySelector('[data-slider-next]');
        let index = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
        let timer = null;

        const show = (nextIndex) => {
            index = (nextIndex + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
            dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === index));
        };

        const restart = () => {
            if (timer) {
                window.clearInterval(timer);
            }

            if (slider.dataset.autoplay === 'true' && slides.length > 1) {
                timer = window.setInterval(() => show(index + 1), 5500);
            }
        };

        prev?.addEventListener('click', () => {
            show(index - 1);
            restart();
        });

        next?.addEventListener('click', () => {
            show(index + 1);
            restart();
        });

        dots.forEach((dot, dotIndex) => {
            dot.addEventListener('click', () => {
                show(dotIndex);
                restart();
            });
        });

        show(index);
        restart();
    });

    document.querySelectorAll('[data-rail-slider]').forEach((slider) => {
        const track = slider.querySelector('.rail-track');
        const prev = slider.querySelector('[data-rail-prev]');
        const next = slider.querySelector('[data-rail-next]');

        if (!track) {
            return;
        }

        const scrollByPage = (direction) => {
            const firstItem = track.querySelector('.rail-item');
            const amount = firstItem ? firstItem.getBoundingClientRect().width + 18 : track.clientWidth * 0.8;
            track.scrollBy({ left: amount * direction, behavior: 'smooth' });
        };

        const autoScroll = () => {
            const maxScroll = track.scrollWidth - track.clientWidth;

            if (maxScroll <= 0) {
                return;
            }

            if (track.scrollLeft >= maxScroll - 8) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
                return;
            }

            scrollByPage(1);
        };

        let railTimer = window.setInterval(autoScroll, 4200);
        const restartRail = () => {
            window.clearInterval(railTimer);
            railTimer = window.setInterval(autoScroll, 4200);
        };

        prev?.addEventListener('click', () => {
            scrollByPage(-1);
            restartRail();
        });
        next?.addEventListener('click', () => {
            scrollByPage(1);
            restartRail();
        });
    });

    const showToast = (message, type = 'success') => {
        let toast = document.querySelector('.sbk-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'sbk-toast';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.toggle('is-error', type === 'error');
        toast.classList.add('is-visible');
        window.clearTimeout(toast.hideTimer);
        toast.hideTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2600);
    };

    document.addEventListener('click', (event) => {
        const card = event.target.closest('[data-card-url]');

        if (!card || event.target.closest('a, button, form, input, select, textarea, label')) {
            return;
        }

        window.location.href = card.dataset.cardUrl;
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-cart-form]');

        if (!form) {
            return;
        }

        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        const originalText = button ? button.textContent : '';

        if (button) {
            button.disabled = true;
            button.textContent = 'Adding...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Unable to add vehicle to cart.');
            }

            if (button) {
                button.textContent = data.button_label || 'Added';
            }

            const cartLink = document.querySelector('.sbk-head-icon[href*="/cart"]');
            if (cartLink && data.cart_count !== undefined) {
                let badge = cartLink.querySelector('b');

                if (!badge) {
                    badge = document.createElement('b');
                    cartLink.appendChild(badge);
                }

                badge.textContent = data.cart_count;
            }

            showToast(data.message || 'Vehicle has been added to cart.');
        } catch (error) {
            if (button) {
                button.textContent = originalText;
            }

            showToast(error.message, 'error');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });

    const revealItems = Array.from(document.querySelectorAll('.reveal-on-scroll'));

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.18 });

        revealItems.forEach((item) => observer.observe(item));

        window.setTimeout(() => {
            revealItems.forEach((item) => item.classList.add('is-visible'));
        }, 900);
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }
});
