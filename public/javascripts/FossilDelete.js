var FossilDelete = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

FossilDelete.prototype.elementSelector = '[data-delete-fossil="true"]';
FossilDelete.prototype.fossilRowSelector = '.fossil-row';

FossilDelete.prototype.registerEvents = function() {
    this.elements.forEach((element) => {
        element.addEventListener('click', this.onLinkClick.bind(this, element));
    });
};

FossilDelete.prototype.onLinkClick = function(element, event) {
    event.preventDefault();

    new Modal(
        'Löschen',
        'Wollen Sie das Fossil wirklich löschen?<br><br>Bestätigen Sie das Löschen, indem Sie die Fossil-Nummer angeben.',
        Modal.prototype.CONFIRM_INPUT,
        this.onModalYesButtonClick.bind(this, element),
        null,
        element.dataset.fossilNumber
    );
};

FossilDelete.prototype.onModalYesButtonClick = function(element, input) {
    document.LoadingIndicator.show();

    let inputUrl = new URL(element.getAttribute("href"));
    inputUrl.searchParams.append('fossilNumber', input);

    new Ajax(inputUrl.toString(), this.ajaxCallback.bind(this, element));
};

FossilDelete.prototype.ajaxCallback = function(element) {
    element.closest(this.fossilRowSelector).remove();

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Das Fossil wurde gelöscht');
};

document.addEventListener("DOMContentLoaded", function() {
    new FossilDelete();
});