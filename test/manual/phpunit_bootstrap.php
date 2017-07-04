<?php
/**
 * Start M2 app if BP is not defined (see './app/autoload.php' in Magento 2 root folder
 */
if (!defined('BP')) {
    include_once(__DIR__ . '/../../../../../app/bootstrap.php');
    /**
     * Create test application that loads configuration and  initializes DB connection
     * then ends w/o exiting ($response->terminateOnSend = false).
     */
    $params = $_SERVER;
    /** @var  $bootstrap \Magento\Framework\App\Bootstrap */
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
    /** @var  $app \Praxigento\Core\Test\App */
    $app = $bootstrap->createApplication(\Praxigento\Core\Test\App::class);
    $bootstrap->run($app);
}