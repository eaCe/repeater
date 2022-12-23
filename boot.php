<?php
if (rex::isBackend() && ('index.php?page=content/edit' == rex_url::currentBackendPage() && rex::getUser())) {
    rex_view::addJSFile($this->getAssetsUrl('js/alpinejs.min.js'), [rex_view::JS_DEFERED => true]);
    rex_view::addJSFile($this->getAssetsUrl('js/script.min.js'));
    rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));
}
