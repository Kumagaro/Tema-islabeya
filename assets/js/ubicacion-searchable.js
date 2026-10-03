// Función para escapar HTML
function escapeHtml(str) {
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// Función que transforma un select nativo en selector con búsqueda
function makeSearchableSelect(select) {
    if (select.hasAttribute("data-searchable-initialized")) return;

    var wrapper = document.createElement("div");
    wrapper.className = "searchable-select-wrapper";

    var trigger = document.createElement("div");
    trigger.className = "searchable-select-trigger";
    var selectedText = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : (select.options[0] ? select.options[0].text : "");
    trigger.innerHTML = '<span class="searchable-select-value">' + escapeHtml(selectedText) + '</span><span class="icon-arrow-down"></span>';

    var dropdown = document.createElement("div");
    dropdown.className = "searchable-select-dropdown";
    dropdown.style.display = "none";

    var searchBox = document.createElement("div");
    searchBox.className = "searchable-select-search";
    var searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "Buscar...";
    searchInput.className = "search-input";
    searchBox.appendChild(searchInput);

    var optionsContainer = document.createElement("div");
    optionsContainer.className = "searchable-select-options";

    function getCurrentOptions() {
        return Array.from(select.options).map(function(opt) {
            return { value: opt.value, text: opt.text, selected: opt.selected };
        });
    }

    var currentOptions = getCurrentOptions();

    function renderOptions(filterText) {
        filterText = filterText || "";
        optionsContainer.innerHTML = "";
        var filterLower = filterText.toLowerCase();
        var hasVisible = false;

        currentOptions.forEach(function(opt) {
            var matches = !filterText || opt.text.toLowerCase().includes(filterLower);
            if (!matches) return;
            hasVisible = true;

            var optionDiv = document.createElement("div");
            optionDiv.className = "searchable-option" + (opt.selected ? " selected" : "");
            optionDiv.setAttribute("data-value", opt.value);
            optionDiv.setAttribute("data-name", opt.text);
            optionDiv.textContent = opt.text;

            optionDiv.addEventListener("click", function(e) {
                e.stopPropagation();
                select.value = opt.value;
                select.dispatchEvent(new Event("change", { bubbles: true }));
                var valueSpan = trigger.querySelector(".searchable-select-value");
                if (valueSpan) valueSpan.textContent = opt.text;
                dropdown.style.display = "none";
                trigger.classList.remove("active");
                searchInput.value = "";
                renderOptions("");
                document.querySelectorAll(".searchable-option[data-value]").forEach(function(el) {
                    el.classList.remove("selected");
                });
                optionDiv.classList.add("selected");
            });

            optionsContainer.appendChild(optionDiv);
        });

        if (!hasVisible) {
            var noResults = document.createElement("div");
            noResults.className = "no-results";
            noResults.textContent = "No se encontraron resultados";
            optionsContainer.appendChild(noResults);
        }
    }

    function refreshOptions() {
        currentOptions = getCurrentOptions();
        renderOptions(searchInput.value);
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            var valueSpan = trigger.querySelector(".searchable-select-value");
            if (valueSpan) valueSpan.textContent = selectedOption.text;
        }
        optionsContainer.querySelectorAll(".searchable-option").forEach(function(opt) {
            if (opt.getAttribute("data-value") === select.value) {
                opt.classList.add("selected");
            } else {
                opt.classList.remove("selected");
            }
        });
    }

    select.refreshSearchableSelect = refreshOptions;

    trigger.addEventListener("click", function(e) {
        e.stopPropagation();
        var isActive = trigger.classList.contains("active");
        document.querySelectorAll(".searchable-select-trigger.active").forEach(function(t) {
            if (t !== trigger) {
                t.classList.remove("active");
                t.nextElementSibling.style.display = "none";
            }
        });
        if (isActive) {
            trigger.classList.remove("active");
            dropdown.style.display = "none";
            searchInput.value = "";
            renderOptions("");
        } else {
            trigger.classList.add("active");
            dropdown.style.display = "block";
            searchInput.focus();
            renderOptions("");
        }
    });

    searchInput.addEventListener("input", function() {
        renderOptions(searchInput.value);
    });

    searchInput.addEventListener("keydown", function(e) {
        var visibleOptions = Array.from(optionsContainer.querySelectorAll(".searchable-option:not(.no-results)"));
        if (visibleOptions.length === 0) return;

        var current = optionsContainer.querySelector(".searchable-option.highlighted");
        if (!current && visibleOptions.length) {
            current = visibleOptions[0];
            current.classList.add("highlighted");
        }

        if (e.key === "ArrowDown") {
            e.preventDefault();
            var next = current ? current.nextElementSibling : visibleOptions[0];
            while (next && !visibleOptions.includes(next)) next = next.nextElementSibling;
            if (!next && visibleOptions.length) next = visibleOptions[0];
            if (current) current.classList.remove("highlighted");
            if (next) {
                next.classList.add("highlighted");
                next.scrollIntoView({ block: "nearest" });
            }
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            var prev = current ? current.previousElementSibling : visibleOptions[visibleOptions.length - 1];
            while (prev && !visibleOptions.includes(prev)) prev = prev.previousElementSibling;
            if (!prev && visibleOptions.length) prev = visibleOptions[visibleOptions.length - 1];
            if (current) current.classList.remove("highlighted");
            if (prev) {
                prev.classList.add("highlighted");
                prev.scrollIntoView({ block: "nearest" });
            }
        } else if (e.key === "Enter") {
            e.preventDefault();
            var highlighted = optionsContainer.querySelector(".searchable-option.highlighted");
            if (highlighted) highlighted.click();
        } else if (e.key === "Escape") {
            trigger.classList.remove("active");
            dropdown.style.display = "none";
            searchInput.value = "";
            renderOptions("");
        }
    });

    document.addEventListener("click", function onClickOutside(e) {
        if (!wrapper.contains(e.target)) {
            if (trigger.classList.contains("active")) {
                trigger.classList.remove("active");
                dropdown.style.display = "none";
                searchInput.value = "";
                renderOptions("");
            }
        }
    });

    select.addEventListener("change", function() {
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            var valueSpan = trigger.querySelector(".searchable-select-value");
            if (valueSpan) valueSpan.textContent = selectedOption.text;
            optionsContainer.querySelectorAll(".searchable-option").forEach(function(opt) {
                if (opt.getAttribute("data-value") === select.value) {
                    opt.classList.add("selected");
                } else {
                    opt.classList.remove("selected");
                }
            });
        }
    });

    wrapper.appendChild(trigger);
    wrapper.appendChild(dropdown);
    dropdown.appendChild(searchBox);
    dropdown.appendChild(optionsContainer);

    select.style.display = "none";
    select.parentNode.insertBefore(wrapper, select.nextSibling);
    select.setAttribute("data-searchable-initialized", "true");

    renderOptions("");
}

// Inicializar todos los selects con clase js-searchable-select
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("select.js-searchable-select").forEach(function(select) {
        makeSearchableSelect(select);
    });
});