document.addEventListener("DOMContentLoaded", () => {
	const preferredOption = document.getElementById("preferredOption");
	const previewImage = document.getElementById("previewImage");
	const placeholderText = document.getElementById("placeholderText");

	const images = {
		A: "../img/a.jpeg",
		B: "../img/b.jpeg",
		C: "../img/c.jpeg",
		D: "../img/d.jpeg",
	};

	// --- LÓGICA DE IDIOMA ---
	let currentLang = localStorage.getItem("language") || "en";

	async function loadLanguage(lang) {
		console.log("Loading language:", lang);
		try {
			// Usamos la ruta relativa correcta y un timestamp para evitar caché
			const response = await fetch(`../locales/${lang}.json?t=${Date.now()}`);
			if (!response.ok)
				throw new Error(`HTTP error! status: ${response.status}`);

			const translations = await response.json();
			console.log("Translations data from JSON:", translations);

			applyTranslations(translations);
			updateLangSwitcherUI(lang);
		} catch (error) {
			console.error("Error loading language:", lang, error);
			// Si falla la carga desde locales del padre, intentamos carga directa (por si acaso)
			if (lang === "es") {
				// Fallback directo para testeo si el fetch falla
				console.log("Attempting fallback for ES");
			}
		}
	}

	function applyTranslations(t) {
		// Traducir todos los elementos con [data-i18n]
		document.querySelectorAll("[data-i18n]").forEach((el) => {
			const key = el.getAttribute("data-i18n");
			// Navegar por el objeto JSON usando el punto (ej: "apply.fields.first_name")
			const text = key.split(".").reduce((o, i) => o?.[i], t);

			if (text) {
				// Si es un botón o un span/div/label, cambiamos textContent
				// No tocamos 'value' de inputs de texto (esos se manejan por placeholder)
				if (
					el.tagName === "INPUT" &&
					(el.type === "submit" || el.type === "button")
				) {
					el.value = text;
				} else {
					el.textContent = text;
				}
			} else {
				console.warn("Translation key not found:", key);
			}
		});

		// Traducir todos los placeholders
		document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
			const key = el.getAttribute("data-i18n-placeholder");
			const text = key.split(".").reduce((o, i) => o?.[i], t);
			if (text) {
				el.placeholder = text;
			}
		});

		// Título de la pestaña
		if (t.meta && t.meta.title) {
			document.title = t.meta.title;
		}
	}

	function updateLangSwitcherUI(lang) {
		document.querySelectorAll(".lang-btn").forEach((btn) => {
			if (btn.dataset.lang === lang) {
				btn.classList.add("active");
			} else {
				btn.classList.remove("active");
			}
		});
	}

	// Configurar clics en los botones de idioma
	document.querySelectorAll(".lang-btn").forEach((btn) => {
		btn.addEventListener("click", function (e) {
			e.preventDefault();
			const selected = this.dataset.lang;
			console.log("Language clicked:", selected);

			if (selected !== currentLang) {
				currentLang = selected;
				localStorage.setItem("language", selected);
				loadLanguage(selected);
			}
		});
	});

	// Carga inicial obligatoria
	loadLanguage(currentLang);

	// --- LÓGICA DE CUSTOM SELECTS ---
	function initCustomSelects() {
		const selects = document.querySelectorAll(".gl-custom-select");

		selects.forEach((select) => {
			const trigger = select.querySelector(".gl-select-trigger");
			const optionsContainer = select.querySelector(".gl-select-options");
			const options = select.querySelectorAll(".gl-select-option");
			const hiddenSelect = select.querySelector("select");
			const labelSpan = trigger.querySelector(".gl-select-value");

			// Toggle dropdown
			trigger.addEventListener("click", (e) => {
				e.stopPropagation();
				// Close others
				document.querySelectorAll(".gl-custom-select").forEach((s) => {
					if (s !== select) s.classList.remove("active");
				});
				select.classList.toggle("active");
			});

			// Handle option click
			options.forEach((opt) => {
				opt.addEventListener("click", (e) => {
					e.stopPropagation();
					if (opt.classList.contains("disabled")) return;

					const value = opt.dataset.value;
					const text = opt.textContent;

					// Update hidden select
					hiddenSelect.value = value;
					hiddenSelect.dispatchEvent(new Event("change"));

					// Update UI
					labelSpan.textContent = text;
					// Si tiene traducción directa, la guardamos para que i18n no la pise mal
					const i18nKey = opt.getAttribute("data-i18n");
					if (i18nKey) {
						labelSpan.setAttribute("data-i18n", i18nKey);
					}

					options.forEach((o) => o.classList.remove("selected"));
					opt.classList.add("selected");

					select.classList.remove("active");
				});
			});
		});

		// Close when clicking outside
		document.addEventListener("click", () => {
			document.querySelectorAll(".gl-custom-select").forEach((s) => {
				s.classList.remove("active");
			});
		});
	}

	initCustomSelects();

	// --- LÓGICA DE PREVISUALIZACIÓN ---
	if (preferredOption) {
		preferredOption.addEventListener("change", (e) => {
			const val = e.target.value;
			if (images[val]) {
				previewImage.src = images[val];
				previewImage.classList.add("visible");
				if (placeholderText) placeholderText.style.display = "none";
			} else {
				previewImage.classList.remove("visible");
				if (placeholderText) placeholderText.style.display = "block";
			}
		});
	}

	// Fecha mínima para el input de fecha
	const moveInDateInput = document.getElementById("moveInDate");
	if (moveInDateInput) {
		const today = new Date().toISOString().split("T")[0];
		moveInDateInput.setAttribute("min", today);
	}
});
