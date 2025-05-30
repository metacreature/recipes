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