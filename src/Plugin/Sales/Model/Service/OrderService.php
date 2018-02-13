<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Sales\Model\Service;

use Praxigento\Pv\Service\Sale\Order\Delete\Request as ARequest;

/**
 * Remove relations with cancelled sale order with PV module data.
 */
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
            $this->servSaleDelete->exec($req);
        }
        return $result;
    }
}