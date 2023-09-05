var SubmutButtonDoubleClick = function(selector) {
    this.elements = document.querySelectorAll(selector);
    this.registerEvents();
};

SubmutButtonDoubleClick.prototype.registerEvents = function() {
    this.elements.forEach((element) => {
        element.addEventListener('click', this.onclick.bind(this));
    });
};

SubmutButtonDoubleClick.prototype.onclick = function(event) {
    if (event.currentTarget.isAlreadyClicked) {
        event.preventDefault();
        return;
    }

    event.currentTarget.isAlreadyClicked = true;
};

document.addEventListener("DOMContentLoaded", function() {
    new SubmutButtonDoubleClick('button[type="submit"]');
});