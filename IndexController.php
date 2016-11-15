<?php
/**
 * IndexController.php
 *
 * @author  Thai Nguyen
 * @author	Daniel Sturm
 * @build	2016-11-15
 */
require 'vendor/autoload.php';

include_once 'TranslationGenerator.php';

$data = ${'_' . $_SERVER['REQUEST_METHOD']};
if (isset($data['path'])) {
    $generator = new TranslationGenerator($data['path']);
    if (isset($data['lang'])) {
        $generator->translationLanguage($data['lang']);
    }
    echo $generator->getTranslationHTMLcode();
}

/* end of file IndexController.php */
