// Loading screen - fade out when page loads
window.addEventListener('load', () => {
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.classList.add('fade-out');
        }, 800); // Kort tonen van logo
    }
});

// ===== Founders slider op About-pagina =====
document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.founders-slide');
    const buttons = document.querySelectorAll('.founder-toggle-btn');

    if (!slides.length || !buttons.length) return;

    const founders = ['edmilson', 'george'];
    let currentIndex = 0;

    function showFounder(index) {
        const targetKey = founders[index];
        slides.forEach(slide => {
            slide.classList.toggle('active', slide.dataset.founder === targetKey);
        });
        buttons.forEach((btn, idx) => {
            btn.classList.toggle('active', idx === index);
        });
    }

    buttons.forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            if (idx === 0) {
                // Links pijl: vorige
                currentIndex = (currentIndex - 1 + founders.length) % founders.length;
            } else {
                // Rechts pijl: volgende
                currentIndex = (currentIndex + 1) % founders.length;
            }
            showFounder(currentIndex);
        });
    });
});

const sakuraContainer = document.getElementById('sakura-container');

function createSakura() {
    // Check if container exists (not all pages have sakura)
    if (!sakuraContainer) return;

    const sakura = document.createElement('div');
    sakura.classList.add('sakura');

    // Willekeurige positie
    sakura.style.left = Math.random() * 100 + 'vw';

    // Willekeurige grootte
    const size = Math.random() * 10 + 5 + 'px';
    sakura.style.width = size;
    sakura.style.height = size;

    // Valtijd
    sakura.style.animationDuration = Math.random() * 5 + 3 + 's';

    sakuraContainer.appendChild(sakura);

    // Opruimen
    setTimeout(() => {
        sakura.remove();
    }, 8000);
}

// Maak elke 300ms een nieuw blaadje (alleen als container bestaat)
if (sakuraContainer) {
    setInterval(createSakura, 300);
}

// Account icon verbergen tijdens scroll
const accountIcon = document.querySelector('.account-icon');
if (accountIcon) {
    window.addEventListener('scroll', () => {
        // Verberg icoon na klein stukje scrollen
        if (window.scrollY > 200) {
            accountIcon.classList.add('hidden');
        } else {
            accountIcon.classList.remove('hidden');
        }
    });
}

// Hamburger menu functionaliteit
const hamburger = document.getElementById('hamburger');
const menuOverlay = document.getElementById('menuOverlay');
const menuClose = document.querySelector('.menu-close');
const menuLinks = document.querySelectorAll('.menu-link');

// Open menu met animatie
hamburger.addEventListener('click', () => {
    menuOverlay.classList.add('active');
    hamburger.classList.add('active');
    document.body.style.overflow = 'hidden'; // Voorkom scrollen
});

// Sluit menu met close button
menuClose.addEventListener('click', (e) => {
    e.preventDefault();
    menuOverlay.classList.remove('active');
    hamburger.classList.remove('active');
    document.body.style.overflow = ''; // Sta scrollen weer toe
});

// Sluit menu bij klik op link met fade-out animatie
menuLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');

        // Als het een anchor link is (#) op dezelfde pagina
        if (href.startsWith('#') && href !== '#') {
            e.preventDefault();

            // Sluit menu met animatie
            menuOverlay.classList.remove('active');
            hamburger.classList.remove('active');
            document.body.style.overflow = '';

            // Scroll naar sectie na animatie
            setTimeout(() => {
                const section = document.querySelector(href);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            }, 600);
        }
        // Voor alle externe links en # home link
        else {
            e.preventDefault();

            // Smooth page transition - fade out everything
            const menuLinks = document.querySelectorAll('.menu-link');
            const menuLogo = document.querySelector('.menu-logo');
            const menuIcon = document.querySelector('.menu-icon');
            const menuImage = document.querySelector('.menu-image');
            const menuClose = document.querySelector('.menu-close');
            const menuFooter = document.querySelector('.menu-footer');

            // Fade out alle elementen
            menuLinks.forEach(l => {
                l.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                l.style.opacity = '0';
                l.style.transform = 'translateY(-20px)';
            });

            if (menuLogo) menuLogo.style.opacity = '0';
            if (menuIcon) menuIcon.style.opacity = '0';
            if (menuImage) {
                menuImage.style.transition = 'opacity 0.4s ease';
                menuImage.style.opacity = '0';
            }
            if (menuClose) menuClose.style.opacity = '0';
            if (menuFooter) menuFooter.style.opacity = '0';

            // Verander achtergrond naar zwart voor smooth transition
            setTimeout(() => {
                menuOverlay.style.transition = 'background 0.4s ease';
                menuOverlay.style.background = '#000000';
            }, 200);

            // Sla huidige URL op in sessionStorage
            sessionStorage.setItem('showLoading', 'true');

            // Navigeer naar nieuwe pagina
            setTimeout(() => {
                window.location.href = href;
            }, 600);
        }
    });
});

// Video scroll effect
const videoSection = document.getElementById('videoScrollSection');
const videoContainer = document.querySelector('.video-container-scroll');
const scrollVideo = document.getElementById('scrollVideo');

