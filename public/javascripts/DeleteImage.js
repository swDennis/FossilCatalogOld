var DeleteImage = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

DeleteImage.prototype.elementSelector = '.delete-image-link';
DeleteImage.prototype.imageCardSelector = 'div[data-delete-image-id="%s"]';

DeleteImage.prototype.registerEvents = function() {
    this.elements.forEach((link) => {
        link.addEventListener('click', this.onLinkClick.bind(this, link));
    });
};

DeleteImage.prototype.onLinkClick = function(link, event) {
    event.preventDefault();

    new Modal('Löschen', 'Wollen Sie das Bild wirklich löschen?', Modal.prototype.YES_NO_BUTTONS, this.onModalYesButtonClick.bind(this, link));
};

DeleteImage.prototype.onModalYesButtonClick = function(link) {
    document.LoadingIndicator.show();

    new Ajax(link.getAttribute("href"), this.ajaxCallback.bind(this, link), this.ajaxErrorCallback.bind(this));
};

DeleteImage.prototype.ajaxCallback = function(link) {
    const imageId = link.dataset.imageid;
    
    document.querySelector(this.imageCardSelector.replace('%s', imageId)).remove();

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Das Bild wurde gelöscht');
};

DeleteImage.prototype.ajaxErrorCallback = function () {
    document.LoadingIndicator.hide();
};

document.addEventListener("DOMContentLoaded", function() {
    new DeleteImage();
});