var ImageIsMainImage = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

ImageIsMainImage.prototype.elementSelector = '.set-as-main-image';
ImageIsMainImage.prototype.hiddenClass = 'hidden';

ImageIsMainImage.prototype.registerEvents = function() {
    this.elements.forEach((link) => {
        link.addEventListener('click', this.onLinkClick.bind(this, link));
    });
};

ImageIsMainImage.prototype.onLinkClick = function(link, event) {
    event.preventDefault();
    document.LoadingIndicator.show();

    new Ajax(link.getAttribute("href"), this.ajaxCallback.bind(this, { link: link }));
};

ImageIsMainImage.prototype.ajaxCallback = function (response) {
    this.elements.forEach((link) => {
        link.classList.remove(this.hiddenClass);
    });

    response.link.classList.add(this.hiddenClass);

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Die Einstellung wurde erfolgreich gespeichert');
};

document.addEventListener("DOMContentLoaded", function() {
    new ImageIsMainImage();
});