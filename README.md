renderMessage
=============

A plugin for other plugin, allowing to render a public page or send a warning to public user.

## Usage

This plugin offer 2 functions for other plugin, after activation : render and flashMessage.
Plugin are tested on LimeSurvey 3.8, and must work partially on version up to 3.0.

**This plugin is not compatible with LimeSurvey 2.73 and lesser version.**

### render
- string $message : message send to twig file
- string|null $layout to be used (must be in a views directory, final name start by layout_ (added here)). By default to global
- string|null $content to be used (layout dependent : in /subviews/content/ for layout 'global', default to included view content.twig) 
- array $aData to be merged from default data

To show any content to a public user with default layout and quit after.
````
    $renderMessage = new \renderMessage\messageHelper();
    $renderMessage->render($content);
````

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

To show one or flash message to a public user.
````
    \renderMessage\messageHelper::addFlashMessage($message,$type);
````
_This function use javascript solution_

### Adding or replace template twig file.

This plugin add a new event : `getPluginTwigPath`, where you can add or replace twig files from template.

Simple example to add a directory :
````
    $viewPath = dirname(__FILE__)."/views";
    $this->getEvent()->append('add', array($viewPath));
````

This plugin already use this event to add and use `subviews/content/alert.twig`, `subviews/content/content.twig`, `subviews/messages/flash_messages.twig` and `subviews/messages/flash_message.twig`.

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
- Copyright © 2017 Denis Chenu <http://sondages.pro>

Distributed under [AFFERO GNU GENERAL PUBLIC LICENSE Version 3](http://www.gnu.org/licenses/agpl.txt) licence.
If you need a more permissive Licence [contact](http://extensions.sondages.pro/about/contact.html).
