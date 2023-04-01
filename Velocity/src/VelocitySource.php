<?php

class VelocitySource {

    private $file;
    private $file_exists;

    // Checks if $file is a valid file path and throws an error if not. Otherwise it stores the file path.
    public function __construct($file)
    {
        if(file_exists($file)) {

            $this->file = $file;

        } else {

            throw new Exception("The file \"$file\" does not exist, thus it cannot be a VelocitySource");

        }
    }

    public function getFile() {

        return $this->file;

    }

}

?>