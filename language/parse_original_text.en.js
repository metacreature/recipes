/*
 File: parse_original_text.de.js
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 published under MIT License
*/

var ingredients_parser_unit_translator = {
    'lb': /^(lb(.)?|pound)$/i,
    'oz': /^(oz|ounce)$/i,
    'kg': /^(Kg|Kilo|Kilogram|Kilograms)$/i,
    'dec': /^(dec|Decagram|Decagram)$/i,
    'g': /^(g|Gram)$/i,
    'l': /^(L|Liter|litre|litres)$/i,
    'cl': /^(cl|Centiliter|Centilitre|Centilitres)$/i,
    'ml': /^(ml|Milliliter|Millilitre|Millilitres)$/i,
    'tbsp.': /^(tablespoon|tbsp|tbs|tbl|tb|tbsp\.|tbs\.|tbl\.|tb\.)$/i,
    'tsp.': /^(teaspoon|tsp|ts|tsb|tsp\.|ts\.|tsb\.)$/i,
    'pcs': /^(piece|pcs|pcs\.)$/i,
    'pinch': /^(pinch)$/i,
    'bunch': /^(bunch)$/i,
    'stem': /^(stem)$/i,
    'twig': /^(twig)$/i,
    'leaf': /^(leaf)$/i,
    'roll': /^(roll)$/i,
    'pkg': /^(package|pk|pkg|pk\.|pkg\.)$/i,
    'tube': /^(tube)$/i,
    'cup': /^(c|cup)$/i,
    'bottle': /^(bottle|btl|btl\.)$/i,
};


var ingredients_parser_or = /^(or|altern[a-z\.]*)[ \t]/i;