(() => {
    const items = [...document.querySelectorAll('.equipment-item[data-zone]')];
    const body = document.querySelector('.body-map');
    if (!body) return;
    const description = document.getElementById('body-description');
    const selected = new Set();
    let pending = null;
    let touch = null;
    const supported = item => !!document.querySelector(`[data-body="${item.dataset.zone}"]`);
    function render(message) {
        items.forEach(item => item.setAttribute('aria-pressed', String(selected.has(item))));
        document.querySelectorAll('[data-body]').forEach(region => {
            region.classList.toggle('selected', [...selected].some(item => item.dataset.zone === region.dataset.body));
        });
        description.textContent = message || (selected.size ? 'No personagem: ' + [...selected].map(item => item.dataset.name).join(', ') + '.' : 'Arraste um EPI para o personagem para começar.');
        document.querySelector('[data-equip-clear]').disabled = !selected.size;
    }
    function equip(item) {
        if (!item) return;
        if (!supported(item)) { render(item.dataset.name + ': ainda não há uma ilustração para este tipo de equipamento.'); return; }
        selected.add(item);
        render();
    }
    function resetDrag() {
        items.forEach(item => item.classList.remove('is-dragging'));
        body.classList.remove('drop-ready', 'drag-over');
        document.querySelector('.equipment-drag-preview')?.remove();
        pending = null;
    }
    function overBody(x, y) {
        const rect = body.getBoundingClientRect();
        return x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
    }
    items.forEach((item, index) => {
        item.addEventListener('dragstart', event => {
            pending = item;
            event.dataTransfer.setData('text/plain', String(index));
            event.dataTransfer.effectAllowed = 'copy';
            item.classList.add('is-dragging');
            body.classList.add('drop-ready');
        });
        item.addEventListener('dragend', resetDrag);
        item.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                pending = item;
                body.classList.add('drop-ready');
                description.textContent = item.dataset.name + ' pronto. Pressione Enter no personagem para vestir ou Escape para cancelar.';
                body.focus();
            }
        });
        item.addEventListener('pointerdown', event => {
            if (event.pointerType === 'mouse') return;
            touch = {id: event.pointerId, item, x: event.clientX, y: event.clientY, moved: false};
            item.setPointerCapture(event.pointerId);
        });
        item.addEventListener('pointermove', event => {
            if (!touch || touch.id !== event.pointerId) return;
            if (!touch.moved && Math.hypot(event.clientX-touch.x,event.clientY-touch.y) < 8) return;
            touch.moved = true;
            if (event.clientY < 70) window.scrollBy(0, -24);
            else if (event.clientY > window.innerHeight - 70) window.scrollBy(0, 24);
            item.classList.add('is-dragging');
            body.classList.add('drop-ready');
            body.classList.toggle('drag-over', overBody(event.clientX,event.clientY));
            let ghost = document.querySelector('.equipment-drag-preview');
            if (!ghost) { ghost = document.createElement('div'); ghost.className = 'equipment-drag-preview'; ghost.textContent = item.dataset.name; document.body.append(ghost); }
            ghost.style.left = event.clientX + 'px'; ghost.style.top = event.clientY + 'px';
        });
        item.addEventListener('pointerup', event => {
            if (!touch || touch.id !== event.pointerId) return;
            if (touch.moved && overBody(event.clientX,event.clientY)) equip(touch.item);
            touch = null; resetDrag();
        });
        item.addEventListener('pointercancel', event => {
            if (!touch || touch.id !== event.pointerId) return;
            touch = null; resetDrag();
        });
    });
    body.addEventListener('dragover', event => {
        if (!pending) return;
        event.preventDefault(); event.dataTransfer.dropEffect = 'copy'; body.classList.add('drag-over');
    });
    body.addEventListener('dragleave', event => { if (!body.contains(event.relatedTarget)) body.classList.remove('drag-over'); });
    body.addEventListener('drop', event => { event.preventDefault(); equip(pending || items[Number(event.dataTransfer.getData('text/plain'))]); resetDrag(); });
    body.addEventListener('keydown', event => {
        if ((event.key === 'Enter' || event.key === ' ') && pending) { event.preventDefault(); equip(pending); resetDrag(); }
    });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') { resetDrag(); render(); } });
    document.querySelector('[data-equip-all]').addEventListener('click', () => {
        items.filter(supported).forEach(item => selected.add(item)); resetDrag(); render();
        const unknown = items.filter(item => !supported(item));
        if (unknown.length) description.textContent += ' Sem ilustração disponível: ' + unknown.map(item => item.dataset.name).join(', ') + '.';
    });
    document.querySelector('[data-equip-all]').disabled = !items.length;
    document.querySelector('[data-equip-clear]').addEventListener('click', () => { selected.clear(); resetDrag(); render(); });
    render();
})();
