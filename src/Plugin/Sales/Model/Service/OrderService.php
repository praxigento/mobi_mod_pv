<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Sales\Model\Service;

use Praxigento\Pv\Service\Sale\Order\Delete\Request as ARequest;
use Praxigento\Pv\Service\Sale\Order\Delete\Response as AResponse;

class OrderService
{
    /** @var \Praxigento\Pv\Service\Sale\Order\Delete */
    private $servSaleDelete;

    public function __construct(
        \Praxigento\Pv\Service\Sale\Order\Delete $servSaleDelete
    ) {
        $this->servSaleDelete = $servSaleDelete;
    }


    /**
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Closure $proceed
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function aroundCancel(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Closure $proceed,
        $id
    ) {
        $result = $proceed($id);
        if ($result === true) {
            $req = new ARequest();
            $req->setSaleId($id);
            /** @var AResponse $resp */
            $resp = $this->servSaleDelete->exec($req);
        }
        return $result;
    }
}