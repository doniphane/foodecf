const dishes = [
    { id: 1, name: "Salade César", price: 12, category: "Entrées", chef: "Marie Dubois", description: "" },
    { id: 2, name: "Soupe de potiron", price: 8, category: "Entrées", chef: "Jean Martin", description: "" },
    { id: 3, name: "Carpaccio de bœuf", price: 16, chef: "Marie Dubois", category: "Entrées", description: "Fines lamelles de bœuf, roquette, copeaux de parmesan et huile de truffe" },
    { id: 4, name: "Bruschetta", price: 10, chef: "Jean Martin", category: "Entrées", description: "Pain grillé garni de tomates fraîches, basilic et mozzarella di bufala" },

    // Plats
    { id: 5, name: "Bœuf bourguignon", price: 24, chef: "Marie Dubois", category: "Plats", description: "Bœuf mijoté au vin rouge avec légumes et garniture traditionnelle" },
    { id: 6, name: "Saumon grillé", price: 22, chef: "Jean Martin", category: "Plats", description: "Filet de saumon grillé, légumes de saison et sauce hollandaise" },
    { id: 7, name: "Risotto aux champignons", price: 18, chef: "Marie Dubois", category: "Plats", description: "Riz arborio crémeux aux cèpes et champignons de Paris, parmesan" },
    { id: 8, name: "Coq au vin", price: 26, chef: "Jean Martin", category: "Plats", description: "Coq fermier mijoté au vin blanc avec lardons et champignons" },

    // Desserts
    { id: 9, name: "Tiramisu", price: 8, chef: "Marie Dubois", category: "Desserts", description: "Dessert italien traditionnel au mascarpone, café et cacao" },
    { id: 10, name: "Tarte tatin", price: 7, chef: "Jean Martin", category: "Desserts", description: "Tarte aux pommes caramélisées servie tiède avec crème fraîche" },
    { id: 11, name: "Crème brûlée", price: 9, chef: "Marie Dubois", category: "Desserts", description: "Crème vanillée avec croûte de sucre caramélisé craquante" },
    { id: 12, name: "Mousse au chocolat", price: 6, chef: "Jean Martin", category: "Desserts", description: "Mousse légère au chocolat noir 70% et chantilly maison" }
];

let currentCategory = "all";
let currentSort = "default";
let currentSearch = "";
let displayCount = 12;

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const categoryButtons = document.querySelectorAll(".category-btn");
    const categoryButtons2 = document.querySelectorAll(".category-btn2");
    const sortButtons = document.querySelectorAll(".sort-btn");
    const displaySlider = document.getElementById("display-count");
    const displayValue = document.getElementById("display-value");
    const dishesContainer = document.getElementById("dishes-container");
    const dishesCountElement = document.getElementById("dishes-count");

    // Ajuster la valeur maximale du slider en fonction du nombre réel de plats
    if (displaySlider && dishesContainer) {
        const totalDishes = dishesContainer.children.length;
        displaySlider.max = totalDishes;
        displayCount = Math.min(displayCount, totalDishes);
        displaySlider.value = displayCount;
        if (displayValue) {
            displayValue.textContent = displayCount;
        }
    }

    if (searchInput) {
        searchInput.addEventListener("input", handleSearch);
    }

    // Filtres par catégorie
    categoryButtons.forEach(button => {
        button.addEventListener("click", handleCategoryFilter);
    });
    categoryButtons2.forEach(button => {
        button.addEventListener("click", handleCategoryFilter);
    });

    // Tri par prix
    sortButtons.forEach(button => {
        button.addEventListener("click", handleSort);
    });

    // Slider pour le nombre de plats affichés
    if (displaySlider) {
        displaySlider.addEventListener("input", handleDisplayCount);
        updateDisplay();
    }
});

function handleSearch() {
    const searchInput = document.getElementById("search");
    if (searchInput) {
        currentSearch = searchInput.value.toLowerCase();
        updateDisplay();
    }
}

function handleCategoryFilter(event) {
    const categoryButtons = document.querySelectorAll(".category-btn");
    const categoryButtons2 = document.querySelectorAll(".category-btn2");

    // Supprime "active" sur tous les boutons
    categoryButtons.forEach(btn => btn.classList.remove("active"));
    categoryButtons2.forEach(btn => btn.classList.remove("active"));

    // Ajoute "active" au bouton cliqué
    event.target.classList.add("active");

    currentCategory = event.target.dataset.category;
    updateDisplay();
}

function handleSort(event) {
    const sortButtons = document.querySelectorAll(".sort-btn");
    sortButtons.forEach(btn => btn.classList.remove("active"));
    event.target.classList.add("active");
    currentSort = event.target.dataset.sort;
    updateDisplay();
}

function handleDisplayCount() {
    const displaySlider = document.getElementById("display-count");
    const displayValue = document.getElementById("display-value");
    if (displaySlider && displayValue) {
        displayCount = parseInt(displaySlider.value);
        displayValue.textContent = displayCount;
        updateDisplay();
    }
}

function filterDishes() {
    const dishesContainer = document.getElementById("dishes-container");
    if (!dishesContainer) return [];

    const dishes = Array.from(dishesContainer.children).map(card => ({
        id: card.dataset.id,
        name: card.querySelector('.dish-name')?.textContent || '',
        price: parseFloat(card.querySelector('.dish-price')?.textContent) || 0,
        category: card.querySelector('.dish-category')?.textContent || '',
        chef: card.querySelector('.dish-chef')?.textContent.replace('Chef: ', '') || '',
        description: card.querySelector('.dish-description')?.textContent || ''
    }));

    let filteredDishes = [...dishes];

    // Filtrage par recherche
    if (currentSearch) {
        filteredDishes = filteredDishes.filter(dish =>
            dish.name.toLowerCase().includes(currentSearch) ||
            dish.chef.toLowerCase().includes(currentSearch) ||
            dish.description.toLowerCase().includes(currentSearch)
        );
    }

    // Filtrage par catégorie
    if (currentCategory !== "all") {
        const categoryMap = {
            "entrees": "Entrées",
            "plats": "Plats",
            "desserts": "Desserts"
        };
        filteredDishes = filteredDishes.filter(dish =>
            dish.category === categoryMap[currentCategory]
        );
    }

    // Tri
    switch (currentSort) {
        case "asc":
            filteredDishes.sort((a, b) => a.price - b.price);
            break;
        case "desc":
            filteredDishes.sort((a, b) => b.price - a.price);
            break;
        default:
            filteredDishes.sort((a, b) => a.id - b.id);
    }

    return filteredDishes.slice(0, displayCount);
}

function updateDisplay() {
    const dishesContainer = document.getElementById("dishes-container");
    const dishesCountElement = document.getElementById("dishes-count");
    if (!dishesContainer || !dishesCountElement) return;

    const filteredDishes = filterDishes();
    dishesCountElement.textContent = `Affichage de ${filteredDishes.length} plat${filteredDishes.length > 1 ? 's' : ''}`;

    // Sauvegarde les cartes originales
    const originalCards = Array.from(dishesContainer.children);


    originalCards.forEach(card => {
        const dishId = card.dataset.id;
        const isVisible = filteredDishes.some(dish => dish.id === dishId);
        card.style.display = isVisible ? '' : 'none';
    });
}
