var databaseColumnNameHelper = function(selector) {
    this.elements = document.querySelectorAll(selector);
    if (this.elements.length <= 0) {
        return;
    }

    this.registerEvents();
};

databaseColumnNameHelper.prototype.registerEvents = function() {
    this.elements.forEach((element) => {
        element.addEventListener('keydown', this.onChange.bind(this));
    });
};

databaseColumnNameHelper.prototype.onChange = function(event) {
    const pattern = /[a-z]|_/;

    if (!event.key.match(pattern)) {
        event.preventDefault();
    }
};

document.addEventListener("DOMContentLoaded", function() {
    new databaseColumnNameHelper('[data-database-column-name-helper="true"]');
});