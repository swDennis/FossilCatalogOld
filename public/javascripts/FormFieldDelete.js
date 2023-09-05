var FormFieldDelete = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

FormFieldDelete.prototype.elementSelector = '[data-delete-form-field="true"]';
FormFieldDelete.prototype.rowSelector = '.form-field-row';

FormFieldDelete.prototype.registerEvents = function () {
    this.elements.forEach((element) => {
        element.addEventListener('click', this.onElementClick.bind(this, element));
    });
};

FormFieldDelete.prototype.onElementClick = function (element, event) {
    event.preventDefault();

    new Modal('Löschen', 'Wollen Sie das Feld wirklich löschen?', Modal.prototype.YES_NO_BUTTONS, this.onConfirmDelete.bind(this, element));
};

FormFieldDelete.prototype.onConfirmDelete = function (element) {
    document.LoadingIndicator.show();

    new Ajax(element.getAttribute("href"), this.ajaxCallback.bind(this, element));
};

FormFieldDelete.prototype.ajaxCallback = function (element) {
    element.closest(this.rowSelector).remove();

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Das Feld wurde gelöscht');
};

document.addEventListener("DOMContentLoaded", function() {
    new FormFieldDelete();
});