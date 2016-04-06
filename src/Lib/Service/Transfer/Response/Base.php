<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Transfer\Response;

abstract class Base extends \Praxigento\Core\Service\Base\Response {
    const ERR_IS_NOT_DOWNLINE = 'receiver is not in the downline of sender';
}