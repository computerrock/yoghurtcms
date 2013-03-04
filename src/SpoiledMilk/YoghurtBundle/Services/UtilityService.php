<?php

namespace SpoiledMilk\YoghurtBundle\Services;

class UtilityService {

    public static function mergeArrays($arr1, $arr2) {
        foreach ($arr2 as $key => $val) {
            if (array_key_exists($key, $arr1) && is_array($val))
                $arr1[$key] = UtilityService::mergeArrays($arr1[$key], $arr2[$key]);
            else
                $arr1[$key] = $val;
        }

        return $arr1;
    }

    /**
     * Parses a JSON object and returns an associative array of it's properties
     * 
     * @param string $jsonString
     * @return array 
     */
    public static function jsonToArray($jsonString) {
        $ret = array();

        try {
            @$ret = get_object_vars(json_decode($jsonString));
        } catch (Exception $e) {
            $ret = array();
        }

        return $ret;
    }

    /**
     * Finds files by pattern, in the stated directory. If recursive is set to
     * true, all the children directories will be searched
     * 
     * @param string $pattern
     * @param string $dir
     * @param boolean $recursive 
     * 
     * @see http://www.redips.net/php/find-files-with-php/
     */
    public static function findFiles($pattern, $dir, $recursive = false) {
        // Escape any character in a string that might be used to trick
        // a shell command into executing arbitrary commands
        $dir = escapeshellcmd($dir);

        // get a list of all matching files in the current directory
        $files = glob("$dir/$pattern");

        if ($recursive) {
            // Find a list of all directories in the current directory
            // Directories beginning with a dot are also included
            foreach (glob("$dir/{.[^.]*,*}", GLOB_BRACE | GLOB_ONLYDIR) as $sub_dir) {
                $arr = UtilityService::findFiles($pattern, $sub_dir, $recursive);  // resursive call
                $files = array_merge($files, $arr); // merge array with files from subdirectory
            }
        }

        return $files;
    }

    /**
     * Recursivly removes a directory and it's content
     * @link http://www.php.net/manual/en/function.rmdir.php#107233
     * @param string $dir 
     */
    public static function recurseRemoveDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        UtilityService::recurseRemoveDir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Makes the dir tree
     * @param string $dir 
     */
    public static function mkDirTree($dir) {
        $ret = true;
        $dirs = explode('/', $dir);

        for ($i = 0; $i < sizeof($dirs); $i++) {
            $newDir = '/';

            for ($j = 0; $j <= $i; $j++) {
                if ($newDir != '/')
                    $newDir .= '/';
                $newDir .= $dirs[$j];
            }

            if (!file_exists($newDir))
                $ret &= mkdir($newDir);
        }

        return $ret;
    }

    public static function writeFile($file, $content) {
        $fh = fopen($file, 'w');
        fwrite($fh, $content);
        fclose($fh);
    }

    /**
     * Modifies a string to remove all non ASCII characters and spaces.
     */
    static public function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}