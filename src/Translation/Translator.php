<?php
/**
 * Holds Translator.
 *
 * @package WALib
 */
namespace WALib\Translation;

/**
 * A translator which uses a database to search for a translation search.
 *
 * @package WALib
 */
class Translator
{
    /**
     * The language to search for translations in.
     *
     * Should be an Alpha-2 language code.
     *
     * @var string
     */
    protected $_lang = '';

    /**
     * Will hold the translations in an array.
     *
     * @var mixed[]
     */
    protected $_translations = array();

    /**
     * Sets the language for the translations.
     *
     * @param string $lang
     */
    public function __construct($lang = 'de')
    {
        $this->_lang = $lang;
    }

    /**
     * Translates and returns the translated string.
     *
     * Also escapes HTML-entities in the string using lib_htmlspecialchars().
     *
     * @param string $str
     * @param string $lang The Alpha-2 code of the wanted language.
     * @return string The translated text.
     */
    public function translate($str, $lang = '')
    {
        if($lang == '')
        {
            $lang = $this->_lang;
        }

        /*
         * Only fill the translations array if the language is not the standard 'de'
         * and the array is empty for that specific language.
         */
        if(empty($this->_translations[$lang]) && $lang != 'de')
        {
            $this->_fillTranslations($lang);
        }

        if(empty($this->_translations[$lang][$str]))
        {
            return lib_htmlspecialchars($str);
        }
        else
        {
            return lib_htmlspecialchars($this->_translations[$lang][$str]);
        }
    }

    /**
     * Fills the translations array with translations from the database.
     *
     * @param string $lang
     */
    protected function _fillTranslations($lang)
    {
        $db = \WALib\Application\AppConfig::get('db');
        $sql = 'SELECT tot.text AS original_text, t.text AS translated_text
                FROM translation_original_texts as tot
                    INNER JOIN translations AS t ON tot.id = t.original_text_id
                    INNER JOIN translation_languages AS tl ON tl.alpha_2 = "'.mysql_escape_string($lang).'"';
        $translations = $db->queryRef($sql);

        foreach($translations as $originalText => $translatedText)
        {
            $this->_translations[$lang][$originalText] = $translatedText;
        }
    }
}