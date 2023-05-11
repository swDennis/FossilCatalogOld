var LoadingIndicator = function() {
    this.element = document.querySelector(this.elementSelector)
};

LoadingIndicator.prototype.elementSelector = '.page-loading-indicator';
LoadingIndicator.prototype.showClass = 'show-loading-indicator';

LoadingIndicator.prototype.show = function () {
    this.element.classList.add(this.showClass);
};

LoadingIndicator.prototype.hide = function () {
    this.element.classList.remove(this.showClass);
};

document.addEventListener("DOMContentLoaded", function() {
    document.LoadingIndicator = new LoadingIndicator();
});