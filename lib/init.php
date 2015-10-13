<?php
define('MAINDIR',dirname(__DIR__));
define('LIBDIR',MAINDIR . '/lib');
define('PARTY',MAINDIR . '/3rdparty');
require MAINDIR.'/settings.php';
require LIBDIR.'/functions.php';
require PARTY.'/autoload.php';

$getkeys = array(
        'address' => FILTER_SANITIZE_ENCODED,
        'country' => FILTER_SANITIZE_ENCODED,
        'radius'  => FILTER_VALIDATE_INT,
        'distros' => FILTER_SANITIZE_ENCODED,
        'desktops' => FILTER_SANITIZE_ENCODED,
        'actions' => FILTER_SANITIZE_ENCODED,
        'groups' => FILTER_SANITIZE_ENCODED,
        'targets' => FILTER_SANITIZE_ENCODED,
        'rewards' => FILTER_SANITIZE_ENCODED 
);

//process_GET($search);
$search = filter_input_array(INPUT_GET, $getkeys, true);

?>
