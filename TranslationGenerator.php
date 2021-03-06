<?php
/**
 * TranslationGenerator.php
 *
 * @author  Thai Nguyen
 * @author	Daniel Sturm
 * @build	2016-11-15
 */

use Stichoza\GoogleTranslate\TranslateClient;

ini_set('display_errors', 1);
!defined('LB') && define('LB', chr(10));

/**
 * class TranslationGenerator
 */
class TranslationGenerator
{
    /**
     * [$extension_path description]
     * @var [type]
     */
    protected $_extension_path;

    /**
     * [$need_to_translate description]
     * @var [type]
     */
    protected $need_to_translate = [];

    /**
     * [$_language description]
     * @var [type]
     */
    protected $_language;

    /**
     * [__construct description]
     * @method __construct
     * @param  [type] $extension_path [description]
     */
    public function __construct($extension_path)
    {
        $this->extensionPath($extension_path);

        $this->_getFromPHPfiles();
        $this->_getFromPHTMLfiles();
        $this->_getFromXMLfiles();

        $this->_validate();
    }

    /**
     * [extensionPath description]
     * @method extensionPath
     * @param  [type] $extension_path [description]
     * @return [type] [description]
     */
    public function extensionPath($extension_path = null)
    {
        if (!is_null($extension_path)) {
            $this->_extension_path = $extension_path;
        }

        return $this->_extension_path;
    }

    /**
     * [translationLanguage description]
     * @method translationLanguage
     * @param  [type] $language [description]
     * @return [type] [description]
     */
    public function translationLanguage($language = null)
    {
        if (!is_null($language)) {
            $this->_language = strtolower($language);
        }

        if (!is_null($this->_language) &&
            !isset($this->__tr)) {
            $this->__tr = new TranslateClient();
            $this->__tr->setSource('en');
        }

        if (!is_null($language)) {
            $this->__tr->setTarget($this->_language);
        }

        return $this->_language;
    }

    /**
     * [__translate description]
     * @method __translate
     * @param  [type] $string [description]
     * @param  [type] $language [description]
     * @return [type] [description]
     */
    private function __translate($string, $language = null)
    {
        if (is_null($language)) {
            $language = $this->translationLanguage();
        }

        if (!isset($this->__tr)) {
            return $string;
        }

        if (!is_null($language)) {
            $this->__tr->setTarget($language);
        }

        return $this->__tr->translate($string);
    }

    /**
     * [getTranslationString description]
     * @method getTranslationString
     * @return [type] [description]
     */
    public function getTranslationString()
    {
        $string = '';
        foreach ($this->need_to_translate as $item) {
            $string .= $item . ',' . $this->__translate($item) . LB;
        }
        return $string;
    }

    /**
     * [getTranslationHTMLcode description]
     * @method getTranslationHTMLcode
     * @return [type] [description]
     */
    public function getTranslationHTMLcode()
    {
        $string = '<p>';
        foreach ($this->need_to_translate as $item) {
            $string .= '"' . $item . '","' . $this->__translate($item) . '"' . '</br>';
        }
        $string .= '</p>';
        return $string;
    }

    /**
     * [getTranslationCsv description]
     * @method getTranslationCsv
     * @return [type] [description]
     */
    public function getTranslationCsv()
    {
    }

    /**
     * [__getIterator description]
     * @method __getIterator
     * @return [type] [description]
     */
    private function __getIterator()
    {
        $directory = new RecursiveDirectoryIterator($this->_extension_path);
        return new RecursiveIteratorIterator($directory);
    }

    /**
     * [__getFilesByExtension description]
     * @method __getFilesByExtension
     * @param  [type] $ext [description]
     * @return [type] [description]
     */
    private function __getFilesByExtension($ext)
    {
        $regex = new RegexIterator($this->__getIterator(), '/^.+\.' . $ext . '$/i', RecursiveRegexIterator::GET_MATCH);
        return array_keys(iterator_to_array($regex));
    }

    /**
     * [__getFileTokens description]
     * @method __getFileTokens
     * @param  [type] $uri [description]
     * @return [type] [description]
     */
    private function __getFileTokens($uri)
    {
        return token_get_all(file_get_contents($uri));
    }

    /**
     * [__addFromFile description]
     * @method __addFromFile
     * @param  [type] $file [description]
     */
    private function __addFromFile($file)
    {
        $tokens = $this->__getFileTokens($file);
        foreach ($tokens as $key => $token) {
            if (!in_array($token[0], [310], true) ||
                $token[1] !== '__') {
                continue;
            }
            $_stringToken = $tokens[$key + 2];
            if (!is_array($_stringToken)) {
                continue;
            }
            $this->need_to_translate[] = ($_stringToken[1]);
        }
    }

    /**
     * [__addFromFiles description]
     * @method __addFromFiles
     * @param  [type] $files [description]
     */
    private function __addFromFiles($files)
    {
        if (is_string($files)) {
            $files = $this->__getFilesByExtension($files);
        }
        for ($i = 0, $length = count($files); $i < $length; ++$i) {
            $this->__addFromFile($files[$i]);
        }
    }

    /**
     * [_getFromPHPfiles description]
     * @method _getFromPHPfiles
     * @return [type] [description]
     */
    protected function _getFromPHPfiles()
    {
        $this->__addFromFiles('php');
    }

    /**
     * [_getFromPHTMLfiles description]
     * @method _getFromPHTMLfiles
     * @return [type] [description]
     */
    protected function _getFromPHTMLfiles()
    {
        $this->__addFromFiles('phtml');
    }

    /**
     * [_getFromXMLfiles description]
     * @method _getFromXMLfiles
     * @return [type] [description]
     */
    protected function _getFromXMLfiles()
    {
        $files = $this->__getFilesByExtension('xml');

        for ($i = 0, $length = count($files); $i < $length; ++$i) {
            $file_path = $files[$i];
            $file_content = file_get_contents($file_path);
            $xml = simplexml_load_string($file_content);
            $list = $xml->xpath('//*[@translate]');

            foreach ($list as $key => $item) {
                $translate_element = (string)$item->attributes()->translate;
                $this->need_to_translate[] = (string)$item->{$translate_element};
            }
        }
    }

    /**
     * [_validate description]
     * @method _validate
     * @return [type] [description]
     */
    protected function _validate()
    {
        $this->need_to_translate = array_unique($this->need_to_translate);
        asort($this->need_to_translate);

        /* remove quote and double quote */
        foreach ($this->need_to_translate as $key => $item) {
            if ($item[0] === '"' && $item[strlen($item) -1] === '"') {
                $this->need_to_translate[$key] = trim($item, '"');
            }
            if ($item[0] === "'" && $item[strlen($item) -1] === "'") {
                $this->need_to_translate[$key] = trim($item, "'");
            }
        }
    }
}

/* end of file TranslationGenerator.php */
