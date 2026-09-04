document.addEventListener("DOMContentLoaded", () => {
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion) {
    return;
  }

  document.body.classList.add("motion-ready");

  const revealSelectors = [
    ".feature-card",
    ".fixed-room-card",
    ".room-detail-card",
    ".service-card",
    ".service-feature",
    ".restaurant-card",
    ".policy-row",
    ".faq-item",
    ".search-link-card",
    ".search-room-card",
    ".review-card",
    ".review-featured-card",
    ".claim-form-card",
    ".claim-confirm-card",
    ".contact-card",
    ".gallery-thumb",
    ".footer-grid > div",
    ".account-card"
  ];

  const revealItems = Array.from(document.querySelectorAll(revealSelectors.join(",")));

  revealItems.forEach((item, index) => {
    if (!item.dataset.animate) {
      item.dataset.animate = "fade-up";
    }

    item.style.setProperty("--motion-delay", `${Math.min(index % 8, 7) * 65}ms`);
  });

  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        revealObserver.unobserve(entry.target);
      });
    },
    {
      threshold: 0.14,
      rootMargin: "0px 0px -8% 0px"
    }
  );

  document.querySelectorAll("[data-animate]").forEach((item) => revealObserver.observe(item));

  const navbar = document.querySelector(".navbar");
  const syncNavbar = () => {
    if (navbar) {
      navbar.classList.toggle("navbar-scrolled", window.scrollY > 18);
    }
  };

  syncNavbar();
  window.addEventListener("scroll", syncNavbar, { passive: true });

  const pressableSelectors = [
    ".btn",
    "button",
    ".fixed-room-action a",
    ".claim-submit",
    ".search-link-card",
    ".nav-search-toggle",
    ".floating-whatsapp"
  ];

  document.querySelectorAll(pressableSelectors.join(",")).forEach((element) => {
    element.classList.add("motion-pressable");

    element.addEventListener("pointerdown", (event) => {
      const rect = element.getBoundingClientRect();
      element.style.setProperty("--press-x", `${event.clientX - rect.left}px`);
      element.style.setProperty("--press-y", `${event.clientY - rect.top}px`);
      element.classList.remove("is-pressing");
      void element.offsetWidth;
      element.classList.add("is-pressing");
    });

    element.addEventListener("animationend", () => {
      element.classList.remove("is-pressing");
    });
  });
});
