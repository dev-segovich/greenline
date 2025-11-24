// Detectar idioma del navegador
let userLang = navigator.language.slice(0, 2);

// Soportamos solo EN y ES
const supported = ["en", "es"];
if (!supported.includes(userLang)) userLang = "en";

// Idioma por defecto
let currentLang = localStorage.getItem("language") || userLang;

const langSwitcher = document.getElementById("langSwitcher");
if (langSwitcher) langSwitcher.value = currentLang;

// Función para cargar JSON del idioma
async function loadLanguage(lang) {
  try {
    const response = await fetch(`./locales/${lang}.json`);
    const translations = await response.json();
    applyTranslations(translations);
  } catch (error) {
    console.error("Error loading language:", lang, error);
  }
}

// Aplicar traducciones a los elementos HTML
function applyTranslations(t) {
  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const key = el.getAttribute("data-i18n");
    const text = key.split(".").reduce((o, i) => o?.[i], t);
    if (text) el.textContent = text;
  });

  // Para placeholders
  document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
    const key = el.getAttribute("data-i18n-placeholder");
    const text = key.split(".").reduce((o, i) => o?.[i], t);
    if (text) el.placeholder = text;
  });

  // Actualizar <title> y <meta>
  if (t.meta?.title) document.title = t.meta.title;

  const metaDesc = document.querySelector("meta[name='description']");
  if (metaDesc && t.meta?.description) {
    metaDesc.setAttribute("content", t.meta.description);
  }
}

// Cambiar idioma manualmente
if (langSwitcher) {
  langSwitcher.addEventListener("change", (e) => {
    const selected = e.target.value;
    localStorage.setItem("language", selected);
    loadLanguage(selected);
  });
}

// Cargar idioma al iniciar
loadLanguage(currentLang);

// Año del footer
document.getElementById("year").textContent = new Date().getFullYear();

// LANG SWITCHER pill buttons
document.querySelectorAll(".lang-btn").forEach(btn => {
  if (btn.dataset.lang === currentLang) {
    btn.classList.add("active");
  }

  btn.addEventListener("click", () => {
    const selected = btn.dataset.lang;

    // UI update
    document.querySelectorAll(".lang-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    // Save and load
    localStorage.setItem("language", selected);
    loadLanguage(selected);
  });
});
