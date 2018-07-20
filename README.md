renderMessage
=============

A plugin for other plugin, allowing to render a public page or send a warning to public user.

## Usage

This plugin offer 2 functions for other plugin, after activation : render and flashMessage.
Plugin are tested on LimeSurvey 3.8, and must work partially on version up to 3.0.

This plugin is compatible with LimeSurvey 2.73 version, but some function need update.

### render
- string $message : message send to twig file
- string|null $layout to be used (must be in a views directory, final name start by layout_ (added here)). By default to global
- string|null $content to be used (layout dependent : in /subviews/content/ for layout 'global', default to included view content.twig) 
- array $aData to be merged from default data

To show any content to a public user with default layout of current survey and defaut title (survey name or sitename).
````
    \renderMessage\messageHelper::renderContent($content);
````

To show any content to a public user with layout, title and data.
````
    $renderMessage = new \renderMessage\messageHelper();
    $renderMessage->render($content,$layout,$content,$aData);
````
**Warning** : there are difference in render function with LimeSurvey 2.XX version.

### renderAlert
- string $message : the html message t be shown to user, language and template are automatically set by actual situation.
- string $type : message type, using alert class of BootStrap then `success`,`info`,`warning`,`danger`. Default is `info`.

To show an alert message to a public user and quit after.
````
    \renderMessage\messageHelper::renderAlert($message,$type);
````

### flashMessage
- string $message : message to be shown to user like a flash
- string $type : message type, using alert class of BootStrap then `success`,`info`,`warning`,`danger`. Default is `info`.

To add one flash message to a public user.
````
    \renderMessage\flashMessageHelper::getInstance()->addFlashMessage($message,$type);
````
_Not working for 2.6lts and lesser version__
_Not working for 3.0 and upper version__

### Replace twig file in your survey template.

This plugin add and use `subviews/content/alert.twig`, `subviews/content/content.twig`, `subviews/messages/flash_messages.twig` and `subviews/messages/flash_message.twig`.

## Installation

See [Install and activate a plugin for LimeSurvey](http://extensions.sondages.pro/install-and-activate-a-plugin-for-limesurvey.html) .

After update of this plugin, best is to reset assets globally. You can do it via _Global settings_ with _Clear assets cache_ button.

### Via GIT
- Go to your LimeSurvey Directory
- Clone in plugins/renderMessage directory

### Via ZIP dowload
- Download <http://extensions.sondages.pro/IMG/auto/renderMessage.zip>
- Extract : `unzip renderMessage.zip`
- Move the directory to plugins/ directory inside LimeSUrvey

## Home page & Copyright
- HomePage <http://extension.sondages.pro/>
- Copyright © 2017-2018 Denis Chenu <http://sondages.pro>

Distributed under [AFFERO GNU GENERAL PUBLIC LICENSE Version 3](http://www.gnu.org/licenses/agpl.txt) licence.
If you need a more permissive Licence [contact](http://extensions.sondages.pro/about/contact.html).
