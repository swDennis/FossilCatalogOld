var Import = function() {
    this.progressBars = document.querySelectorAll(this.progressBarSelector);
    this.button = document.querySelector(this.importButtonSelector);

    if (!this.button) {
        return;
    }

    this.registerEvents();
};

Import.prototype.progressBarSelector = '.progress-bar';
Import.prototype.importButtonSelector = '#importButton';
Import.prototype.progressBarStripedClass = 'progress-bar-striped';
Import.prototype.progressBarAnimatedClass = 'progress-bar-animated';

Import.prototype.registerEvents = function () {
    this.button.addEventListener('click', this.onClickButton.bind(this));
};



Import.prototype.onClickButton = function(event) {
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
        'Import',
        '<h5>Achtung!</h5><p class="text-bg-warning p-3 border text-center rounded">Führen Sie diese Vorgang ausschließlich nach einem Datenverlust aus! Die Datenbank wird vor dem Import geleert!</p><p>Dieser Vorgang kann je nach Anzahl der Einträge einige Zeit in anspruch nehmen!</p>',
        Modal.prototype.OK_CANCEL_BUTTON,
        this.onConfirmImport.bind(this)
    );
};

Import.prototype.onConfirmImport = function () {
    this.button.disabled = true
    this.loadingIndicator = new SetLoading(this.button);
    this.import();
};

Import.prototype.import = function() {
    new Ajax(this.button.dataset.url, this.importCallback.bind(this));
};

Import.prototype.importCallback = function(response) {
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

    this.import();
};

Import.prototype.onSessionCleared = function() {
    this.loadingIndicator.remove();

    new Modal(
        'Importiert',
        '<p>Die Daten wurden vollständig importiert</p><p>Bitte überprüfen Sie Ihren Katalog. Sollten Fehler aufgetreten sein melden Sie sich bitte beim Entwickler.</p>',
        Modal.prototype.OK_BUTTON
    );
};

Import.prototype.setViewState = function(status) {
    const selector = 'div[data-type="%s"]'.replace('%s', status.type)

    const progressBar = document.querySelector(selector);

    const onStep = 100 / status.toImport;
    const currentPercent = onStep * status.imported;

    progressBar.style.width = currentPercent + '%';

    if (status.isFinished) {
        progressBar.classList.remove(this.progressBarStripedClass);
        progressBar.classList.remove(this.progressBarAnimatedClass);

        progressBar.style.width = '100%';
    }
};

document.addEventListener("DOMContentLoaded", function() {
    new Import();
});