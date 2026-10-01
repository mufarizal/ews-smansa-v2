document.querySelectorAll('.ews-menu-toggle').forEach((button) => {
    const sidebar = document.getElementById(button.getAttribute('aria-controls'));
    if (!sidebar) return;
    const backdrop = document.querySelector('.ews-sidebar-backdrop');
    const closeButton = sidebar.querySelector('.ews-sidebar-close');
    const desktop = window.matchMedia('(min-width: 768px)');
    const background = [document.querySelector('.ews-topbar'), document.querySelector('.ews-content')].filter(Boolean);
    let opened = false;
    const close = (restoreFocus = true) => {
        opened = false;
        button.setAttribute('aria-expanded', 'false');
        sidebar.classList.remove('is-open');
        sidebar.removeAttribute('role');
        sidebar.removeAttribute('aria-modal');
        backdrop.hidden = true;
        document.body.classList.remove('ews-sidebar-open');
        background.forEach((element) => { element.inert = false; });
        if (restoreFocus) button.focus();
    };
    button.addEventListener('click', () => {
        if (opened) return close();
        if (desktop.matches) return;
        opened = true;
        button.setAttribute('aria-expanded', 'true');
        sidebar.classList.add('is-open');
        sidebar.setAttribute('role', 'dialog');
        sidebar.setAttribute('aria-modal', 'true');
        backdrop.hidden = false;
        document.body.classList.add('ews-sidebar-open');
        closeButton.focus();
        background.forEach((element) => { element.inert = true; });
    });
    closeButton.addEventListener('click', () => close());
    backdrop.addEventListener('click', () => close());
    sidebar.addEventListener('click', (event) => {
        if (opened && event.target.closest('a')) close();
    });
    document.addEventListener('keydown', (event) => {
        if (!opened) return;
        if (event.key === 'Escape') { event.preventDefault(); close(); }
        if (event.key === 'Tab') {
            const items = [...sidebar.querySelectorAll('a[href], button:not([disabled])')];
            const first = items[0], last = items[items.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    desktop.addEventListener('change', () => {
        const focusWasInside = sidebar.contains(document.activeElement);
        close(false);
        if (!desktop.matches && focusWasInside) button.focus();
        else if (desktop.matches && focusWasInside) sidebar.querySelector('a')?.focus();
    });
});

// Native disclosure keeps account links accessible with keyboard and without JS.
document.querySelectorAll('.ews-profile').forEach((profile) => {
    const trigger = profile.querySelector('summary');
    document.addEventListener('click', (event) => {
        if (!profile.contains(event.target)) profile.open = false;
    });
    profile.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && profile.open) {
            event.preventDefault();
            profile.open = false;
            trigger.focus();
        }
    });
    profile.addEventListener('focusout', () => {
        requestAnimationFrame(() => {
            if (!profile.contains(document.activeElement)) profile.open = false;
        });
    });
});
document.querySelectorAll('[data-show-password]').forEach((toggle) => {
    toggle.addEventListener('change', () => {
        toggle.form.querySelectorAll('input[autocomplete$="password"]').forEach((input) => {
            input.type = toggle.checked ? 'text' : 'password';
        });
    });
});
