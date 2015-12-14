<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

class InstallSchema extends \Praxigento\Core\Setup\Data\Base {

    public function __construct() {
        parent::__construct('Praxigento\Pv\Lib\Setup\Data');
    }

}