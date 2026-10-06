document.querySelectorAll('[data-print]').forEach(button => button.addEventListener('click', () => window.print()));

document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (!window.confirm(form.dataset.confirm)) event.preventDefault(); }));
// Alterna a visibilidade sem alterar o valor ou enviar o formulário.
document.querySelectorAll('input[type="password"]').forEach((input, index) => {
    const wrapper = document.createElement('span');
    wrapper.className = 'password-field';
    input.before(wrapper);
    wrapper.append(input);
    if (!input.id) input.id = 'password-field-' + index;
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'password-toggle';
    toggle.setAttribute('aria-controls', input.id);
    toggle.setAttribute('aria-label', 'Mostrar senha');
    toggle.setAttribute('aria-pressed', 'false');
    toggle.title = 'Mostrar senha';
    toggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="eye-slash" d="m4 4 16 16"/></svg>';
    toggle.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(visible));
        toggle.setAttribute('aria-label', visible ? 'Ocultar senha' : 'Mostrar senha');
        toggle.title = visible ? 'Ocultar senha' : 'Mostrar senha';
    });
    wrapper.append(toggle);
});
