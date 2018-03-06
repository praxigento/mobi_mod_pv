<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Framework\Webapi;

class ServiceOutputProcessor
{
    const JSON_CAN_SEE_PV = 'praxigentoCustomerCanSeePv';
    const JSON_ITEM_CAN_SEE_PV = 'prxgt_pv_item_can_see';
    const JSON_ITEM_PV_TOTAL = 'prxgt_pv_item_total';
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
     * Add PV to cart/quote according to REST API requests/response structure.
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
            ($serviceClassName == \Magento\Checkout\Api\ShippingInformationManagementInterface::class) &&
            ($serviceMethodName == 'saveAddressInformation')
        ) {
            $result = $this->onCheckoutStepShippingSave($result);
        } elseif (
            ($serviceClassName == \Magento\Checkout\Api\TotalsInformationManagementInterface::class) &&
            ($serviceMethodName == 'calculate')
        ) {
            $result = $this->onCheckoutCartSummary($result);
        } elseif (
            ($serviceClassName == \Magento\Checkout\Api\PaymentInformationManagementInterface::class) &&
            ($serviceMethodName == 'getPaymentInformation')
        ) {
            $result = $this->onCheckoutStepBilling($result);
        }
        return $result;
    }

    /**
     * Add PV to totals for "/checkout/cart" summary block (on the right side).
     *   - on shipping address changes;
     *
     * @param $data
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function onCheckoutCartSummary($data)
    {
        $restIn = $this->hlpReg->getRestInputParams();
        $cartId = $restIn[0];
        $canSeePv = $this->hlpCfgProvider->getCanSeePv(null, $cartId);
        /* add 'Can See PV' flag to the whole structure */
        $data[self::JSON_CAN_SEE_PV] = $canSeePv;
        if ($canSeePv) {
            /* add PV totals to cart */
            $segments = $data['total_segments'];
            $segments = $this->hlpCfgProvider->addPvToTotalSegments($segments, $cartId);
            $data['total_segments'] = $segments;
            /* add PV totals to items */
            $items = $data['items'];
            $updated = [];
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $total = $this->hlpCfgProvider->getCartItemPv($itemId);
                $item[self::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
                $item[self::JSON_ITEM_PV_TOTAL] = $total;
                $updated[] = $item;
            }
            $data['items'] = $updated;
        }
        return $data;
    }

    /**
     * Add PV to totals for "/checkout/cart" summary block (on the right side).
     *
     * @param $data
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function onCheckoutStepBilling($data)
    {
        $restIn = $this->hlpReg->getRestInputParams();
        $cartId = $restIn[0];
        $canSeePv = $this->hlpCfgProvider->getCanSeePv(null, $cartId);
        /* add 'Can See PV' flag to the whole structure */
        $data[self::JSON_CAN_SEE_PV] = $canSeePv;
        if ($canSeePv) {
            /* add PV totals to cart */
            $segments = $data['totals']['total_segments'];
            $segments = $this->hlpCfgProvider->addPvToTotalSegments($segments, $cartId);
            $data['totals']['total_segments'] = $segments;
            /* add PV totals to items */
            $items = $data['totals']['items'];
            $updated = [];
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $total = $this->hlpCfgProvider->getCartItemPv($itemId);
                $item[self::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
                $item[self::JSON_ITEM_PV_TOTAL] = $total;
                $updated[] = $item;
            }
            $data['totals']['items'] = $updated;
        }
        return $data;
    }

    private function onCheckoutStepShippingSave($data)
    {
        $restIn = $this->hlpReg->getRestInputParams();
        $cartId = $restIn[0];
        $canSeePv = $this->hlpCfgProvider->getCanSeePv(null, $cartId);
        /* add 'Can See PV' flag to the whole structure */
        $data[self::JSON_CAN_SEE_PV] = $canSeePv;
        if ($canSeePv) {
            /* add PV totals to cart */
            $segments = $data['totals']['total_segments'];
            $segments = $this->hlpCfgProvider->addPvToTotalSegments($segments, $cartId);
            $data['totals']['total_segments'] = $segments;
            /* add PV totals to items */
            $items = $data['totals']['items'];
            $updated = [];
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $total = $this->hlpCfgProvider->getCartItemPv($itemId);
                $item[self::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
                $item[self::JSON_ITEM_PV_TOTAL] = $total;
                $updated[] = $item;
            }
            $data['totals']['items'] = $updated;
        }
        return $data;
    }
}