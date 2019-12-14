var translit = {
    replace: {},
    t: function(text) {
        var s = '', c;
        for (var i = 0; i < text.length; i++) {
            c = text[i];
            if (c in this.replace) {
                s += this.replace[c];
            } else {
                s += c;
            }
        }
        s = s.replace(/[^\-0-9a-z]/ig, '').replace(/\-+/g, '-').replace(/^\-*(.*?)\-*$/, '$1');
        return s;
    }
}

$('[data-translit="button"]').each(function() {
    var $button = $(this),
        $source = $('#' + $button.data('translit-source')),
        $target = $('#' + $button.data('translit-target'));
    if (typeof $button.data('translit-auto') != 'undefined') {
        $source.data({'translit-button': $button, 'translit-target': $target}).on('blur', function() {
            var $source = $(this),
                $button = $source.data('translit-button'),
                $target = $source.data('translit-target');
            if ($target.length && $target.val() == '') {
                $button.trigger('click');
            }
        });
    }
});

$(document).on('click', '[data-translit="button"]', function(e) {
    e.preventDefault();
    var $button = $(this),
        $source = $('#' + $button.data('translit-source')),
        $target = $('#' + $button.data('translit-target'));
    $target.val(translit.t($source.val()));
});
