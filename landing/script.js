// Set current year in footer
document.addEventListener("DOMContentLoaded", () => {
  const yearElement = document.getElementById("year");
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }

  // Smooth scroll for anchor links
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      const href = link.getAttribute("href");
      if (href !== "#") {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });

  // Mobile menu toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const nav = document.querySelector(".nav");

  if (mobileMenuToggle && nav) {
    mobileMenuToggle.addEventListener("click", () => {
      nav.classList.toggle("active");
      mobileMenuToggle.classList.toggle("active");

      // Animate hamburger icon
      const spans = mobileMenuToggle.querySelectorAll("span");
      if (mobileMenuToggle.classList.contains("active")) {
        spans[0].style.transform = "rotate(45deg) translateY(10px)";
        spans[1].style.opacity = "0";
        spans[2].style.transform = "rotate(-45deg) translateY(-10px)";
      } else {
        spans[0].style.transform = "none";
        spans[1].style.opacity = "1";
        spans[2].style.transform = "none";
      }
    });

    // Close mobile menu when clicking on a link
    const navLinks = nav.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        nav.classList.remove("active");
        mobileMenuToggle.classList.remove("active");
        const spans = mobileMenuToggle.querySelectorAll("span");
        spans[0].style.transform = "none";
        spans[1].style.opacity = "1";
        spans[2].style.transform = "none";
      });
    });
  }

  // Intersection Observer for fade-in animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1";
        entry.target.style.transform = "translateY(0)";
      }
    });
  }, observerOptions);

  // Observe all feature cards, benefit cards, and app items
  const animatedElements = document.querySelectorAll(
    ".feature-card, .benefit-card, .app-item"
  );

  animatedElements.forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(30px)";
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(el);
  });

  // Parallax effect on scroll for hero visual cards
  let ticking = false;

  window.addEventListener("scroll", () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const scrolled = window.pageYOffset;
        const visualCards = document.querySelectorAll(".visual-card");

        visualCards.forEach((card, index) => {
          const speed = 0.1 + index * 0.05;
          const yPos = -(scrolled * speed);
          card.style.transform = `translateY(${yPos}px)`;
        });

        ticking = false;
      });

      ticking = true;
    }
  });

  // Add hover effect sound feedback (optional - visual feedback)
  const buttons = document.querySelectorAll(
    ".btn-primary, .btn-hero, .btn-secondary, .btn-outline"
  );

  buttons.forEach((btn) => {
    btn.addEventListener("mouseenter", () => {
      btn.style.transition = "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)";
    });
  });

  // CTA buttons click handlers (placeholder)
  const ctaButtons = document.querySelectorAll(
    '[class*="btn-"]:not(.mobile-menu-toggle)'
  );

  ctaButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      // Check if it's not an anchor link
      if (!btn.getAttribute("href")) {
        e.preventDefault();
        console.log("CTA Button clicked:", btn.textContent.trim());

        // Add a subtle click animation
        btn.style.transform = "scale(0.95)";
        setTimeout(() => {
          btn.style.transform = "";
        }, 150);

        // Here you would typically trigger a modal, form, or navigation
        // For now, showing an alert as a placeholder
        alert(
          `¡Gracias por tu interés! La funcionalidad "${btn.textContent.trim()}" estará disponible próximamente.`
        );
      }
    });
  });

  // Header scroll effect
  const header = document.querySelector(".header");
  let lastScroll = 0;

  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
      header.style.boxShadow = "0 4px 20px rgba(0, 0, 0, 0.5)";
    } else {
      header.style.boxShadow = "none";
    }

    lastScroll = currentScroll;
  });

  // Add stagger animation to feature cards
  const featureCards = document.querySelectorAll(".feature-card");
  featureCards.forEach((card, index) => {
    card.style.transitionDelay = `${index * 0.1}s`;
  });

  // Add stagger animation to benefit cards
  const benefitCards = document.querySelectorAll(".benefit-card");
  benefitCards.forEach((card, index) => {
    card.style.transitionDelay = `${index * 0.08}s`;
  });

  // Console log for developers
  console.log("%c🏢 Greenline Estate Management", "color: #33c27f; font-size: 20px; font-weight: bold;");
  console.log("%cLanding page loaded successfully", "color: #9ca3af; font-size: 12px;");
});

// Add resize handler for responsive adjustments
let resizeTimer;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    // Reset mobile menu on desktop
    if (window.innerWidth > 768) {
      const nav = document.querySelector(".nav");
      const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");

      if (nav && nav.classList.contains("active")) {
        nav.classList.remove("active");
      }

      if (mobileMenuToggle && mobileMenuToggle.classList.contains("active")) {
        mobileMenuToggle.classList.remove("active");
        const spans = mobileMenuToggle.querySelectorAll("span");
        spans[0].style.transform = "none";
        spans[1].style.opacity = "1";
        spans[2].style.transform = "none";
      }
    }
  }, 250);
});
