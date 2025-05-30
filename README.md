## Recipes - a lightweight recipe-manager homepage for private use 

### Homepage: [https://github.com/metacreature/recipes](https://github.com/metacreature/recipes)

### Table of contents
- [About this repository](#about-this-repository)
- [Installation](#installation)
  - [German or English](#german-or-english)
  - [Other languages](#other-languages)
- [Usage](#usage)
- [Preview](#preview)


## About this repository
This is a lightweight recipe-manager homepage done in PHP, HTML5, JavaScript and CSS3 for private use.
You can setup this homepage easily for different languages. Running it multilingual doesn't make sense, because the recipes aren't multilingual. To make it easier to add a recipe, there is a parser to fill the form from a continuous text.

Published under [MIT License](https://choosealicense.com/licenses/mit/)

**Copyright:**

Copyright (c) 2023 Clemens K. (https://github.com/metacreature)

**If you like it, I would be happy if you  [donate on checkya](https://checkya.com/1hhp2cpit9eha/payme)**<br /><br />

## Installation
First of all you need a domain or subdomain, even on localhost (google for a howto), because the website won't work in a subdirectory.<br />
And you will need a MySQL-Database with utf8_general_ci - charset. It should work with MariaDB and Postgres too, but it is not tested.<br /><br />
After you have downloaded or cloned the repo you have to open `_inc/settings.inc.php` and change the settings for your needs.
Don't forget to change "SECURE_SALT" (for the passwords) and "HIDDEN_IMAGEFOLDER_SECURE" (for the images). HIDDEN_IMAGEFOLDER_SECURE should only contain lowercase alphanumeric characters, because it is used as a suffix for a folder-name.<br />
Because there is no admin-interface you have to temporary set SETTINGS_ALLOW_REGISTER to 'true' to add a new user.<br /><br />
Run `_lib/setup.sql` in eg. phpMyAdmin to create all tables.<br /><br />

There is a cron-job (`cronjobs/weekly.cron.php`) which runs 'optimize tables', hard deletes recipes set to delete after 14 days and deletes unused images. I would recommend to run it weekly.

### German or English:
For German or English you have to set SETTINGS_DEFAULT_LANG to 'de' or 'en'. After that you have to execute either `_lib/setup.de.sql` or `_lib/setup.en.sql`. There are some inserts for category, tags and units you may want to fit for your needs. <br /><br />
If you want to change the inserts for unit you have to change the 'ingredients_parser_unit_translator' in `language/parse_original_text.de.js` or `language/parse_original_text.en.js`. If a regular expression in this list matches it will be replaced by the keys. The keys of course have to match with the default-units in the database.<br />
I did my very best to find all abbreviation for the units transformed by 'ingredients_parser_unit_translator', but of course, maybe I have missed some. You can add them later

### Other languages:
**First of all, it would be great to share your work with me.**<br /><br />
You have to copy following files and replace the language-code with yours:
- language/en.lang.php
- language/parse_original_text.en.js
- emails/password.request.email.en.html
- _lib/setup.en.sql

`language/{your language-code}.lang.php` and `emails/password.request.email.{your language-code}.html` are obvious.<br />
The default-units in `_lib/setup.{your language-code}.sql` are tricky. The default-units have to match the 'ingredients_parser_unit_translator' list in `language/parse_original_text.{your language-code}.js`. If a regular expression in this list matches it will be replaced by the keys. You have to change the regular expressions to every possible variant of a unit. For eg. kilogram, there are 'kg', 'kilo' and 'kilogram' as a variant. Its c to use case-insensitive regular expressions<br /><br />
In `language/parse_original_text.{your language-code}.js` you will have to change the regular expression for detecting an alternate ingredient (variable 'ingredients_parser_or') with all its variants.


## Usage
Everything is obvious, except the original-text-parser. If you want to add a recipe, you have found in the www, it would be very hard to add it manually - the parser helps. Therefore you simply copy the recipe into the textarea `Original Text` on the right side. After a little editing to match the pattern and press `parse original text`. <br /><br />
Because there is no AI, the magic is done with regular expressions. So it has to follow a specific form, which is best explained with an example:
```
Pancakes

for 12 people

1 1/2 kg flour
3L milk
12 piece eggs
Corn oil
or rapeseed oil

Mix the milk and eggs with a hand mixer and then slowly add the flour
while constantly mixing.
Leave to stand for 1 hour.

Using a soup ladle, portion the batter into a 
pan preheated to level 8 with a little oil 
and spread by swirling the pan. 
Turn when the top is no longer liquid.
```
<br />
First of all the recipe name, followed by an optional information about the number of persons. Then of course the ingredients followed by the instruction-steps.
<b>The blocks have to be separated with at least one empty line.</b><br /><br />
The real magic is done at the ingredients (one per line!). If there is an alternate ingredient it should be in a separate line prefixed with 'or'.<br /><br /> 
The quantity can be a floating-number (eg. '1,5' or '1.5'), or an optional number followed by ¼, ½, ¾ or a fraction (eg '1 ½', '1 1/2', '1/3'). If there is a quantity, a unit is required. Be aware, that '1 fresh bunch of peppermint' will take 'fresh' as the unit and 'bunch of peppermint' as the ingredient.<br /><br />
The unit will be replace to a predefined variant of it, eg. 'kilogram' will be replaced by 'kg'.<br /><br />
Quantity and unit are optional, because in most recipes the amount of eg. salt is not defined.<br /><br />
If the parsing of an ingredients-line fails, a line of empty form-fields will be added, in order to not accidentality miss an ingredient.<br />On top of this, if a unit or ingredient is already in the database it will be colored green, if not orange-red. This will help to find 'fresh' as a unit of '1 fresh bunch of peppermint' and 'Onion' instead of 'onions' easily. Its up to you if you want to use singular or plural, but its recommended not to use both variants for the search.



## Preview

Login, of course with registration, forgotten password and editable user-profile
![login](docs/login.png)<br /><br />

List including search-functionality via ajax
![list](docs/list.png)<br /><br />

View the details in a slideshow
![slideshow](docs/slideshow.png)<br /><br />

The editor including a parser to fill the form from a continuous text and recommendations for units and ingredients if you want to fill it manually
![editor](docs/editor.png)<br /><br />
