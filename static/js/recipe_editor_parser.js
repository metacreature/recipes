/*

 File: recipe_editorr_parser.js
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 
 MIT License
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.

*/



$(function() {

    var parse_quantity = function(quantity) {
        quantity = quantity.replace('¼', '1/4').replace('½', '1/2').replace('¾', '3/4');
        quantity = quantity.trim().split(/[ \t]+/);
        var ret = '';
        if (quantity[0].match(/\//)) {
            if (typeof quantity[1] != 'undefined') {
                return '';
            }
            ret = eval(quantity[0]);
        } else {
            ret = parseFloat(quantity[0].replace(',', '.'));
        }
        if (typeof quantity[1] != 'undefined') {
            if (ret != Math.round(ret) || !quantity[1].match(/\//)) {
                return '';
            }
            ret += eval(quantity[1]);
        }
        return Math.round(ret *100) / 100;
    }

    var parse_unit = function(unit) {
        unit = unit.trim();
        for (repl in ingredients_parser_unit_translator) {
            if (unit.match(ingredients_parser_unit_translator[repl])) {
                return repl;
            }
        }
        return unit;
    }

    var parse_ingredenties = function(line) {
        let fields = Array.from(addIngredients().find('input'));

        var or = line.match(ingredients_parser_or) ? true : false;
        line = line.replace(ingredients_parser_or, '').trim();

        var quantity = line.match(new RegExp('^(([0-9,./ \t]+)([^0-9,./ \t]))'));
        var unit = '';
        if (quantity) {
            line = line.replace(quantity[0], quantity[3]).trim();
            quantity = parse_quantity(quantity[2]);
            unit = line.match(new RegExp('^(([^ \t]+)([ \t]))'));
            if (!unit) {
                return;
            }
            line = line.replace(new RegExp('^(([^ \t]+)([ \t]))'), '').trim();
            unit = parse_unit(unit[2])
        } else {
            quantity = '';
        }

        if (or) {
            $(fields[0]).prop( "checked", true );
        }
        $(fields[1]).val(quantity);
        $(fields[2]).val(unit).change();
        $(fields[3]).val(line).change();
    }

    var parse_original_text = function () {
        var original_text = $('#original_text').val().trim();

        if (!original_text) {
            return;
        }
            
        $('#steps_list').html('');

        // clean
        original_text = original_text.replace(new RegExp('[\n]', 'g'), '<xxpxx>');
        original_text = original_text.replace(new RegExp('[\n\r\t ]*<xxpxx>[\n\r\t ]*', 'g'), '<xxpxx>');
        var original_text_block = original_text.split(new RegExp('<xxpxx>(<xxpxx>)+', 'g'));
        original_text_block = original_text_block.filter((ele) => !(ele.match(new RegExp('^<xxpxx>(<xxpxx>)*$', 'g')) || ele == ''))

        if (original_text_block.length < 3) {
            return;
        }

        var i;
        for (i in original_text_block) {
            let data = original_text_block[i];
            data = data.replace(new RegExp('^(<xxpxx>)+', 'g'), '').replace(new RegExp('(<xxpxx>)+$', 'g'), '');
            data = data.replace(new RegExp('-<xxpxx>', 'g'), '');
            original_text_block[i] = data;
        }

        $('#recipe_name').val(original_text_block[0].replace(new RegExp('^(<xxpxx>)+', 'g'), ' '));
        original_text_block.shift();

        var persons = original_text_block[0].match(new RegExp('^([^0-9><]*([0-9]+)[^0-9><]*)$'));
        if (persons) {
            $('#persons').val(persons[2]);
            original_text_block.shift();
        }

        if (original_text_block.length < 2) {
            $('.recipe_editor_form').reset();
            return;
        }
        
        $('#ingredients_list').html('');
        var line;
        for (line of original_text_block[0].split(new RegExp('<xxpxx>', 'g'))) {
            parse_ingredenties(line);
        }
        original_text_block.shift();

        $('#steps_list').html('');
        for (step of original_text_block) {
            step = step.replace(new RegExp('^([^a-zA-ZäöüÄÖÜ]*)([a-zA-ZäöüÄÖÜ])'), '$2');
            step = step.replace(new RegExp('<xxpxx>', 'g'), ' ');
            let node = addStep();
            node.find('textarea').val(step);
        }
    }

    $('#parse_original_text').click(function(e) {
        e.preventDefault();
        parse_original_text();
    });

});

