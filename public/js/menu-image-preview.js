document.addEventListener('DOMContentLoaded', () => {
    const imageInputs = document.querySelectorAll('#image, #images');
    imageInputs.forEach((input) => {
        const previewContainer = document.getElementById(
            `${input.id}-preview`
        );
        if (!previewContainer) {
            return;
        }
        const sortable = input.id === 'images';
        let draggedItem = null;
        let selectedFiles = [];
        function getFileKey(file) {
            return [
                file.name,
                file.size,
                file.lastModified,
                file.type,
            ].join(':');
        }
        function syncInputFiles() {
            const transfer = new DataTransfer();
            selectedFiles.forEach((file) => {
                transfer.items.add(file);
            });
            input.files = transfer.files;
        }
        function renderPreview() {
            previewContainer.replaceChildren();
            selectedFiles.forEach((file) => {
                const item = document.createElement('div');
                item.className = 'menu-image-preview-item';
                if (sortable) {
                    item.draggable = true;
                }
                item._file = file;
                const image = document.createElement('img');
                image.alt = file.name;
                image.width = sortable ? 120 : 100;
                image.loading = 'lazy';
                const filename = document.createElement('small');
                filename.textContent = file.name;
                item.append(image, filename);
                if (sortable) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'menu-image-preview-remove';
                    removeButton.textContent = '×';
                    removeButton.setAttribute(
                        'aria-label',
                        `Remove ${file.name}`
                    );
                    removeButton.title = `Remove ${file.name}`;
                    removeButton.addEventListener('click', () => {
                        selectedFiles = selectedFiles.filter(
                            (selectedFile) => selectedFile !== file
                        );
                        syncInputFiles();
                        renderPreview();
                    });
                    item.append(removeButton);
                }
                previewContainer.append(item);
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    image.src = reader.result;
                });
                reader.readAsDataURL(file);
            });
        }
        input.addEventListener('change', () => {
            const newlySelectedFiles = Array.from(input.files ?? [])
                .filter((file) => file.type.startsWith('image/'));
            if (sortable) {
                const existingKeys = new Set(
                    selectedFiles.map(getFileKey)
                );
                newlySelectedFiles.forEach((file) => {
                    const key = getFileKey(file);
                    if (!existingKeys.has(key)) {
                        selectedFiles.push(file);
                        existingKeys.add(key);
                    }
                });
                syncInputFiles();
            } else {
                selectedFiles = newlySelectedFiles;
            }
            renderPreview();
        });
        if (!sortable) {
            return;
        }
        previewContainer.addEventListener('dragstart', (event) => {
            const item = event.target.closest(
                '.menu-image-preview-item'
            );
            if (
                !item ||
                !previewContainer.contains(item) ||
                event.target.closest('.menu-image-preview-remove')
            ) {
                event.preventDefault();
                return;
            }
            draggedItem = item;
            item.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', 'menu-image');
        });
        previewContainer.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (!draggedItem) {
                return;
            }
            const target = event.target.closest(
                '.menu-image-preview-item'
            );
            if (
                !target ||
                target === draggedItem ||
                !previewContainer.contains(target)
            ) {
                return;
            }
            const rect = target.getBoundingClientRect();
            const after = event.clientX > rect.left + rect.width / 2;
            previewContainer.insertBefore(
                draggedItem,
                after ? target.nextSibling : target
            );
        });
        previewContainer.addEventListener('drop', (event) => {
            event.preventDefault();
        });
        previewContainer.addEventListener('dragend', () => {
            if (!draggedItem) {
                return;
            }
            draggedItem.classList.remove('dragging');
            draggedItem = null;
            selectedFiles = Array.from(
                previewContainer.querySelectorAll(
                    '.menu-image-preview-item'
                )
            ).map((item) => item._file);
            syncInputFiles();
            renderPreview();
        });
    });
});