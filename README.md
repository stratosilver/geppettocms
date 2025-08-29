# Geppetto CMS (Proof of concept)

Geppetto CMS is a PHP file that enable online edition of defined parts of all static web pages of a website.
(I changed the name, Alien CMS to Geppetto CMS, it's more accurate regarding of the functionalities)

# Required

PHP

# Usage

Upload alien.php in the root directory of your static website.

go to https://www.exemple.com/geppetto.php?page=pagename.html.

It will open the page pagename.html and any section of the HTML page between `<!--EDITABLE-->` and `<!--END EDITABLE-->` become a small form with a textarea and a save button
When you edit the text and click on save, the page will be updated whit the change you made.
