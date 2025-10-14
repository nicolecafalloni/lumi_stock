document.addEventListener("DOMContentLoaded", () => {
    // A. Resolve URLs relativas usando um elemento <a> e extrai o nome do arquivo (último segmento)
    function getFilenameFromUrl(url) {
        const a = document.createElement("a");
        a.href = url; // browser resolves relative -> absolute

        let pathname = a.pathname || "";
        pathname = pathname.split("?")[0].split("#")[0];

        const segments = pathname.split("/").filter((s) => s.length > 0);
        if (segments.length === 0) {
            // se for raiz, assumimos index.php (ajuste se a home for outro arquivo)
            return "index.php";
        }
        return segments[segments.length - 1].toLowerCase();
    }

    // Obtém o nome do arquivo atual (ex: 'produtos.php' ou 'dashboard.php')
    const currentSegment = getFilenameFromUrl(window.location.href);
    console.log("Segmento atual (arquivo):", currentSegment);

    // 1. Funcionalidade da Sidebar no Mobile (Responsividade)
    const sidebar = document.querySelector(".sidebar");
    const mediaQuery = window.matchMedia("(max-width: 768px)");

    function handleSidebarToggle(e) {
        if (e.matches) {
            sidebar.classList.remove("open");
        } else {
            sidebar.classList.add("open");
        }
    }

    handleSidebarToggle(mediaQuery);
    mediaQuery.addListener(handleSidebarToggle);

    // 2. Interatividade - Efeito de clique nos Atalhos (Feedback visual)
    const shortcutCards = document.querySelectorAll(".shortcut-card");

    shortcutCards.forEach((card) => {
        card.addEventListener("mousedown", (e) => {
            card.style.transform = "translateY(0)";
            card.style.boxShadow = "0 4px 10px rgba(0, 0, 0, 0.1)";
        });

        card.addEventListener("mouseup", (e) => {
            setTimeout(() => {
                card.style.transform = "";
                card.style.boxShadow = "";
            }, 100);
        });

        card.addEventListener("mouseleave", (e) => {
            card.style.transform = "";
            card.style.boxShadow = "";
        });
    });

    // 3. Simulação de Dados Dinâmicos
    const lowStockCountEl = document.getElementById("lowStockCount");
    const productQuantityEl = document.getElementById("productQuantity");
    const totalCostEl = document.getElementById("totalCost");

    const newLowStockCount = 5;
    const newProductQuantity = 120;
    const newTotalCost = "250.990";

    setTimeout(() => {
        lowStockCountEl.textContent = newLowStockCount;
        productQuantityEl.textContent = newProductQuantity;
        totalCostEl.textContent = newTotalCost;

        if (newLowStockCount > 0) {
            lowStockCountEl.parentElement.parentElement.style.animation = "pulse 1.5s infinite";
        }
    }, 500);

    // 4. DESTAQUE DA PÁGINA ATIVA NA SIDEBAR (Lógica Ajustada)
    const navItems = document.querySelectorAll(".nav-menu ul li");

    // currentSegment já foi obtido acima via getFilenameFromUrl(window.location.href)
    console.log("Segmento atual (arquivo):", currentSegment);

    navItems.forEach((li) => {
        const link = li.querySelector("a");
        if (!link) return;

        const linkSegment = getFilenameFromUrl(link.href);
        console.log("  Comparando com link:", link.href, "=>", linkSegment);

        // remove qualquer marcação anterior
        li.classList.remove("active");

        if (currentSegment === linkSegment) {
            li.classList.add("active");
            return;
        }

        // fallback: se estivermos na raiz/index, destacamos dashboard.php
        const indexLike = ["index.php", ""];
        if (indexLike.includes(currentSegment) && linkSegment === "dashboard.php") {
            li.classList.add("active");
            return;
        }
    });
    // Ajusta quando a tela é redimensionada (remove inline style se voltar para desktop)
    window.addEventListener("resize", () => {
        if (!isMobileView()) {
            sidebar.classList.remove("active");
            sidebar.style.transform = "";
            body.classList.remove("menu-open");
        } else {
            // se no mobile e ainda sem classe active, garante que esteja escondida
            if (!sidebar.classList.contains("active")) {
                sidebar.style.transform = "translateX(-110%)";
            }
        }
    });

    // Inicial: forçar estado correto ao carregar
    if (isMobileView()) {
        sidebar.classList.remove("active");
        sidebar.style.transform = "translateX(-110%)";
    } else {
        sidebar.style.transform = "";
    }
});

