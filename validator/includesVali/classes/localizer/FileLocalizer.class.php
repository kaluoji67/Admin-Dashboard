<?php

/**
 * A concrete implementation of the Localizer being
 * able to read resources from simple properties-files (one line for
 * each entry, line scheme is 'key=value'.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class FileLocalizer extends Localizer {
    private $language;
    private $strings = array();

    public function getSupportedLanguages(){
        return array(
            "en",
            "de"
        );
    }

    /**
     * @return array
     */
    public function getStrings()
    {
        return $this->strings;
    }

    /** @inheritdoc */
    public function setLanguage($lang) {
        //Check whether given language is supported. Fall back to english if not.
        if (!in_array($lang,$this->getSupportedLanguages())){
            $lang = "en";
        }
        $this->language = $lang;
        $content = file_get_contents(__DIR__ . "/lang_{$lang}.properties");
        $lines = preg_split("/\r?\n/", $content);
        // $this->strings = array();
        foreach($lines as $line) {
            if(strpos($line, '=')) {
                list($key, $res) = explode("=", $line, 2);
                $this->strings[trim($key)] = $res;
            }
        }

    }

    /** @inheritdoc */
    public function getLanguage() {
        return $this->language;
    }

    /** @inheritdoc */
	/**Extra parameters 
	 * add the new language specific words to the file in case the key does not already exists
	 * adds it in both files, even if it already exist in one*/
    public function getString($key, $params = null) {
        if(array_key_exists(trim($key), $this->strings)) {
            $res = $this->strings[trim($key)];
            if(is_array($params)) {
                for($i = 0; $i < count($params); $i++) {
                    $res = str_replace('{' . $i . '}', $params[$i], $res);
                }
            }
            return $res;
        }
		
        if(is_string($params)) {
            $s = explode("|", $params);//Split params by separator |
            for($i = 0; $i < 2; $i++) {
                $str = $s[$i];
                $lang = $i == 0 ? "en" : "de";

                $file = fopen(__DIR__ . "/lang_{$lang}.properties", "a");
                fwrite($file, "\n" . $key . "=" . $str);
                fclose($file);
            }
            $this->setLanguage($this->language);
            return $this->getString($key);
        }
    }
}