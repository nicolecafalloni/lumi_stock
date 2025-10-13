document.addEventListener('DOMContentLoaded', () => {
    // 1. Elementos do Modal
    const modal = document.getElementById('newMovementModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.querySelector('.modal .close-btn'); // O 'x'
    const cancelBtn = document.getElementById('cancelModalBtn');
    const form = document.querySelector('.new-movement-form');

    // 2. Função para fechar o modal
    const closeModal = function() {
        modal.style.display = 'none';
    }

    // 3. Abrir o modal
    openBtn.onclick = function() {
        modal.style.display = 'block';
    }

    // 4. Fechar ao clicar em 'x' ou 'Cancelar'
    closeBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;

    // 5. Fechar ao clicar fora do modal
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

    // 6. Lógica de submissão do formulário (Exemplo)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const quantidade = document.getElementById('quantity').value;
        const tipo = document.getElementById('movType').value;
        const produto = document.getElementById('productSelect').options[document.getElementById('productSelect').selectedIndex].text;

        alert(`SUCESSO! Movimentação de ${tipo.toUpperCase()} para o produto '${produto}' (Qnt: ${quantidade}) foi registrada.`);
        
        form.reset();
        closeModal();
    });

    // 7. Define a data atual no campo de data (para melhor UX)
    const movDateInput = document.getElementById('movDate');
    if (movDateInput) {
        const today = new Date().toISOString().split('T')[0];
        movDateInput.value = today;
    }
});