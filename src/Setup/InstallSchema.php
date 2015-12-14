<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base {

    /**
     * InstallSchema constructor.
     */
    public function __construct() {
        parent::__construct('Praxigento\Pv\Lib\Setup\Schema');
    }

}