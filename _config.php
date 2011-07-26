<?php

define('MOD_FCF_PATH',rtrim(dirname(__FILE__), DIRECTORY_SEPARATOR));
$folders = explode(DIRECTORY_SEPARATOR,MOD_FCF_PATH);
define('MOD_FCF_DIR',rtrim(array_pop($folders),DIRECTORY_SEPARATOR));
unset($folders);
