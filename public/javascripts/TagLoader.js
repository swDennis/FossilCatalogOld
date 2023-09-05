var TagLoader = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

TagLoader.prototype.elementSelector = '[data-isTagField="true"]';
TagLoader.prototype.ajaxCallRegister = [];

TagLoader.prototype.registerEvents = function() {
    this.elements.forEach((select) => {
        select.addEventListener('change', this.onSelectItem.bind(this, select));
    });
};

TagLoader.prototype.onSelectItem = function(select) {
    document.LoadingIndicator.show();

    const selected = Array.from(select.selectedOptions).map(({ value }) => value),
        url = new URL(select.dataset.url);

    url.searchParams.append('selectField', select.name.replace('[]', ''));
    url.searchParams.append('values', selected.join(','));

    this.elements.forEach((element) => {
        if (element.name === select.name) {
            return;
        }

        this.registerAjaxCall(element.name);
        new Ajax(url.toString(), this.onResultLoaded.bind(this, element));
    });
};

TagLoader.prototype.onResultLoaded = function(element, response) {
    const selected = Array.from(element.selectedOptions).map(({ value }) => value);

    element.innerHTML = '';
    element.appendChild(new Option('Keine', '0'));
    response.tags.forEach((tag) => {
        element.appendChild(new Option(tag.name, tag.id, false, selected.contains(tag.id)));
    });

    this.unregisterAjaxCall(element.name);
    document.LoadingIndicator.hide();
};

TagLoader.prototype.registerAjaxCall = function(fieldName) {
    if (this.ajaxCallRegister.contains(fieldName)) {
        throw new Error('field with name: %s is already registered'.replace('%s', fieldName));
    }

    this.ajaxCallRegister.push(fieldName);
};

TagLoader.prototype.unregisterAjaxCall = function(fieldName) {
    const index = this.ajaxCallRegister.indexOf(fieldName);

    if (index > -1) {
        this.ajaxCallRegister.splice(index, 1);
    }
};

document.addEventListener("DOMContentLoaded", function() {
    new TagLoader();
});