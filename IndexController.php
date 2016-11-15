<?php
/**
 * IndexController.php
 *
 * @author  Thai Nguyen
 * @author	Daniel Sturm
 * @build	2016-11-15
 */

include_once 'TranslationGenerator.php';

if (isset($_GET['path'])) {
    $generator = new TranslationGenerator($_GET['path']);
    echo $generator->getTranslationHTMLcode();
}

/* end of file IndexController.php */
