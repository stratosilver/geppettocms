# Geppetto CMS (Alpha)

Geppetto CMS make any static website editable. It enables online edition of defined parts of all static web pages of a website.

# Required

PHP

# Usage

Upload `geppetto.php` and  `/geppetto` directory in the root directory of your static website.

go to `https://www.yourwebsite.com/geppetto.php`

## Create an account

The first time it is used it asks you to create an account, fill in the login and password fields and click 'create'
The login page will show.

## Edit website

After the login, it will search for the default page (index.html, index.html, or index.php) it will open the page in edition mode 

If you have a specific default page indicate it as a parameter of the url `https://www.yourwebsite.com/geppetto.php?page=pagename.html`

In the edition mode and any HTML element with the attribute __editable="true"__ will become editable. To change the text simply click on it and edit it 
When you click on the save button which is floating on the bottom right of the page, the page will be updated with the change you made.
