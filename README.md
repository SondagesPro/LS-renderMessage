renderMessage
=============

A plugin for other plugin, allowing to render a public page or send a warning to public user.

## Usage

This plugin offer 2 functions for other plugin, after activation : render and flashMessage.
Plugin are tested on LimeSurvey 2.62.0, and must work partially on lesser version.

### render
- string $message : the html message t be shown to user, language and template are automatically set by actual situation.

To show a message to a public user and quit after.
````
    $renderMessage = new \renderMessage\messageHelper();
    $renderMessage->render($message);
````

### flashMessage
- string $message : message to be shown to user like a flash
- string $type : message type, using alert class of BootStrap then `success`,`info`,`warning`,`danger`. Default is `info`.

To show one or flash message to a public user.
````
    $renderFlashMessage = \renderMessage\flashMessageHelper::getInstance();
    $renderFlashMessage->addFlashMessage($message);
````
_This function are not available for 2.61 and lesser version_

## Installation

See [Install and activate a plugin for LimeSurvey](http://extensions.sondages.pro/install-and-activate-a-plugin-for-limesurvey.html)

### Via GIT
- Go to your LimeSurvey Directory (version up to 2.06 only)
- Clone in plugins/findUserAgentInfo directory

### Via ZIP dowload
- Download <http://extensions.sondages.pro/IMG/auto/findUserAgentInfo.zip>
- Extract : `unzip findUserAgentInfo.zip`
- Move the directory to plugins/ directory inside LimeSUrvey

## Home page & Copyright
- HomePage <http://extension.sondages.pro/>
- Copyright © 2017 Denis Chenu <http://sondages.pro>

Distributed under [AFFERO GNU GENERAL PUBLIC LICENSE Version 3](http://www.gnu.org/licenses/agpl.txt) licence.
If you need a more permissive Licence [contact](http://extensions.sondages.pro/about/contact.html).
