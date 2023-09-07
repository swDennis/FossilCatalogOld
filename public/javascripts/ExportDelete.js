var ExportDelete = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

ExportDelete.prototype.elementSelector = 'a[data-deleteButton="true"]';

ExportDelete.prototype.registerEvents = function() {
    this.elements.forEach((element) => {
        element.addEventListener('click', this.onClick.bind(this, element));
    });
};

ExportDelete.prototype.onClick = function(element, event) {
    event.preventDefault();

    new Modal('Löschen', 'Wollen Sie das Backup wirklich löschen?', Modal.prototype.YES_NO_BUTTONS, this.onModalYesButtonClick.bind(this, element));
};

ExportDelete.prototype.onModalYesButtonClick = function(element) {
    document.LoadingIndicator.show();

    new Ajax(element.getAttribute("href"), this.ajaxCallback.bind(this, element), this.ajaxErrorCallback.bind(this));
};

ExportDelete.prototype.ajaxCallback = function(element) {
    element.closest('tr').remove();
    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Das Backup wurde gelöscht');
};

ExportDelete.prototype.ajaxErrorCallback = function () {
    document.LoadingIndicator.hide();
};

document.addEventListener("DOMContentLoaded", function() {
    new ExportDelete();
});