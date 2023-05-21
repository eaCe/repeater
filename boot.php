<?php

if (rex::isBackend() && ('index.php?page=content/edit' == rex_url::currentBackendPage() && rex::getUser())) {
    rex_view::addJSFile($this->getAssetsUrl('js/script.js'));
    rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));
}
