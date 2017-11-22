<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Sale\Order;

/**
 * @deprecated TODO: use it or remove it.
 */
interface GetInterface
{
    /**
     * @param \Praxigento\Pv\Api\Sale\Order\Get\Request $data
     * @return \Praxigento\Pv\Api\Sale\Order\Get\Response
     */
    public function execute(\Praxigento\Pv\Api\Sale\Order\Get\Request $data);
}