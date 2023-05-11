var ZipExport = function() {
    this.elements = document.querySelectorAll(this.elementSelector);

    this.registerEvents();
};

ZipExport.prototype.elementSelector = 'a[data-downloadButton="true"]';

ZipExport.prototype.registerEvents = function() {
    this.elements.forEach((downloadButton) => {
        downloadButton.addEventListener('click', this.onButtonClick.bind(this));
    });
};

ZipExport.prototype.onButtonClick = function(event) {
    event.preventDefault();
    this.loadingIndicator = new SetLoading(event.currentTarget, true, true);

    const url = event.currentTarget.href;

    new Ajax(url, this.onZipIsPrepared.bind(this, event.currentTarget));
};

ZipExport.prototype.onZipIsPrepared = function(element, response) {
    const url = new URL(element.dataset.downloadurl);
    url.searchParams.append('filename', response.zipFile)

    const downloadLink = document.createElement('a');
    downloadLink.href = url.toString();
    downloadLink.click();

    this.loadingIndicator.remove();
};

document.addEventListener("DOMContentLoaded", function() {
    new ZipExport();
});