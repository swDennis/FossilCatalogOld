var Modal = function(title, content, type, confirmButtonCallback, cancelButtonCallback, compareValue) {
    this.type = type;
    this.compareValue = compareValue;

    this.modalContainer = document.getElementById(this.modalBasicContainerSelector);
    this.modalTemplate = this.modalContainer.cloneNode(true);

    this.newId = 'instance-modal-%s'.replace('%s', Math.floor(Math.random() * 100))
    this.modalTemplate.setAttribute('id', this.newId);
    document.body.appendChild(this.modalTemplate);

    this.modal = document.getElementById(this.newId);

    this.modalTitleContainer = this.modal.querySelector(this.modalTitleSelector);
    this.modalContentContainer = this.modal.querySelector(this.modalContentSelector);
    this.modalTitleContainer.innerHTML = title;
    this.modalContentContainer.innerHTML = content;

    this.closeButton = this.modal.querySelector(this.closeButtonSelector);
    this.yesButton = this.modal.querySelector(this.yesButtonSelector);
    this.noButton = this.modal.querySelector(this.noButtonSelector);
    this.okButton = this.modal.querySelector(this.okButtonSelector);
    this.cancelButton = this.modal.querySelector(this.cancelButtonSelector);
    this.confirmInput = this.modal.querySelector(this.confirmInputSelector);

    this.handleButtonVisibility();

    this.registerEvents();

    this.confirmCallback = confirmButtonCallback;
    this.cancelCallback = cancelButtonCallback;

    this.showModal();
};

Modal.prototype.OK_BUTTON = 'ok';
Modal.prototype.OK_CANCEL_BUTTON = 'okcancel';
Modal.prototype.YES_NO_BUTTONS = 'yesno';
Modal.prototype.CONFIRM_INPUT = 'confirminput';

Modal.prototype.modalBasicContainerSelector = 'modal-template-container';
Modal.prototype.modalTitleSelector = '.modal-content .modal-title';
Modal.prototype.modalContentSelector = '.modal-content .modal-body .modal-content';
Modal.prototype.closeButtonSelector = '.close';
Modal.prototype.yesButtonSelector = '.yesButton';
Modal.prototype.noButtonSelector = '.noButton';
Modal.prototype.okButtonSelector = '.okButton';
Modal.prototype.cancelButtonSelector = '.cancelButton';
Modal.prototype.confirmInputSelector = 'input[name="confirmInput"]';

Modal.prototype.hiddenClass = 'hidden';
Modal.prototype.invalidClass = 'is-invalid';

Modal.prototype.registerEvents = function() {
    this.closeButton.addEventListener('click', this.onCloseButtonClick.bind(this));

    if (this.type === this.OK_BUTTON) {
        this.okButton.addEventListener('click', this.onConfirm.bind(this));
    }

    if (this.type === this.YES_NO_BUTTONS) {
        this.yesButton.addEventListener('click', this.onConfirm.bind(this));
        this.noButton.addEventListener('click', this.onCancel.bind(this));
    }

    if (this.type === this.OK_CANCEL_BUTTON || this.type === this.CONFIRM_INPUT) {
        this.okButton.addEventListener('click', this.onConfirm.bind(this));
        this.cancelButton.addEventListener('click', this.onCancel.bind(this));
    }
};

Modal.prototype.handleButtonVisibility = function() {
    if (this.type === this.OK_BUTTON) {
        this.okButton.classList.remove(this.hiddenClass);
    }

    if (this.type === this.YES_NO_BUTTONS) {
        this.yesButton.classList.remove(this.hiddenClass);
        this.noButton.classList.remove(this.hiddenClass);
    }

    if (this.type === this.OK_CANCEL_BUTTON || this.type === this.CONFIRM_INPUT) {
        this.okButton.classList.remove(this.hiddenClass);
        this.cancelButton.classList.remove(this.hiddenClass);
    }

    if (this.type === this.CONFIRM_INPUT) {
        this.confirmInput.classList.remove(this.hiddenClass);
    }
};

Modal.prototype.onCloseButtonClick = function() {
    this.hideModal();
};

Modal.prototype.onConfirm = function() {
    if (this.type === this.CONFIRM_INPUT) {
        if (this.compareValue !== this.confirmInput.value) {
            this.confirmInput.classList.add(this.invalidClass);

            return;
        }

        if (this._isFunction(this.confirmCallback)) {
            this.confirmCallback(this.confirmInput.value);

            this.hideModal();

            return;
        }
    }

    this.confirmInput.classList.remove(this.invalidClass);

    if (this._isFunction(this.confirmCallback)) {
        this.confirmCallback.call();
    }

    this.hideModal();
};

Modal.prototype.onCancel = function() {
    if (this._isFunction(this.cancelCallback)) {
        this.cancelCallback.call();
    }

    this.hideModal();
};

Modal.prototype.showModal = function() {
    this.modal.style.display = 'block';
};

Modal.prototype.hideModal = function() {
    this.modal.style.display = 'none';
    this.modal.remove();
};

Modal.prototype._isFunction = function(callback) {
    return callback && {}.toString.call(callback) === '[object Function]';
};
