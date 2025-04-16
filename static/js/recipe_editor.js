$(function() {

    // ingredients

    var ingredients_line = $('#ingredients_list .ingredients_entry').first().clone();
    ingredients_line.find('input[type="checkbox"]').prop( "checked", false );
    ingredients_line.find('input[type="text"]').val('');

    function removeIngredients () {
        $( this ).closest('.ingredients_entry').remove();
    }

    function addIngredients() {
        var new_line = ingredients_line.clone();
        $('#ingredients_list').append(new_line);
        new_line.find('.btn_remove').on("click", removeIngredients);
        new_line.find('input[data-type="ingredients_unit"]').combobox({list: unit_list});
        new_line.find('input[data-type="ingredients_name"]').combobox({list: ingredients_list});
    }

    function beforeSubmitIngredients() {
        if ($('#ingredients_list .ingredients_entry').length == 0) {
            addIngredients();
        }
        var cnt = 0;
        $('#ingredients_list .ingredients_entry').each(function() {
            cnt++;
            $(this).find('input').each(function(){
                $(this).attr('name', $(this).attr('name').replace(/\d+/, cnt));
            });
        });
        $('input[name="cnt_ingredients"]').val(cnt);
    }

    $('#add_ingredients').on("click", addIngredients);
    $('#ingredients_list .btn_remove').on("click", removeIngredients);

    $('#ingredients_list input[data-type="ingredients_unit"]').combobox({list: unit_list});
    $('#ingredients_list input[data-type="ingredients_name"]').combobox({list: ingredients_list});

    // step

    var step_line = $('#steps_list .field-line').first().clone();
    step_line.find('textarea').val('');

    function removeStep () {
        $( this ).closest('.field-line').remove();
        var cnt = 0;
        $('#steps_list .field-line').each(function() {
            cnt++;
            $(this).find('label').html($(this).find('label').html().replace(/\d+/, cnt));
        });
    }

    function addStep() {
        var new_line = step_line.clone();
        $('#steps_list').append(new_line);
        var cnt = $('#steps_list .field-line').length;
        new_line.find('label').html(new_line.find('label').html().replace(/\d+/, cnt));
        
    }

    function beforeSubmitStep() {
        if ($('#steps_list .field-line').length == 0) {
            addStep();
        }
        var cnt = 0;
        $('#steps_list .field-line').each(function() {
            cnt++;
            $(this).find('label').html($(this).find('label').html().replace(/\d+/, cnt));
            $(this).find('textarea').attr('name', $(this).find('textarea').attr('name').replace(/\d+/, cnt));
        });
        $('input[name="cnt_step"]').val(cnt);
    }

    $('#add_step').on("click", addStep);
    $('#steps_list .btn_remove').on("click", removeStep);

    // image

    function loadImage(image_name) {
        if (!image_name) {
            $('#image_preview').hide();
            return;
        }
        $('#image_preview .field-wrapper img').remove();
        $('#image_preview .field-wrapper').prepend('<img src="/gallery/recipes/'+image_name+'_s.webp" alt="">');
        $('#image_preview').show();
    }
    loadImage(image_name);

    // tag-list

    var input = document.querySelector('input[name="tag_list"]'),
    tagify = new Tagify(input, {
        whitelist: tag_list,
        dropdown: {
            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    })
    
    // submit

    $('.recipe_editor_form').ajax_form({
        'beforesubmit': function(form, button) {
            beforeSubmitIngredients();
            beforeSubmitStep();
        },
        'success': function(form, button, data) {
            tag_list = data.tag_list;
            unit_list = data.unit_list;
            ingredients_list = data.ingredients_list;

            $('#recipe_editor input[name="recipe_id"]').val(data.recipe_id);
            $('#recipe_editor input[name="del_image"]').prop('checked', false);
            loadImage(data.image_name);
            tagify.whitelist = data.tag_list;
            $('#ingredients_list input[data-type="ingredients_unit"]').combobox('update_list', data.unit_list);
            $('#ingredients_list input[data-type="ingredients_name"]').combobox('update_list', data.ingredients_list);
        }
    });
});