// js/script.js
(function () {
    // diagnóstico
    console.log("[hamburger] script iniciado");

    document.addEventListener("DOMContentLoaded", () => {
        console.log("[hamburger] DOM pronto");

        const hamburgerBtn = document.getElementById("hamburger-btn");
        const sidebar = document.querySelector(".sidebar");

        if (!hamburgerBtn) {
            console.error('[hamburger] botão com id "hamburger-btn" NÃO encontrado no DOM');
            return;
        }
        if (!sidebar) {
            console.error("[hamburger] .sidebar NÃO encontrada no DOM");
            return;
        }

        // evita múltiplos event listeners caso o script seja carregado duas vezes
        if (hamburgerBtn.dataset.hamburgerInit === "1") {
            console.warn("[hamburger] já inicializado (listener existente)");
            return;
        }
        hamburgerBtn.dataset.hamburgerInit = "1";

        // cria overlay (se quiser)
        let overlay = document.querySelector(".sidebar-overlay");
        function createOverlay() {
            if (!overlay) {
                overlay = document.createElement("div");
                overlay.className = "sidebar-overlay";
                document.body.appendChild(overlay);
                overlay.addEventListener("click", closeSidebar);
            }
        }

        function openSidebar() {
            sidebar.classList.add("active");
            document.body.classList.add("menu-open");
            hamburgerBtn.setAttribute("aria-expanded", "true");
            createOverlay();
            // visível para debug
            console.log("[hamburger] sidebar aberta");
        }

        function closeSidebar() {
            sidebar.classList.remove("active");
            document.body.classList.remove("menu-open");
            hamburgerBtn.setAttribute("aria-expanded", "false");
            if (overlay) overlay.remove();
            overlay = null;
            console.log("[hamburger] sidebar fechada");
        }

        function toggleSidebar() {
            if (sidebar.classList.contains("active")) closeSidebar();
            else openSidebar();
        }

        hamburgerBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });

        // fechar ao clicar fora (tela pequena)
        document.addEventListener("click", (e) => {
            if (window.innerWidth <= 768 && sidebar.classList.contains("active")) {
                if (!sidebar.contains(e.target) && e.target !== hamburgerBtn) {
                    closeSidebar();
                }
            }
        });

        // fechar com ESC
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && sidebar.classList.contains("active")) {
                closeSidebar();
            }
        });

        // debug: informar tamanho da janela
        console.log("[hamburger] largura da janela:", window.innerWidth);
    });
})();

(function () {
    var candidates = [
        "js/script.js", // quando a página está na raiz do projeto
        "../js/script.js", // quando a página está dentro de php/ (ex: php/dashboard.php)
        "/lumi_stock/js/script.js", // caminho absoluto esperado no ambiente local (ajuste se necessário)
    ];

    // function tryLoad(list, idx) {
    //     if (idx >= list.length) return; // nenhum deu certo
    //     var s = document.createElement("script");
    //     s.src = list[idx];
    //     s.onload = function () {
    //         console.log("script carregado:", list[idx]);
    //     };
    //     s.onerror = function () {
    //         // remove o script com src inválido e tenta o próximo
    //         s.parentNode && s.parentNode.removeChild(s);
    //         tryLoad(list, idx + 1);
    //     };
    //     document.head.appendChild(s);
    // }

    tryLoad(candidates, 0);
})();
