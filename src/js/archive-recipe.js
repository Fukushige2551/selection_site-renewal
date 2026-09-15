import '../scss/archive-recipe.scss';

document.addEventListener('DOMContentLoaded', () => {
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
