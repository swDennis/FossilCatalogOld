var Ajax = function(url, successCallback, errorCallback) {
    const xhttp = new XMLHttpRequest();
    const me = this;

    xhttp.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                if (me._isFunction(successCallback)) {
                    successCallback(JSON.parse(this.responseText));
                } else {
                    console.error('No success callback provided')
                }
            } else {
                const response = JSON.parse(this.responseText);
                let message = response.message;

                if (response.trace) {
                    message += '<br>' + response.trace
                }

                new Toast('Fehler', message);

                if (me._isFunction(errorCallback)) {
                    errorCallback.call();
                }
            }
        }
    };

    xhttp.open("POST", url, true);
    xhttp.send();
};

Ajax.prototype._isFunction = function(callback) {
    return callback && {}.toString.call(callback) === '[object Function]';
};