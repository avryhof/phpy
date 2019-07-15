<?php

class python_path {
    function exists($path) {
        return is_dir($path) || file_exists($path);
    }
    function join($parts) {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }
    function split($path) {
        $path = str_replace('\\', '/', $path);

        return explode('/', $path);
    }
    function translate($path, $path_type=false) {
        $split_path =  $this->split($path);

        if (strtolower($path_type) == "windows") {
            $retn = implode("\\", $split_path);
        } elseif (substr_count(strtolower($path_type), 'nix')) {
            $retn = implode('/', $split_path);
        } else {
            $retn = $this->join($split_path);
        }

        return $retn;
    }
}
class os {
    var $path;
    function __construct() {
        $this->path = new python_path();
    }
    function environ($var_name) {

        return getenv($var_name);
    }
}

$os = new os();