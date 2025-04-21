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
You can setup this homepage easily for different languages. Running it multilingual doesn't make sense, coz the recipes aren't multilingual. To make it easier to add a recipe, there is a parser to fill the form from a continuous text.

Published under [MIT License](https://choosealicense.com/licenses/mit/)

**Copyright:**

Copyright (c) 2023 Clemens K. (https://github.com/metacreature)

**If you like it, I would be happy if you  [donate on checkya](https://checkya.com/1hhp2cpit9eha/payme)**<br /><br />

## Installation
First of all you need a domain or subdomain, even on localhost (google for a howto), coz the website won't work in a subdirectory.<br />
And you will need a MySQL-Database with utf8_general_ci - charset. It should work with MariaDB and Postgres too, but it is not tested.<br /><br />
After you have downloaded or cloned the repo you have to open `_inc/settings.inc.php` and change the settings for your needs.
Don't forget to change "SECURE_SALT" (for the passwords) and "HIDDEN_IMAGEFOLDER_SECURE" (for the images). HIDDEN_IMAGEFOLDER_SECURE should only contain lowercase alphanumeric characters, coz it is used as a suffix for a folder-name.<br />
Coz there is no admin-interface you have to temporary set SETTINGS_ALLOW_REGISTER to 'true' to add a new user.<br /><br />
Run `_lib/setup.sql` in eg. phpMyAdmin to create all tables

### German or English:
For german or english you have to set SETTINGS_DEFAULT_LANG to 'de' or 'en'. After that you have to execute either `_lib/setup.de.sql` or `_lib/setup.en.sql`. There are some inserts for category, tags and units you may want to fit for your needs. <br /><br />
If you want to change the inserts for unit you have to change the 'ingredients_parser_unit_translator' in `language/parse_original_text.de.js` or `language/parse_original_text.en.js`. If a regex in this list matches it will be replaced by the the keys. The keys of course have to match with the default-units in the database.

### Other languages:
**First of all, it would be great to share your work with me.**<br /><br />
You have to copy following files and replace the language-code with yours:
- language/en.lang.php
- language/parse_original_text.en.js
- emails/password.request.email.en.html
- _lib/setup.en.sql

`language/{your language-code}.lang.php` and `emails/password.request.email.{your language-code}.html` are obvious.<br />
The default-units in `_lib/setup.{your language-code}.sql` are tricky. The default-units have to match the 'ingredients_parser_unit_translator' list in `language/parse_original_text.{your language-code}.js`. If a regex in this list matches it will be replaced by the the keys. You have to change the regexes to every possible variant of a unit. For eg. kilogram, there are 'kg', 'kilo' and 'kilogram' as a variant. Its recommended to use case-insensitve regexes<br /><br />
In `language/parse_original_text.{your language-code}.js` you will have to change the regex for detecting an alternativ ingredient (variable 'ingredients_parser_or') with all its variants.


## Usage

## Preview
![list](docs/list.jpg)
![slideshow](docs/slideshow.jpg)
![editor](docs/editor.jpg)