if (videoSection && videoContainer && scrollVideo) {
    // Start video when it comes into view
    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                scrollVideo.play();
            } else {
                scrollVideo.pause();
            }
        });
    }, { threshold: 0.5 });

    videoObserver.observe(videoSection);

    // Scroll effect - video groeit naar fullscreen
    window.addEventListener('scroll', () => {
        const rect = videoSection.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        // Bereken hoeveel de sectie in beeld is (0 tot 1)
        let progress = 0;

        if (rect.top < windowHeight && rect.bottom > 0) {
            // Sectie is zichtbaar
            const visibleHeight = Math.min(rect.bottom, windowHeight) - Math.max(rect.top, 0);
            progress = visibleHeight / windowHeight;
            progress = Math.max(0, Math.min(1, progress));
        }

        // Schaal video van 60% naar 100%
        const minScale = 0.6;
        const maxScale = 1.0;
        const currentScale = minScale + (progress * (maxScale - minScale));

        videoContainer.style.width = `${currentScale * 100}%`;
        videoContainer.style.height = `${currentScale * 100}%`;
        videoContainer.style.borderRadius = `${(1 - progress) * 20}px`;
    });
}

// Scroll-triggered animations (Takumi-style)
const observerOptions = {
    // Lager threshold + minder negatieve rootMargin = eerder animeren bij scroll
    threshold: 0.05,
    rootMargin: '0px 0px -40px 0px'
};

const fadeInObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-active');
        }
    });
}, observerOptions);

// Observeer alle secties
document.querySelectorAll('.featured-section, .about-section, .order-section, .location-section').forEach(section => {
    section.classList.add('fade-in-section');
    fadeInObserver.observe(section);
});

// Observeer featured items met stagger
document.querySelectorAll('.featured-item').forEach((item, index) => {
    item.classList.add('fade-in-item');
    item.style.transitionDelay = `${index * 0.2}s`;
    fadeInObserver.observe(item);
});

// Observeer order steps met stagger
document.querySelectorAll('.step').forEach((step, index) => {
    step.classList.add('fade-in-item');
    step.style.transitionDelay = `${index * 0.15}s`;
    fadeInObserver.observe(step);
});

// Parallax effect voor achtergrond elementen
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;

    // Parallax voor hero section
    const hero = document.querySelector('.hero');
    if (hero) {
        const heroKanji = document.querySelector('.bg-kanji');
        const blueGlow = document.querySelector('.blue-glow');

        if (heroKanji) {
            heroKanji.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
        if (blueGlow) {
            blueGlow.style.transform = `translateY(${scrolled * 0.3}px)`;
        }
    }
});

// ===== ANIME.JS SCROLL ANIMATIES =====
// Wacht tot Anime.js geladen is
window.addEventListener('load', () => {
    if (typeof anime === 'undefined') return;

    // Functie om te checken of element in viewport is
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.85 &&
            rect.bottom >= 0
        );
    }

    // Houd bij welke elementen al geanimeerd zijn
    const animatedElements = new Set();

    // Scroll event handler
    function handleScrollAnimation() {
        // Animeer section titles
        document.querySelectorAll('.section-title').forEach(title => {
            if (isInViewport(title) && !animatedElements.has(title)) {
                animatedElements.add(title);

                // Animeer solid en outline text apart
                const solidText = title.querySelector('.solid-text');
                const outlineText = title.querySelector('.outline-text');

                if (solidText) {
                    anime({
                        targets: solidText,
                        translateX: [-50, 0],
                        opacity: [0, 1],
                        duration: 1200,
                        easing: 'easeOutExpo'
                    });
                }

                if (outlineText) {
                    anime({
                        targets: outlineText,
                        translateX: [50, 0],
                        opacity: [0, 1],
                        duration: 1200,
                        delay: 200,
                        easing: 'easeOutExpo'
                    });
                }
            }
        });

        // Animeer section labels (Japanse tekst)
        document.querySelectorAll('.section-label').forEach(label => {
            if (isInViewport(label) && !animatedElements.has(label)) {
                animatedElements.add(label);

                anime({
                    targets: label,
                    translateY: [-20, 0],
                    opacity: [0, 1],
                    duration: 800,
                    easing: 'easeOutQuad'
                });
            }
        });

        // Animeer paragraphs
        document.querySelectorAll('.about-text, .order-subtitle').forEach(text => {
            if (isInViewport(text) && !animatedElements.has(text)) {
                animatedElements.add(text);

                anime({
                    targets: text,
                    translateY: [30, 0],
                    opacity: [0, 1],
                    duration: 1000,
                    delay: 400,
                    easing: 'easeOutExpo'
                });
            }
        });

        // Animeer featured item titles
        document.querySelectorAll('.featured-info h3').forEach((title, index) => {
            if (isInViewport(title) && !animatedElements.has(title)) {
                animatedElements.add(title);

                anime({
                    targets: title,
                    translateY: [20, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: index * 100,
                    easing: 'easeOutQuad'
                });
            }
        });

        // Animeer step numbers
        document.querySelectorAll('.step-number').forEach((number, index) => {
            if (isInViewport(number) && !animatedElements.has(number)) {
                animatedElements.add(number);

                anime({
                    targets: number,
                    scale: [0.5, 1],
                    opacity: [0, 1],
                    duration: 600,
                    delay: index * 150,
                    easing: 'easeOutBack'
                });
            }
        });

        // Animeer step titles en descriptions
        document.querySelectorAll('.step h3, .step p').forEach((text, index) => {
            if (isInViewport(text) && !animatedElements.has(text)) {
                animatedElements.add(text);

                anime({
                    targets: text,
                    translateX: [-30, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: 200 + (index * 100),
                    easing: 'easeOutExpo'
                });
            }
        });
    }

    // Run op scroll
    window.addEventListener('scroll', handleScrollAnimation);

    // Run ook meteen bij laden
    handleScrollAnimation();
});