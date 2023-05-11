var TagDelete = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

TagDelete.prototype.elementSelector = '[data-delete-tag="true"]';
TagDelete.prototype.rowSelector = '.tag-row';

TagDelete.prototype.registerEvents = function() {
    this.elements.forEach((element) => {
        element.addEventListener('click', this.onElementClick.bind(this, element));
    });
};

TagDelete.prototype.onElementClick = function (element, event) {
    event.preventDefault();

    new Modal('Löschen', 'Wollen Sie das Tag wirklich löschen?', Modal.prototype.YES_NO_BUTTONS, this.onConfirmDelete.bind(this, element));
};

TagDelete.prototype.onConfirmDelete = function (element) {
    document.LoadingIndicator.show();

    new Ajax(element.getAttribute("href"), this.ajaxCallback.bind(this, element));
};

TagDelete.prototype.ajaxCallback = function (element) {
    element.closest(this.rowSelector).remove();

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Das Tag wurde gelöscht');
};

document.addEventListener("DOMContentLoaded", function() {
    new TagDelete();
});