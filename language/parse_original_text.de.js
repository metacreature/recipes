/*
 File: parse_original_text.de.js
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 published under MIT License
*/

var ingredients_parser_unit_translator = {
    'Kg': /^(Kg|Kilo|Kilogramm)$/i,
    'dag': /^(dag|Deka|Dekagramm)$/i,
    'g': /^(g|Gramm)$/i,
    'L': /^(L|Liter)$/i,
    'cl': /^(cl|Centiliter)$/i,
    'ml': /^(ml|Milliliter)$/i,
    'EL': /^(el|Esslöffel)$/i,
    'TL': /^(tl|Teelöffel)$/i,
    'Stück': /^(Stück|stk|stk\.|st|st\.)$/i,
    'Bund': /^(Bund)$/i,
    'Stängel': /^(Stängel)$/i,
    'Zweig': /^(Zweig)$/i,
    'Blatt': /^(Blatt)$/i,
    'Rolle': /^(Rolle)$/i,
    'Packung': /^(Packung|Pkg|Pckg|Pkg\.|Pckg\.)$/i,
    'Prise': /^(Prise)$/i,
    'Tube': /^(Tube)$/i,
    'Portion': /^(Portion|Port|Port\.)$/i,
    'Becher': /^(Becher)$/i,
    'Flasche': /^(Flasche|Fl)$/i,
    'Tasse': /^(Tasse)$/i,
};


var ingredients_parser_or = /^(od\.|oder|altern[a-z\.]*)[ \t]/i;