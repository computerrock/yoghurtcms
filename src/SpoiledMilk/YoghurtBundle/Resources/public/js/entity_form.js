
$(document).ready(function() {

    // Select appropriate vocabulary terms
    var terms = $('#spoiledmilk_yoghurtbundle_entitytype_vocabularyTerms').val().split(',');

    if (terms.length > 0) {
        $('select.vocabulary option').each(function(index, element) {
            if (terms.indexOf(element.value) != -1)
                element.selected = true;
        });
    }

    $('#main_form').submit(function() {
        // Populate vocabularyTerms field with selected terms IDs
        var selectedIds = [];

        $('select.vocabulary option:selected').each(function(index, element) {
            selectedIds.push(element.value);
        });

        $('#spoiledmilk_yoghurtbundle_entitytype_vocabularyTerms').val(selectedIds.join(','));
    });

    // Apply "select2" where needed
    $('select.vocabulary').select2({
        'containerCssClass': 'input-xxlarge'
    });

});