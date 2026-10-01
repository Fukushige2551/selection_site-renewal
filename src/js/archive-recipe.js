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
        form.addEventListener('submit', () => {
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

    const ingredientButtons = [...panel.children].map(button => button.cloneNode(true));
    const dishCards = document.querySelectorAll('[aria-labelledby="recipe-desktop-name-title"] [name="recipe_search"]');
    const dishButtons = [...dishCards].map(card => {
        const button = document.createElement('button');
        button.className = 'p-recipe-archive__ingredient-button';
        button.type = 'submit';
        button.name = 'recipe_search';
        button.value = card.value;
        button.textContent = card.querySelector('span').textContent;
        return button;
    });

    const activateTab = tab => {
        tabs.forEach(item => {
            const selected = item === tab;
            item.classList.toggle('is-active', selected);
            item.setAttribute('aria-selected', String(selected));
            item.tabIndex = selected ? 0 : -1;
        });
        panel.setAttribute('aria-labelledby', tab.id);
        const buttons = tab.dataset.recipeSearchTab === 'name' ? dishButtons : ingredientButtons;
        panel.replaceChildren(...buttons.map(button => button.cloneNode(true)));
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', event => {
            let next;
            if (event.key === 'ArrowRight') next = tabs[(index + 1) % tabs.length];
            if (event.key === 'ArrowLeft') next = tabs[(index + tabs.length - 1) % tabs.length];
            if (event.key === 'Home') next = tabs[0];
            if (event.key === 'End') next = tabs[tabs.length - 1];
            if (!next) return;
            event.preventDefault();
            activateTab(next);
            next.focus();
        });
    });
});
