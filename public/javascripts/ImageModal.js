var ImageModal = function() {
    this.element = document.querySelector(this.elementSelector);
    if (!this.element) {
        return;
    }
    this.links = this.element.querySelectorAll(this.linkSelector);
    this.modalContainer = document.querySelector(this.imageModalSelector);
    this.modalCloseButtons = this.modalContainer.querySelectorAll(this.imageModalCloseButtonSelector);
    this.images = this.modalContainer.querySelectorAll(this.imageSelector);

    this.registerEvents();
};

ImageModal.prototype.elementSelector = '[data-image-gallery="true"]';
ImageModal.prototype.linkSelector = '[data-image-link="true"]';
ImageModal.prototype.imageModalSelector = '.image-modal';
ImageModal.prototype.imageModalCloseButtonSelector = '[data-bs-dismiss="modal"]';
ImageModal.prototype.imageSelector = '.image';
ImageModal.prototype.hiddenClass = 'hidden';

ImageModal.prototype.registerEvents = function() {
    this.links.forEach((link) => {
        link.addEventListener('click', this.onImageLinkClick.bind(this));
    });

    this.modalCloseButtons.forEach((closeButton) => {
        closeButton.addEventListener('click', this.onCloseButtonClick.bind(this));
    });
};

ImageModal.prototype.onImageLinkClick = function(event) {
    const currentImageId = event.currentTarget.dataset.imageid;

    this.images.forEach((image) => {
        image.classList.add(this.hiddenClass);

        if (image.dataset.imageid === currentImageId) {
            image.classList.remove(this.hiddenClass);
        }
    });

    this.showModal();
};

ImageModal.prototype.onCloseButtonClick = function(event) {
    this.hideModal();
};

ImageModal.prototype.showModal = function() {
    this.modalContainer.style.display = 'block';
};

ImageModal.prototype.hideModal = function() {
    this.modalContainer.style.display = 'none';
};

document.addEventListener("DOMContentLoaded", function() {
    new ImageModal();
});