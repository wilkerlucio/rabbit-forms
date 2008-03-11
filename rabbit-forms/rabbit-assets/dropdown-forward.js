/**
 * Load select data from json url return
 */
forwardSelect = function(url, source, destiny, data) {
    data = data || {};

    var sourceEl = $('#' + source).get(0);

    data.value = sourceEl.value;
    sourceEl.disabled = true;

    var obj = $('#' + destiny).get(0);

    while(obj.options.length > 0)
        obj.options[0] = null;

    var opt = document.createElement('option');
    opt.setAttribute('value', '0');
    opt.innerHTML = 'Loading...';

    obj.appendChild(opt);

    $.post(url, data, function() {
        var src = source;
        var dst = destiny;

        return function(data) {
            var obj = $('#' + dst).get(0);

            while(obj.options.length > 0)
                obj.options[0] = null;

            for(var p in data) {
                var opt = document.createElement('option');
                opt.setAttribute('value', p);
                opt.innerHTML = data[p];

                obj.appendChild(opt);
            }

            $('#' + src).get(0).disabled = false;
        };
    }(), "json");
};