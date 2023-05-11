var autocomplete = function(selector) {
    var $elements = document.querySelectorAll(selector);

    $elements.forEach((element) => {
        new autocompleteField(element);
    });
};


var autocompleteField = function($field) {
    this.$input = $field.querySelector(this.inputSelector);
    this.$searchListElement = $field.querySelector(this.searchListElementSelector);

    if (this.$searchListElement === null) {
        return;
    }

    if (!this.$searchListElement.dataset.searchList) {
        return;
    }

    this.searchList = JSON.parse(this.$searchListElement.dataset.searchList);

    this.registerEvents();
};

autocompleteField.prototype.$input = null;
autocompleteField.prototype.$searchListElement = null;
autocompleteField.prototype.searchList = null;
autocompleteField.prototype.inputSelector = 'input'
autocompleteField.prototype.searchListElementSelector = '.autocomplete-search-list'
autocompleteField.prototype.autocompleteListItemClass = 'autocomplete-value-list-item'

autocompleteField.prototype.registerEvents = function() {
    this.$input.addEventListener('input', this.onChangeInput.bind(this));
    this.$input.addEventListener('keydown', this.onKeyDown.bind(this));
};

autocompleteField.prototype.registerSelectItemEvents = function() {
    const searchListItems = this.$searchListElement.querySelectorAll('div[class=' + this.autocompleteListItemClass + ']');
    searchListItems.forEach((item) => {
        item.addEventListener('click', this.onSelectListItem.bind(this), {once: true});
    });
};

autocompleteField.prototype.onChangeInput = function(event) {
    const currentValue = event.target.value;
    if (currentValue.length < 3) {
        return;
    }

    const colonCurrentValue = currentValue.colophonetics().replace('0', '');
    this.$searchListElement.innerHTML = '';
    index = 0;
    this.searchList.forEach((item) => {
        if (item.colophonetics().includes(colonCurrentValue)) {
            const listItem = document.createElement("div");
            listItem.classList.add(this.autocompleteListItemClass);
            listItem.innerHTML = item;
            listItem.dataset.value = item;
            listItem.dataset.index = index;

            this.$searchListElement.appendChild(listItem);
            index++;
        }
    });

    this.registerSelectItemEvents();
    this.showSearchListContainer();
};

autocompleteField.prototype.onKeyDown = function(event) {
    new listAction(event.key.toLowerCase(), this.$searchListElement, event);
};

autocompleteField.prototype.onBlurInput = function() {
    this.hideSearchListContainer();
};

autocompleteField.prototype.onSelectListItem = function(event) {
    this.$input.value = event.target.dataset.value;
    this.hideSearchListContainer();
};

autocompleteField.prototype.showSearchListContainer = function() {
    this.$searchListElement.style.display = 'block';
};

autocompleteField.prototype.hideSearchListContainer = function() {
    this.$searchListElement.style.display = 'none';
};


var listAction = function(action, $list, event) {
    if (!this.supportedActions.includes(action)) {
        return;
    }

    this.$list = $list;
    this.$listItem = this.$list.querySelector(this.activeSelector);

    if (this.$listItem === null) {
        this.currentIndex = -1;
    } else {
        this.currentIndex = this.$listItem.dataset.index;
    }

    this[action](event);
};

listAction.prototype.activeClass = 'active';
listAction.prototype.activeSelector = '.active';
listAction.prototype.autocompleteListItemSelector = '.autocomplete-value-list-item'
listAction.prototype.supportedActions = [
    'arrowdown',
    'arrowup',
    'enter'
];

listAction.prototype.arrowdown = function(event) {
    event.preventDefault();

    this.currentIndex++;

    if (this.$listItem !== null) {
        this.$listItem.classList.remove(this.activeClass);
    }

    let nextItem = this.$list.querySelector(this._getSelector());
    if (nextItem === null) {
        this.currentIndex = 0;
        nextItem = this.$list.querySelector(this._getSelector());
    }

    nextItem.classList.add(this.activeClass);
};

listAction.prototype.arrowup = function(event) {
    event.preventDefault();

    const lastIndex = this.$list.querySelectorAll(this.autocompleteListItemSelector).length - 1;

    --this.currentIndex;

    if (this.currentIndex < 0) {
        this.currentIndex = lastIndex;
    }

    if (this.$listItem !== null) {
        this.$listItem.classList.remove(this.activeClass);
    }

    this.$list.querySelector(this._getSelector()).classList.add(this.activeClass);
};

listAction.prototype.enter = function(event) {
    event.preventDefault();
    const activeItem = this.$list.querySelector(this.activeSelector);
    if (activeItem === null) {
        this.$list.style.display = 'none';
        return;
    }

    event.target.value = activeItem.dataset.value;

    this.$list.style.display = 'none';
};

listAction.prototype._getSelector = function() {
    return `div[data-index="${this.currentIndex}"]`;
};

document.addEventListener("DOMContentLoaded", function() {
    new autocomplete('div[class=autocomplete]');
});
