$(function() {
    $('.recipe_editor_form').ajax_form({
        'success': function(form, button, data) {
            $('#recipe_editor input[name="recipe_id"]').val(data.recipe_id)
        }
    });
    var input = document.querySelector('input[name="tag_list"]'),
    tagify = new Tagify(input, {
        whitelist: tag_list,
        dropdown: {
            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    })
    $('input[data-type="ingredients_unit').combobox({list: unit_list});
    $('input[data-type="ingredients_name').combobox({list: ingredients_list});
});

