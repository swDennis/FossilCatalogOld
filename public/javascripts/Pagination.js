var Pagination = function() {
    this.elements = document.querySelectorAll(this.elementsSelector);

    this.registerEvents();
};

Pagination.prototype.elementsSelector = '.page-link';
Pagination.prototype.pageInputSelector = 'input[name="page"]';
Pagination.prototype.filterFormSelector = 'form[name="filter"]';

Pagination.prototype.registerEvents = function() {
    this.elements.forEach((link) => {
        link.addEventListener('click', this.onLinkClick.bind(this, link));
    });
};

Pagination.prototype.onLinkClick = function(link, event) {
    event.preventDefault();

    const pageInput = document.querySelector(this.pageInputSelector);
    pageInput.value = link.dataset.page;

    const form = document.querySelector(this.filterFormSelector);
    form.submit();
};

document.addEventListener("DOMContentLoaded", function() {
    new Pagination();
});