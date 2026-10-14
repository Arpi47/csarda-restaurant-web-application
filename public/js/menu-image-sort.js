document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('menu-image-list');
    if (!container) {
        return;
    }
    let draggedItem = null;
    container.querySelectorAll('.menu-image-item').forEach((item) => {
        item.addEventListener('dragstart', (event) => {
            draggedItem = item;
            item.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', item.dataset.imageId);
        });
        item.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (!draggedItem || draggedItem === item) {
                return;
            }
            const rect = item.getBoundingClientRect();
            const after = event.clientX > rect.left + rect.width / 2;
            container.insertBefore(
                draggedItem,
                after ? item.nextSibling : item
            );
        });
        item.addEventListener('dragend', () => {
            item.classList.remove('dragging');
            draggedItem = null;
            updateOrder();
        });
    });
    function updateOrder() {
        container.querySelectorAll('.menu-image-item').forEach((item) => {
            const orderInput = item.querySelector(
                'input[name="image_order[]"]'
            );
            if (orderInput) {
                orderInput.value = item.dataset.imageId;
            }
        });
    }
});