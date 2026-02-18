/**
 * Log Tracker - Main JavaScript
 */

(function() {
    'use strict';

    // ===========================
    // Theme Toggle
    // ===========================
    const themeToggle = document.getElementById('themeToggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Get saved theme or use system preference
    const currentTheme = localStorage.getItem('theme') || 
        (prefersDarkScheme.matches ? 'dark' : 'light');
    
    // Set initial theme
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    // Theme toggle handler
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    // ===========================
    // Mobile Menu Toggle
    // ===========================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navbarLinks = document.querySelector('.navbar-links');
    
    if (mobileMenuBtn && navbarLinks) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            navbarLinks.classList.toggle('active');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navbarLinks.classList.contains('active') && 
                !navbarLinks.contains(e.target) && 
                !mobileMenuBtn.contains(e.target)) {
                navbarLinks.classList.remove('active');
            }
        });
        
        // Close mobile menu when clicking a nav link
        navbarLinks.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navbarLinks.classList.remove('active');
            });
        });
    }

    // ===========================
    // Copy to Clipboard
    // ===========================
    const copyBtn = document.getElementById('copyBtn');
    const installCommand = document.getElementById('installCommand');
    
    if (copyBtn && installCommand) {
        copyBtn.addEventListener('click', async () => {
            const text = installCommand.textContent;
            
            try {
                await navigator.clipboard.writeText(text);
                
                // Visual feedback
                const originalHTML = copyBtn.innerHTML;
                copyBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                `;
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalHTML;
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        });
    }

    // ===========================
    // Smooth Scrolling for Anchor Links
    // ===========================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            // Skip if it's just "#" or empty
            if (!href || href === '#') return;
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===========================
    // Screenshots Slider
    // ===========================
    const screenshotsTrack = document.querySelector('.screenshots-track');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const dotsContainer = document.querySelector('.screenshots-dots');
    
    if (screenshotsTrack && prevBtn && nextBtn && dotsContainer) {
        const slides = screenshotsTrack.querySelectorAll('.screenshot-slide');
        let currentIndex = 0;
        let slidesToShow = getSlidesToShow();
        
        // Calculate slides to show based on window width
        function getSlidesToShow() {
            if (window.innerWidth >= 1024) return 3;
            if (window.innerWidth >= 768) return 2;
            return 1;
        }
        
        // Create dots
        const totalDots = Math.ceil(slides.length / slidesToShow);
        for (let i = 0; i < totalDots; i++) {
            const dot = document.createElement('button');
            dot.classList.add('screenshot-dot');
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }
        
        const dots = dotsContainer.querySelectorAll('.screenshot-dot');
        
        function updateSlider() {
            const slideWidth = slides[0].offsetWidth;
            const gap = parseFloat(getComputedStyle(screenshotsTrack).gap) || 0;
            const offset = currentIndex * (slideWidth + gap) * slidesToShow;
            
            screenshotsTrack.style.transform = `translateX(-${offset}px)`;
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
            
            // Update button states
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= totalDots - 1;
        }
        
        function goToSlide(index) {
            currentIndex = Math.max(0, Math.min(index, totalDots - 1));
            updateSlider();
        }
        
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
        
        nextBtn.addEventListener('click', () => {
            if (currentIndex < totalDots - 1) {
                currentIndex++;
                updateSlider();
            }
        });
        
        // Auto-play slider
        let autoplayInterval = setInterval(() => {
            if (currentIndex < totalDots - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSlider();
        }, 5000);
        
        // Pause autoplay on hover
        screenshotsTrack.addEventListener('mouseenter', () => {
            clearInterval(autoplayInterval);
        });
        
        screenshotsTrack.addEventListener('mouseleave', () => {
            autoplayInterval = setInterval(() => {
                if (currentIndex < totalDots - 1) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateSlider();
            }, 5000);
        });
        
        // Update on window resize
        window.addEventListener('resize', () => {
            const newSlidesToShow = getSlidesToShow();
            if (newSlidesToShow !== slidesToShow) {
                slidesToShow = newSlidesToShow;
                currentIndex = 0;
                
                // Recreate dots
                dotsContainer.innerHTML = '';
                const newTotalDots = Math.ceil(slides.length / slidesToShow);
                for (let i = 0; i < newTotalDots; i++) {
                    const dot = document.createElement('button');
                    dot.classList.add('screenshot-dot');
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                    if (i === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                }
                
                updateSlider();
            }
        });
        
        updateSlider();
    }

    // ===========================
    // Layout Tabs
    // ===========================
    const layoutTabs = document.querySelectorAll('.layout-tab');
    const layoutPreviews = document.querySelectorAll('.layout-preview');
    
    layoutTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const layout = tab.getAttribute('data-layout');
            
            // Update active tab
            layoutTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update active preview
            layoutPreviews.forEach(preview => {
                preview.classList.remove('active');
                if (preview.id === `layout-${layout}`) {
                    preview.classList.add('active');
                }
            });
        });
    });

    // ===========================
    // FAQ Accordion
    // ===========================
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const isExpanded = question.getAttribute('aria-expanded') === 'true';
            
            // Close all other FAQs
            faqQuestions.forEach(q => {
                q.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle current FAQ
            question.setAttribute('aria-expanded', !isExpanded);
        });
    });

    // ===========================
    // Lightbox
    // ===========================
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxClose = document.querySelector('.lightbox-close');
    const lightboxPrev = document.querySelector('.lightbox-prev');
    const lightboxNext = document.querySelector('.lightbox-next');
    
    // Get all clickable screenshots
    const screenshotCards = document.querySelectorAll('.screenshot-card, .doc-screenshot');
    const screenshots = Array.from(screenshotCards).map(card => {
        const img = card.querySelector('img');
        const label = card.querySelector('.screenshot-label, .doc-screenshot-label');
        return {
            src: img ? img.src : '',
            alt: img ? img.alt : '',
            caption: label ? label.textContent : ''
        };
    });
    
    let currentLightboxIndex = 0;
    
    function openLightbox(index) {
        currentLightboxIndex = index;
        const screenshot = screenshots[index];
        
        lightboxImage.src = screenshot.src;
        lightboxImage.alt = screenshot.alt;
        lightboxCaption.textContent = screenshot.caption;
        
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function showPrevious() {
        currentLightboxIndex = (currentLightboxIndex - 1 + screenshots.length) % screenshots.length;
        const screenshot = screenshots[currentLightboxIndex];
        lightboxImage.src = screenshot.src;
        lightboxImage.alt = screenshot.alt;
        lightboxCaption.textContent = screenshot.caption;
    }
    
    function showNext() {
        currentLightboxIndex = (currentLightboxIndex + 1) % screenshots.length;
        const screenshot = screenshots[currentLightboxIndex];
        lightboxImage.src = screenshot.src;
        lightboxImage.alt = screenshot.alt;
        lightboxCaption.textContent = screenshot.caption;
    }
    
    // Add click handlers to screenshots
    screenshotCards.forEach((card, index) => {
        card.addEventListener('click', () => openLightbox(index));
        card.style.cursor = 'pointer';
    });
    
    // Lightbox controls
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', showPrevious);
    }
    
    if (lightboxNext) {
        lightboxNext.addEventListener('click', showNext);
    }
    
    // Close on overlay click
    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox || !lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            showPrevious();
        } else if (e.key === 'ArrowRight') {
            showNext();
        }
    });

    // ===========================
    // Fetch Live Package Stats from Packagist
    // ===========================
    const statsCards = document.querySelectorAll('.stat-card');
    
    if (statsCards.length > 0) {
        // Show loading spinner initially
        statsCards.forEach(card => {
            const numberElement = card.querySelector('.stat-number');
            if (numberElement) {
                numberElement.innerHTML = '<div class="stat-spinner"></div>';
            }
        });

        async function fetchPackageStats() {
            try {
                const response = await fetch('https://packagist.org/packages/kssadi/log-tracker/stats.json');
                const data = await response.json();
                
                // Update stats with live data
                const statsData = [
                    { value: data.downloads.total.toLocaleString() + '+', selector: 0 },
                    { value: data.downloads.monthly.toLocaleString(), selector: 1 },
                    { value: data.downloads.daily.toLocaleString(), selector: 2 },
                    { value: data.versions.length.toLocaleString(), selector: 3 }
                ];
                
                statsData.forEach(stat => {
                    const card = statsCards[stat.selector];
                    if (card) {
                        const numberElement = card.querySelector('.stat-number');
                        if (numberElement) {
                            // Remove spinner and show number
                            numberElement.innerHTML = stat.value;
                        }
                    }
                });
                
            } catch (error) {
                console.log('Using static fallback stats');
                // Show fallback numbers
                const fallbackData = [
                    { value: '1,837+', selector: 0 },
                    { value: '723', selector: 1 },
                    { value: '13', selector: 2 },
                    { value: '9', selector: 3 }
                ];
                
                fallbackData.forEach(stat => {
                    const card = statsCards[stat.selector];
                    if (card) {
                        const numberElement = card.querySelector('.stat-number');
                        if (numberElement) {
                            numberElement.innerHTML = stat.value;
                        }
                    }
                });
            }
        }
        
        // Fetch stats on page load
        fetchPackageStats();
        
        // Optionally refresh every 5 minutes
        setInterval(fetchPackageStats, 5 * 60 * 1000);
    }

    // ===========================
    // Syntax Highlighting
    // ===========================
    if (typeof hljs !== 'undefined') {
        hljs.highlightAll();
    }

    // ===========================
    // Documentation Navigation
    // ===========================
    const docNavLinks = document.querySelectorAll('.doc-nav-link');
    
    // Update active link on scroll
    if (docNavLinks.length > 0) {
        const sections = Array.from(docNavLinks)
            .map(link => {
                const href = link.getAttribute('href');
                if (href && href.startsWith('#')) {
                    return document.querySelector(href);
                }
                return null;
            })
            .filter(section => section !== null);
        
        function updateActiveNavLink() {
            const scrollPosition = window.scrollY + 100;
            
            let currentSection = null;
            sections.forEach(section => {
                if (section.offsetTop <= scrollPosition) {
                    currentSection = section;
                }
            });
            
            docNavLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (currentSection && href === `#${currentSection.id}`) {
                    link.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', updateActiveNavLink);
        updateActiveNavLink();
    }

    // ===========================
    // Spotlight Search (Documentation)
    // ===========================
    const spotlightOverlay = document.getElementById('spotlightOverlay');
    const spotlightContainer = document.getElementById('spotlightContainer');
    const spotlightSearch = document.getElementById('spotlightSearch');
    const spotlightResults = document.getElementById('spotlightResults');
    const spotlightShortcut = document.getElementById('spotlightShortcut');
    
    if (spotlightOverlay && spotlightContainer && spotlightSearch && spotlightResults) {
        // Show shortcut hint after a delay
        setTimeout(() => {
            if (spotlightShortcut) {
                spotlightShortcut.classList.add('show');
                setTimeout(() => {
                    spotlightShortcut.classList.remove('show');
                }, 3000);
            }
        }, 2000);
        
        // Open spotlight with Cmd+K or Ctrl+K
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                openSpotlight();
            }
            
            if (e.key === 'Escape') {
                closeSpotlight();
            }
        });
        
        function openSpotlight() {
            spotlightOverlay.classList.add('active');
            spotlightContainer.classList.add('active');
            spotlightSearch.focus();
        }
        
        function closeSpotlight() {
            spotlightOverlay.classList.remove('active');
            spotlightContainer.classList.remove('active');
            spotlightSearch.value = '';
            spotlightResults.innerHTML = '';
        }
        
        spotlightOverlay.addEventListener('click', closeSpotlight);
        
        // Search functionality
        const searchableContent = Array.from(document.querySelectorAll('.doc-section')).map(section => ({
            id: section.id,
            title: section.querySelector('.doc-section-title')?.textContent || '',
            content: section.textContent || ''
        }));
        
        spotlightSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            if (!query) {
                spotlightResults.innerHTML = '';
                return;
            }
            
            const results = searchableContent.filter(item => 
                item.title.toLowerCase().includes(query) || 
                item.content.toLowerCase().includes(query)
            ).slice(0, 5);
            
            if (results.length === 0) {
                spotlightResults.innerHTML = '<div class="spotlight-no-results">No results found</div>';
                return;
            }
            
            spotlightResults.innerHTML = results.map((result, index) => `
                <div class="spotlight-result ${index === 0 ? 'active' : ''}" data-href="#${result.id}">
                    <svg class="spotlight-result-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <div class="spotlight-result-content">
                        <div class="spotlight-result-title">${result.title}</div>
                        <div class="spotlight-result-description">${result.content.substring(0, 100)}...</div>
                    </div>
                </div>
            `).join('');
            
            // Add click handlers
            document.querySelectorAll('.spotlight-result').forEach(resultEl => {
                resultEl.addEventListener('click', () => {
                    const href = resultEl.getAttribute('data-href');
                    const target = document.querySelector(href);
                    if (target) {
                        closeSpotlight();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
        
        // Keyboard navigation in results
        spotlightSearch.addEventListener('keydown', (e) => {
            const activeResult = spotlightResults.querySelector('.spotlight-result.active');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (activeResult && activeResult.nextElementSibling) {
                    activeResult.classList.remove('active');
                    activeResult.nextElementSibling.classList.add('active');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (activeResult && activeResult.previousElementSibling) {
                    activeResult.classList.remove('active');
                    activeResult.previousElementSibling.classList.add('active');
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeResult) {
                    activeResult.click();
                }
            }
        });
    }

    // ===========================
    // Code Copy Buttons (Documentation)
    // ===========================
    document.querySelectorAll('.doc-code-copy').forEach(btn => {
        btn.addEventListener('click', async () => {
            const codeBlock = btn.closest('.doc-code-block');
            const code = codeBlock.querySelector('code');
            
            if (code) {
                try {
                    await navigator.clipboard.writeText(code.textContent);
                    const originalText = btn.textContent;
                    btn.textContent = 'Copied!';
                    setTimeout(() => {
                        btn.textContent = originalText;
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
            }
        });
    });

})();
