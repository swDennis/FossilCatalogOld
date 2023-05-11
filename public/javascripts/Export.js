var Export = function() {
    this.button = document.querySelector(this.buttonSelector);
    this.progressBars = document.querySelectorAll(this.progressBarSelector);

    if (!this.button) {
        return;
    }


    this.registerEvents();
};

Export.prototype.buttonSelector = '#exportButton';
Export.prototype.progressBarSelector = '.progress-bar';
Export.prototype.progressBarStripedClass = 'progress-bar-striped';
Export.prototype.progressBarAnimatedClass = 'progress-bar-animated';

Export.prototype.registerEvents = function() {
    this.button.addEventListener('click', this.onClickButton.bind(this));
};

Export.prototype.onClickButton = function(event) {
    event.preventDefault();

    this.progressBars.forEach((progressBar) => {
        if (!progressBar.classList.contains(this.progressBarStripedClass)) {
            progressBar.classList.add(this.progressBarStripedClass);
        }

        if (!progressBar.classList.contains(this.progressBarAnimatedClass)) {
            progressBar.classList.add(this.progressBarAnimatedClass);
        }

        progressBar.style.width = '0%';
    });

    new Modal(
        'Export',
        '<p>Wollen Sie den Export starten?</p><p>Dieser Vorgang kann je nach Anzahl der Einträge einige Zeit in anspruch nehmen!</p>',
        Modal.prototype.OK_CANCEL_BUTTON,
        this.onConfirmExport.bind(this)
    );
};

Export.prototype.onConfirmExport = function() {
    this.button.disabled = true
    this.loadingIndicator = new SetLoading(this.button);
    this.export();
};

Export.prototype.export = function() {
    new Ajax(this.button.dataset.url, this.onSuccess.bind(this));
};

Export.prototype.onSuccess = function(response) {
    let allFinished = true;
    Object.keys(response.status).forEach((status) => {
        if (!response.status[status].isFinished) {
            allFinished = false;
        }

        this.setViewState(response.status[status]);
    });

    if (allFinished) {
        new Ajax(this.button.dataset.clearurl, this.onSessionCleared.bind(this));

        return;
    }

    this.export();
};

Export.prototype.onSessionCleared = function() {
    this.loadingIndicator.remove();

    this.button.disabled = false;
};

Export.prototype.setViewState = function(status) {
    const selector = 'div[data-type="%s"]'.replace('%s', status.type)
    const progressBar = document.querySelector(selector);

    const onStep = 100 / status.toExport;
    const currentPercent = onStep * status.exported;

    progressBar.style.width = currentPercent + '%';

    if (status.isFinished) {
        progressBar.classList.remove(this.progressBarStripedClass);
        progressBar.classList.remove(this.progressBarAnimatedClass);

        progressBar.style.width = '100%';
    }
};

document.addEventListener("DOMContentLoaded", function() {
    new Export();
});