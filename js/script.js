document.addEventListener("DOMContentLoaded", () => {
    // A. Resolve URLs relativas usando um elemento <a> e extrai o nome do arquivo (último segmento)
    function getFilenameFromUrl(url) {
        const a = document.createElement('a');
        a.href = url; // browser resolves relative -> absolute

        let pathname = a.pathname || '';
        pathname = pathname.split('?')[0].split('#')[0];

        const segments = pathname.split('/').filter((s) => s.length > 0);
        if (segments.length === 0) {
            // se for raiz, assumimos index.php (ajuste se a home for outro arquivo)
            return 'index.php';
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
    const navItems = document.querySelectorAll('.nav-menu ul li');

    // currentSegment já foi obtido acima via getFilenameFromUrl(window.location.href)
    console.log('Segmento atual (arquivo):', currentSegment);

    navItems.forEach((li) => {
        const link = li.querySelector('a');
        if (!link) return;

        const linkSegment = getFilenameFromUrl(link.href);
        console.log('  Comparando com link:', link.href, '=>', linkSegment);

        // remove qualquer marcação anterior
        li.classList.remove('active');

        if (currentSegment === linkSegment) {
            li.classList.add('active');
            return;
        }

        // fallback: se estivermos na raiz/index, destacamos dashboard.php
        const indexLike = ['index.php', ''];
        if (indexLike.includes(currentSegment) && linkSegment === 'dashboard.php') {
            li.classList.add('active');
            return;
        }
    });
});

