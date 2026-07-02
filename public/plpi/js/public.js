document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a');

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            menu.classList.toggle('show');
            toggle.classList.toggle('is-active');
        });

        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('show');
                toggle.classList.remove('is-active');
            });
        });

        document.addEventListener('click', function (event) {
            const isClickInsideMenu = menu.contains(event.target);
            const isClickToggle = toggle.contains(event.target);

            if (!isClickInsideMenu && !isClickToggle) {
                menu.classList.remove('show');
                toggle.classList.remove('is-active');
            }
        });
    }

    const progressBar = document.getElementById('readingProgressBar');

    if (progressBar) {
        const updateReadingProgress = function () {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

            progressBar.style.width = progress + '%';
        };

        updateReadingProgress();

        window.addEventListener('scroll', updateReadingProgress);
        window.addEventListener('resize', updateReadingProgress);
    }

    const copyButton = document.querySelector('.copy-link-btn');

    if (copyButton) {
        copyButton.addEventListener('click', function () {
            const url = copyButton.getAttribute('data-url') || window.location.href;

            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function () {
                    showCopied(copyButton);
                });
            } else {
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);

                showCopied(copyButton);
            }
        });
    }

    function showCopied(button) {
        button.textContent = 'Link Tersalin';

        setTimeout(function () {
            button.textContent = 'Salin Link';
        }, 1800);
    }

    const articleSearch = document.getElementById('articleSearch');
    const categoryButtons = document.querySelectorAll('.category-filter');
    const articleCards = document.querySelectorAll('.js-article-card');
    const articleEmpty = document.getElementById('articleEmpty');
    const articleCount = document.getElementById('articleCount');
    const resetArticleFilter = document.getElementById('resetArticleFilter');

    let activeCategory = 'all';

    function filterArticles() {
        const keyword = articleSearch ? articleSearch.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        articleCards.forEach(function (card) {
            const title = card.getAttribute('data-title') || '';
            const category = card.getAttribute('data-category') || '';
            const summary = card.getAttribute('data-summary') || '';

            const matchKeyword =
                title.includes(keyword) ||
                category.includes(keyword) ||
                summary.includes(keyword);

            const matchCategory =
                activeCategory === 'all' ||
                category === activeCategory.toLowerCase();

            const isVisible = matchKeyword && matchCategory;

            card.classList.toggle('is-hidden', !isVisible);

            if (isVisible && !card.classList.contains('featured-article-card')) {
                visibleCount++;
            }
        });

        if (articleCount) {
            articleCount.textContent = visibleCount;
        }

        if (articleEmpty) {
            articleEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (articleSearch) {
        articleSearch.addEventListener('input', filterArticles);
    }

    if (categoryButtons.length > 0) {
        categoryButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                categoryButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');
                activeCategory = button.getAttribute('data-category') || 'all';

                filterArticles();
            });
        });
    }

    if (resetArticleFilter) {
        resetArticleFilter.addEventListener('click', function () {
            activeCategory = 'all';

            if (articleSearch) {
                articleSearch.value = '';
            }

            categoryButtons.forEach(function (button) {
                button.classList.toggle('active', button.getAttribute('data-category') === 'all');
            });

            filterArticles();
        });
    }

    const passwordToggle = document.querySelector('[data-password-toggle]');

    if (passwordToggle) {
        passwordToggle.addEventListener('click', function () {
            const passwordInput = document.getElementById('password');

            if (!passwordInput) {
                return;
            }

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    }

    document.querySelectorAll('.youtube-lite').forEach(function (wrapper) {
        const button = wrapper.querySelector('.youtube-lite-button');
        const src = wrapper.getAttribute('data-youtube-src') || '';
        const title = wrapper.getAttribute('data-youtube-title') || 'Video YouTube';

        if (!button || !src) return;

        button.addEventListener('click', function () {
            const iframe = document.createElement('iframe');
            const separator = src.indexOf('?') === -1 ? '?' : '&';

            iframe.src = src + separator + 'autoplay=1';
            iframe.title = title;
            iframe.loading = 'lazy';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.referrerPolicy = 'strict-origin-when-cross-origin';
            iframe.allowFullscreen = true;

            wrapper.replaceChildren(iframe);
            wrapper.classList.add('is-playing');
        });
    });

    const countElements = document.querySelectorAll('.count-up');

    if (countElements.length > 0) {
        const animateCount = function (element) {
            const target = parseInt(element.getAttribute('data-count'), 10) || 0;
            const duration = 900;
            const startTime = performance.now();

            const update = function (currentTime) {
                const progress = Math.min((currentTime - startTime) / duration, 1);
                const value = Math.floor(progress * target);

                element.textContent = value;

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    element.textContent = target;
                }
            };

            requestAnimationFrame(update);
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('has-counted')) {
                    entry.target.classList.add('has-counted');
                    animateCount(entry.target);
                }
            });
        }, {
            threshold: 0.6
        });

        countElements.forEach(function (element) {
            observer.observe(element);
        });
    }

    filterArticles();
});
