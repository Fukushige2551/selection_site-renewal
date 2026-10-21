import '../scss/archive-recipe.scss';

document.addEventListener('DOMContentLoaded', () => {
    const mobileViewport = window.matchMedia('(max-width: 767px)');
    const results = document.querySelector('.p-recipe-archive--results');
    const syncResultPageSize = () => {
        if (!results) return;
        const size = mobileViewport.matches ? 10 : 12;
        const previousSize = Number(results.dataset.recipePageSize);
        if (size === previousSize) return;
        const url = new URL(window.location.href);
        const previousPage = Math.max(1, Number(url.searchParams.get('recipe_page')) || 1);
        // Keep the previous page's first recipe within the new page after resizing.
        const page = Math.floor((previousPage - 1) * previousSize / size) + 1;
        url.searchParams.set('recipe_view', mobileViewport.matches ? 'sp' : 'wide');
        url.searchParams.set('recipe_page', String(page));
        window.location.replace(url.href);
    };
    syncResultPageSize();
    mobileViewport.addEventListener('change', syncResultPageSize);
    document.querySelectorAll('form.p-recipe-archive__search').forEach(form => {
        form.addEventListener('submit', event => {
            const input = form.querySelector('.p-recipe-archive__name-input');
            if (input && !input.disabled && event.submitter?.name !== 'recipe_search') {
                if (!input.value.trim()) {
                    event.preventDefault();
                    input.focus();
                    return;
                }
                let search = form.querySelector('input[name="recipe_search"]');
                if (!search) {
                    search = document.createElement('input');
                    search.type = 'hidden';
                    search.name = 'recipe_search';
                    form.append(search);
                }
                search.value = input.value.trim();
            } else {
                form.querySelector('input[name="recipe_search"]')?.remove();
            }
            let field = form.querySelector('[name="recipe_view"]');
            if (!field) {
                field = document.createElement('input');
                field.type = 'hidden';
                field.name = 'recipe_view';
                form.append(field);
            }
            field.value = mobileViewport.matches ? 'sp' : 'wide';
        });
    });

    const tabs = [...document.querySelectorAll('[data-recipe-search-tab]')];
    const panel = document.getElementById('recipe-search-panel');
    if (!panel || !tabs.length) return;

    const form = panel.closest('form');
    const namePanel = form.querySelector('.p-recipe-archive__name-panel');
    namePanel.querySelector('input').addEventListener('keydown', event => {
        if (event.key !== 'Enter' || event.isComposing) return;
        event.preventDefault();
        form.requestSubmit(namePanel.querySelector('button'));
    });

    const activateTab = tab => {
        tabs.forEach(item => {
            const selected = item.dataset.recipeSearchTab === tab.dataset.recipeSearchTab;
            item.classList.toggle('is-active', selected);
            if (item.getAttribute('role') === 'tab') {
                item.setAttribute('aria-selected', String(selected));
                item.tabIndex = selected ? 0 : -1;
            } else {
                item.setAttribute('aria-pressed', String(selected));
            }
        });
        const nameSearch = tab.dataset.recipeSearchTab === 'name';
        form.classList.toggle('is-name-search', nameSearch);
        panel.hidden = nameSearch;
        namePanel.hidden = !nameSearch;
        namePanel.querySelectorAll('input, button').forEach(control => { control.disabled = !nameSearch; });
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', event => {
            let next;
            const peers = tabs.filter(item => item.getAttribute('role') === tab.getAttribute('role'));
            const position = peers.indexOf(tab);
            if (event.key === 'ArrowRight') next = peers[(position + 1) % peers.length];
            if (event.key === 'ArrowLeft') next = peers[(position + peers.length - 1) % peers.length];
            if (event.key === 'Home') next = peers[0];
            if (event.key === 'End') next = peers[peers.length - 1];
            if (!next) return;
            event.preventDefault();
            activateTab(next);
            next.focus();
        });
    });
});
