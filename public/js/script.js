let currentCategory = "all";
let currentSort = "default";
let currentSearch = "";
let displayCount = 12;


const searchInput = document.getElementById("search");
const categoryButtons = document.querySelectorAll(".category-btn");
const categoryButtons2 = document.querySelectorAll(".category-btn2");
const sortButtons = document.querySelectorAll(".sort-btn");
const displaySlider = document.getElementById("display-count");
const displayValue = document.getElementById("display-value");
const dishesContainer = document.getElementById("dishes-container");
const dishesCountElement = document.getElementById("dishes-count");


document.addEventListener("DOMContentLoaded", () => {

    updateDisplay();


    searchInput.addEventListener("input", handleSearch);
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
    displaySlider.addEventListener("input", handleDisplayCount);
});


function handleSearch() {
    currentSearch = searchInput.value.toLowerCase();
    updateDisplay();
}


function handleCategoryFilter(event) {
    // Supprime "active" sur tous les boutons
    categoryButtons.forEach(btn => btn.classList.remove("active"));
    categoryButtons2.forEach(btn => btn.classList.remove("active"));



    // Ajoute "active" au bouton cliqué
    event.target.classList.add("active");

    currentCategory = event.target.dataset.category; // Récupère la catégorie 
    updateDisplay();
}



function handleSort(event) {
    sortButtons.forEach(btn => btn.classList.remove("active"));
    event.target.classList.add("active"); // active le bouton cliqué
    currentSort = event.target.dataset.sort; // "asc", "desc", ou default
    updateDisplay();
}



function handleDisplayCount() {
    displayCount = parseInt(displaySlider.value); // Converti la valeur du slider en nombre
    displayValue.textContent = displayCount;
    updateDisplay();
}

function filterDishes() {
    let filteredDishes = [...dishes]; // copie du tableau original

    // Filtrage par recherche
    if (currentSearch) {
        filteredDishes = filteredDishes.filter(dish =>
            dish.name.toLowerCase().includes(currentSearch) ||
            dish.chef.toLowerCase().includes(currentSearch) ||
            dish.description.toLowerCase().includes(currentSearch)
        );
    }

    // Filtrage par catégorie (sauf si "all")
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

// Fonction pour mettre à jour l'affichage
function updateDisplay() {
    const filteredDishes = filterDishes();


    dishesCountElement.textContent = `Affichage de ${filteredDishes.length} plat${filteredDishes.length > 1 ? 's' : ''}`;


    dishesContainer.innerHTML = "";


    filteredDishes.forEach(dish => {
        const dishCard = document.createElement("div");
        dishCard.className = "dish-card";

        dishCard.innerHTML = `
            <h3 class="dish-name">${dish.name}</h3>
            <p class="dish-price">${dish.price}€</p>
            <span class="dish-category">${dish.category}</span>
            <p class="dish-chef">Chef: ${dish.chef}</p>
            
        `;

        dishesContainer.appendChild(dishCard);
    });
} 