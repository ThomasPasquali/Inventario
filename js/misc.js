function getFormAsJSON($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function showAlert(content, type) {
    const btn = $('<button type="button" class="close"></button>');
    btn.attr('data-dismiss', 'alert');
    btn.attr('aria-label', 'Close');
    btn.append($('<span aria-hidden="true">&times;</span>'));

    const div = $('<div></div>');
    div.addClass(`alert alert-${type} alert-dismissible fade show`);
    div.attr('role', 'alert');
    div.text(content);
    div.append(btn);

    $('body').prepend(div);
}