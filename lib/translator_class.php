<?php
/*
    The translation files created by this class are in .json format
    The array structure: string (md5), translation (translated string)
     
    Default language is English. 

*/

class translator {
    public $gui_lang; // Two-letter language code for the translation of GUI strings.
    private $install_dir;
    private $translation_file_path;
    
    public function __construct($lang) {
        $this->install_dir = rtrim(realpath(dirname(__FILE__) . '/../'), "/") . '/';
        $this->gui_lang = $lang;
        $this->translation_file_path = $this->install_dir.'translations/'.$this->gui_lang.'.json';
    }
    
    public function string($string) {
            if (!file_exists($this->translation_file_path)) {$translation_file=false;} else {$translation_file=true;}
            if ($translation_file==false) {
                // If translation file does not exist, attempt to create it with update_translations()
                $this->update_translations(); // Add string to translation file
            } else {
                $loaded_translation_arr = $this->load_translation();
                $md5string = md5($string); // Is this really needed? AFAIK keys can contain special characters, too!
                if (isset($loaded_translation_arr["$md5string"])) {
                    return $loaded_translation_arr["$md5string"];
                } else {
                  $new_string_arr = array();
                  $new_string_arr[] = array('string' => md5($string), 'translation' => $string);
                  $this->update_translations($string);
                }
            }
        // If no GUI language is selected or available, show original string.
        return $string;
    }
    public function update_translations($add_string=false) {
        $updated_lang_arr = array();
        // Updates the Json translation files
        $loaded_translation_arr = $this->load_translation();
        if ($loaded_translation_arr!==false) {
          foreach ($loaded_translation_arr as $key => $val) {
            $updated_lang_arr[] = array('string' => $key, 'translation' => $val);
          }

        }
        if ($add_string !== false) {
          $updated_lang_arr[] = array('string' => md5($add_string), 'translation' => $add_string);
        }
            // Since errors may occur while handling files, ideally the file_handler_class is to be used,
            // but since the file_handler also depends on translations (to handle errors)
            // I just hard-code some english errors.. Maybe I should try to fix this dependency conflict later??
            if($fp = fopen($this->translation_file_path, 'w')) {
                if(!fwrite($fp, json_encode($updated_lang_arr))) {
                  return false;
                }
                unset($updated_lang_arr);
                fclose($fp);
            } else {
                return false;
            }
            return true;
    }
    public function load_translation() {
        if (!file_exists($this->translation_file_path)) {
          $translation_data = false;
        } else {
          $translation_data = file_get_contents($this->translation_file_path);
        }
        if ($translation_data!==false) {
            $reformed_translation_arr = array();
            // Returns the entire translation from its source
            $loaded_translation_arr = json_decode($translation_data, true);
          foreach ($loaded_translation_arr as $key => $val) {
            $reformed_translation_arr["{$val["string"]}"] = $val["translation"];
          }
          unset($loaded_translation_arr);
          return $reformed_translation_arr;
        } else {return false;}
    }
}