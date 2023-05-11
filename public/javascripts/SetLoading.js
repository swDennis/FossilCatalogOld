var SetLoading = function(element, append = true, asOverlay = false) {
    this.element = element;
    this.append = append;
    this.asOverLay = asOverlay;

    this.type = element.nodeName;

    this.setElementLoading();
};

SetLoading.TYPE = {
    BUTTON: 'BUTTON'
};

SetLoading.prototype.relativeClass = 'position-relative';

SetLoading.prototype.setElementLoading = function() {
    if (this.append) {
        if (!this.asOverLay) {
            this.element.append(this.createPlaceHolder());
        }

        this.element.append(this.createSpinner());

        return;
    }

    if (!this.asOverLay) {
        this.element.append(this.createPlaceHolder());
    }

    this.element.prepend(this.createSpinner());
};


SetLoading.prototype.createSpinner = function() {
    const spinnerContainer = document.createElement('span');
    spinnerContainer.classList.add('overlaySpinner');

    this.indicator = document.createElement('span');

    this.indicator.classList.add('spinner-border');
    this.indicator.classList.add('spinner-border-sm');

    if (this.asOverLay) {
        this.indicator.classList.add('overlay');
        spinnerContainer.append(this.indicator);
        this.indicator = spinnerContainer;
        this.element.classList.add(this.relativeClass)
    }

    return this.indicator;
};

SetLoading.prototype.remove = function() {
    this.indicator.remove();
    if (this.placeholder) {
        this.placeholder.remove();
    }

    this.element.classList.remove(this.relativeClass);
};

SetLoading.prototype.createPlaceHolder = function() {
    this.placeholder = document.createElement('span');

    this.placeholder.innerHTML = '&nbsp;'

    return this.placeholder;
};
