var Toast = function(toastTitle, toastContent) {
    this.toastContainer = document.querySelector(this.toastContainerSelector);
    this.toastTemplate = document.querySelector(this.toastTemplateSelector).content.cloneNode(true);
    this.toastTitleContainer = this.toastTemplate.querySelector(this.toastTitleSelector);
    this.toastContentContainer = this.toastTemplate.querySelector(this.toastContentSelector);
    this.toastCloseButton = this.toastTemplate.querySelector(this.toastCloseButtonSelector);

    this.toastTitleContainer.innerHTML = toastTitle;
    this.toastContentContainer.innerHTML = toastContent;

    this.toastContainer.append(this.toastTemplate);

    this.registerEvents();
};

Toast.prototype.toastContainerSelector = '.toast-container';
Toast.prototype.toastTemplateSelector = '#toast-template-item';
Toast.prototype.toastTitleSelector = '.toast-title';
Toast.prototype.toastContentSelector = '.toast-content';
Toast.prototype.toastCloseButtonSelector = '.toast-close-button';

Toast.prototype.registerEvents = function() {
    this.toastCloseButton.addEventListener('click', this.onCloseButtonClick.bind(this));
};

Toast.prototype.onCloseButtonClick = function(event) {
    this.toastCloseButton.removeEventListener('click', this.onCloseButtonClick.bind(this));

    event.currentTarget.closest('.toast').remove();
};
