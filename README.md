# Alien CMS (Proof of concept)

Alien CMS is a PHP file that enable online edition of defined parts of all static web pages of a website.

# Required

PHP

# Usage

Upload alien.php in the root directory of your static website.

go to https://www.exemple.com/alien.php?page=pagename.html.

It will open thepage pagename.html and any section of the HTML page between <!--EDITABLE--> and <!--END EDITABLE--> become a small form with a textarea and a save button
When you edit the text and click on save, the page will be updated whit the change you made.
