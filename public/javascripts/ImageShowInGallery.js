var ImageShowInGallery = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

ImageShowInGallery.prototype.elementSelector = '.show-in-gallery-link';
ImageShowInGallery.prototype.hiddenClass = 'hidden';
ImageShowInGallery.prototype.buddyLinkAddSelector = 'a[data-imageidadd="%s"]';
ImageShowInGallery.prototype.buddyLinkRemoveSelector = 'a[data-imageidremove="%s"]';


ImageShowInGallery.prototype.registerEvents = function() {
    this.elements.forEach((link) => {
        link.addEventListener('click', this.onLinkClick.bind(this, link));
    });
};

ImageShowInGallery.prototype.onLinkClick = function(link, event) {
    event.preventDefault();
    document.LoadingIndicator.show();

    new Ajax(link.getAttribute("href"), this.ajaxCallback.bind(this, link));
};

ImageShowInGallery.prototype.ajaxCallback = function(link) {
    const imageAdd = link.dataset.imageidadd,
        imageRemove = link.dataset.imageidremove;

    let buddyLink;

    if (imageAdd) {
        buddyLink = document.querySelector(this.buddyLinkRemoveSelector.replace('%s', imageAdd));
    }

    if (imageRemove) {
        buddyLink = document.querySelector(this.buddyLinkAddSelector.replace('%s', imageRemove));
    }

    link.classList.add(this.hiddenClass);
    buddyLink.classList.remove(this.hiddenClass);

    document.LoadingIndicator.hide();

    new Toast('Erfolg', 'Die Einstellung wurde erfolgreich gespeichert');
};

document.addEventListener("DOMContentLoaded", function() {
    new ImageShowInGallery();
});