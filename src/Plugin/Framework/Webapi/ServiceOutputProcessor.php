<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Framework\Webapi;

class ServiceOutputProcessor
{
    const JSON_CAN_SEE_PV = 'praxigentoCustomerCanSeePv';
    const JSON_ITEM_CAN_SEE_PV = 'prxgt_pv_can_see';
    const JSON_ITEM_PV_TOTAL = 'prxgt_pv_total';
    const JSON_TOTAL_SEG_DISCOUNT = 'prxgt_pv_cart_discount'; // flag bound to the item (TODO: use JSON_CAN_SEE_PV on the front)
    const JSON_TOTAL_SEG_GRAND = 'prxgt_pv_cart_grand';
    const JSON_TOTAL_SEG_SUBTOTAL = 'prxgt_pv_cart_subtotal';

    /** @var \Praxigento\Pv\Helper\ConfigProvider */
    private $hlpCfgProvider;
    /** @var \Praxigento\Core\Api\Helper\Registry */
    private $hlpReg;

    public function __construct(
        \Praxigento\Core\Api\Helper\Registry $hlpReg,
        \Praxigento\Pv\Helper\ConfigProvider $hlpCfgProvider
    ) {
        $this->hlpReg = $hlpReg;
        $this->hlpCfgProvider = $hlpCfgProvider;
    }

    /**
     * Add PV to cart/quote on REST API requests from front.
     *
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $subject
     * @param \Closure $proceed
     * @param $data
     * @param $serviceClassName
     * @param $serviceMethodName
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundProcess(
        \Magento\Framework\Webapi\ServiceOutputProcessor $subject,
        \Closure $proceed,
        $data,
        $serviceClassName,
        $serviceMethodName
    ) {
        $result = $proceed($data, $serviceClassName, $serviceMethodName);
        if (
            (($serviceClassName == \Magento\Checkout\Api\ShippingInformationManagementInterface::class) &&
                ($serviceMethodName == 'saveAddressInformation')) ||
            (($serviceClassName == \Magento\Checkout\Api\TotalsInformationManagementInterface::class) &&
                ($serviceMethodName == 'calculate'))
        ) {
            $result = $this->hlpCfgProvider->addPvData($result);
        } elseif (
            ($serviceClassName == \Magento\Checkout\Api\PaymentInformationManagementInterface::class) &&
            ($serviceMethodName == 'getPaymentInformation')
        ) {
            $result = $this->onCheckoutStepBilling($result);
        }
        return $result;
    }

    private function onCheckoutStepBilling($data)
    {
        $restIn = $this->hlpReg->getRestInputParams();
        $cartId = $restIn[0];
        $canSeePv = $this->hlpCfgProvider->getCanSeePv(null, $cartId);
        $data[self::JSON_CAN_SEE_PV] = $canSeePv;
        return $data;
    }
}