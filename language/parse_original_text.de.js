/*
 File: parse_original_text.de.js
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
    'Packung': /^(Packung)$/i,
    'Prise': /^(Prise)$/i,
    'Tube': /^(Tube)$/i,
    'Portion': /^(Portion)$/i,
    'Becher': /^(Becher)$/i,
    'Flasche': /^(Flasche)$/i,
    'Tasse': /^(Tasse)$/i,
};


var ingredients_parser_or = /^(od\.|oder|alter[a-z\.]*)[ \t]/i;