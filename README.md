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
For german or english you have to set SETTINGS_DEFAULT_LANG to 'de' or 'en'. After that you have to execute either `_lib/setup.de.sql` or `_lib/setup.en.sql`. There are some inserts for category, tags and units you may want to fit for youer needs. <br /><br />
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

### File-structure:
```
myfile.ecmb
    ˪ ecmb.xml
    ˪ cover.jpeg
    ˪ content/
        ˪ chapter1/
            ˪ page1.jpeg
            ˪ page2.jpeg
```
### Allowed image-types:
jpeg, jpg, png, webp - I would recommend to use webp

### Organize the content
If you want to use chapters (and subchapters) in navigation you have to organize the images in folders, coz a chapter points to a folder. You can add images directly to the root as well for example the introduction, table of contents, spacer-images between the chapters, … 

Chapters with an uneven page-count are supported, also double-pages on an even page and uneven page-count of the book …

### ecmb.xml
All rules and requirements for "ecmb.xml" are defined in the XSD ([located here >](https://github.com/metacreature/ecmb_definition/tree/master/schema)), but unfortunately not everything:

**The navigation-links to the contents are relative!** An example says more than 1000 words ... [go to example >](https://github.com/metacreature/ecmb_definition/blob/master/examples/v1.0/advanced_book/advanced_book.ecmb_unpacked/ecmb.xml)

To clarify that more: a chapter links to a folder, that the reader-app can display which chapter you are currently reading. Of course you want to click on the chapter in the reader-app and therefore you have to provide a link to an image (href) which has to be part of the chapter. To enforce this the link to the image is relative to the chapter's folder. Btw. you can link to the 2nd image (or any you want as long it is part of the chapter's folder), if e.g. the first one is a spacer-image. Subchapters and links are also relative to it is parent.

I know it is a bit complicated, but it is a no-go to mix content with navigation and the programs which should build the eBook would have massive problems, if you want to place links before the content is added.
Unfortunately I couldn't find a possibility to validate that directly with XSD, but of course the validator will check this. If you have an idea, please post it here: [https://stackoverflow.com/questions/77667931/cross-validation-of-contents-in-an-xml-using-xsd](https://stackoverflow.com/questions/77667931/cross-validation-of-contents-in-an-xml-using-xsd)

The **width and height** defined in the root-node **should** be the size of the images. It is not exact, coz when I was building fan-translated Mangas, all images had a different size and aspect-ratio, **but** the aspect-ratio is entirely important for the validator to validate the correct placement of double-page-images `(Formula: id_double = (img_width / img_height) > (book_width / book_height * 1.5))`

Use the **original**-node if the book was (fan-) translated. It is recommended to add the authors to the original book, and leave them empty at main meta-data.

If the book based on e.g. a light-novel and you want to give that credit, you can place the information at the **basedon**-node.

### Navigation
Navigation is optional. If you don’t add it the app will automatically generate a navigation based on the folder-structure.

You can use headlines, links to images and chapters. Chapters point to folders and you have to specify an image to start with. This is useful if the first image of the chapter is e.g. a spacer-image or if there is a prologue and you want to point to the chapter's title-image if the user clicks on the chapter-link.

### Double-page-images
Double-page-image have to be stored in the "dimg"-tag (`<dimg src="doublepage.jpg" />`)  while single-paged images have to be stored in the "img"-tag (`<img src="singlepage.jpg" />`)

The validator will check for incorrectly positioned images.

If you link to a double-page-image in the navigation you have to address the src-attribute, and have to specify either #auto, #left or #right. e.g. `<chapter label="Chapter 1" dir="/content/chap1" href="/doublepage.jpg#left" />`. Using "#auto, #left or #right" is mandatory for double-page-images, while using this for single-page-images this is forbidden. The XSD can't check this, but of course the validator does.

### Finally
Maybe ecmblib's documentation is also helpful for you: [https://comic-manga-ebook.github.io/ecmblib](https://comic-manga-ebook.github.io/ecmblib)<br /><br />

## Validator
The validator of course validates the XML first. After that it manually checks if there are dead links in the navigation (If you have an idea doing this directly in XSD, please post it here: [https://stackoverflow.com/questions/77667931/cross-validation-of-contents-in-an-xml-using-xsd](https://stackoverflow.com/questions/77667931/cross-validation-of-contents-in-an-xml-using-xsd)).
If you validate a *.ecmb (you can validate a single XML-file too) the validator then checks if all images linked in content are available. Finally it will check if all double-page-images are placed correctly.

### For developers
If you are doing your own library to create an *.ecmb in your preferred programming-language it is highly recommended to validate it after the creation. My reader-app will validate it and won't display it if it is invalid!<br /><br />

## Using the Python-Validator
### Installation
- download and install Python3 [https://www.python.org/downloads/](https://www.python.org/downloads/)
- download or clone this repository
- open the console and then
    - go to the validator-folder `cd ecmb_definition/validator/python/`
    - run `pip install -r requirements.txt`
 
### Using it
- open the console and then
    - go to the validator-folder `cd ecmb_definition/validator/python/`
    - run `invoke validate [absolute or relative path to your file]`

Example:
```
$ cd ecmb_definition/validator/python/
$ invoke validate ../../examples/v1.0/advanced_book/advanced_book.ecmb

    File is valid!

$
```


