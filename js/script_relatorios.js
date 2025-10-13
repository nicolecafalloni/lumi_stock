document.addEventListener('DOMContentLoaded', function() {
    if (!REL_REPORT_GENERATED) return;

    // --- Gráfico Valor por Categoria ---
    const ctxCategoria = document.getElementById('rel-chart-categoria').getContext('2d');
    const labelsCategoria = Object.keys(REL_CATEGORIA_SUMS).map(cat => cat.charAt(0).toUpperCase() + cat.slice(1));
    const dataCategoria = Object.values(REL_CATEGORIA_SUMS);

    new Chart(ctxCategoria, {
        type: 'pie',
        data: {
            labels: labelsCategoria,
            datasets: [{
                data: dataCategoria,
                backgroundColor: [
                    '#0033cc', // Escritório
                    '#0055ff', // Eletrônicos
                    '#3399ff', // Mobiliário
                    '#99ccff', // Ferramentas
                    '#cce6ff'  // Consumíveis
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': R$ ' + context.raw.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        }
                    }
                }
            }
        }
    });

    // --- Gráfico Top 10 Produtos por Valor ---
    const ctxTop10 = document.getElementById('rel-chart-top10').getContext('2d');
    const labelsTop10 = REL_TOP_PRODUTOS.map(p => p.nome);
    const dataTop10 = REL_TOP_PRODUTOS.map(p => p.preco_total);

    new Chart(ctxTop10, {
        type: 'bar',
        data: {
            labels: labelsTop10,
            datasets: [{
                label: 'Valor Total (R$)',
                data: dataTop10,
                backgroundColor: '#0055ff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'R$ ' + context.raw.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

document.getElementById('rel-export-xlsx').addEventListener('click', () => {
    const table = document.querySelector('.rel-table');
    const wb = XLSX.utils.table_to_book(table, { sheet: "Relatório" });
    XLSX.writeFile(wb, 'relatorio_estoque.xlsx');
});

document.getElementById('rel-export-pdf').addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Relatório de Estoque", 14, 15);
    doc.autoTable({ 
        html: '.rel-table',
        startY: 25,
        theme: 'grid'
    });
    
    doc.save('relatorio_estoque.pdf');
});

document.getElementById('rel-print').addEventListener('click', () => {
    window.print();
});
