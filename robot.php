<?php
ignore_user_abort( false );

require_once 'Zend/Loader/Autoloader.php';
$arrRegisterNamespace = array();
$arrRegisterNamespace['application_robot']   = 'Robot';
$loader = Zend_Loader_Autoloader::getInstance();
foreach ($arrRegisterNamespace as $namespace) {
    $loader->registerNamespace( $namespace );
}

$main = \Robot\Main::getInstance();
try {

    register_shutdown_function( array($main, 'shutdown') );

    $main->argsManipulator()
         ->bootstrap()
         ->run();

} catch (Exception $exception) {

    if ($main instanceof Robot\Main) {
        $main->setExpection( $exception );
        exit(1);
    }

    if ($exception instanceof Zend_Console_Getopt_Exception) {
        exit( $exception->getMessage() . PHP_EOL . $exception->getUsageMessage() );
    }
    exit( $exception->getMessage() );
}

exit(0);
