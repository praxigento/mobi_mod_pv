<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Framework\Webapi;

use Praxigento\Pv\Plugin\Checkout\Model\CompositeConfigProvider as ACompConfProv;

class ServiceOutputProcessor
{
    const JSON_ITEM_CAN_SEE_PV = ACompConfProv::JSON_ITEM_CAN_SEE_PV;
    const JSON_ITEM_PV_TOTAL = ACompConfProv::JSON_ITEM_PV_TOTAL;

    /** @var \Praxigento\Pv\Helper\ConfigProvider */
    private $hlpCfgProvider;
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoQuoteItem;

    public function __construct(
        \Praxigento\Pv\Helper\ConfigProvider $hlpCfgProvider,
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->hlpCfgProvider = $hlpCfgProvider;
        $this->repoQuoteItem = $repoQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    /**
     * Add PV to quote items on checkout 2nd step.
     *
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $subject
     * @param \Closure $proceed
     * @param $data
     * @param $serviceClassName
     * @param $serviceMethodName
     * @return mixed
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
//            $canSeePv = $this->hlpPvCust->canSeePv();
//            $items = $result['totals']['items'];
//            foreach ($items as $key => $item) {
//                $itemId = $item['item_id'];
//                $pvData = $this->repoQuoteItem->getById($itemId);
//                $total = $pvData->getTotal();
//                $total = number_format($total, 2, '.', '');
//                $item[self::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
//                $item[self::JSON_ITEM_PV_TOTAL] = $total;
//                $items[$key] = $item;
//            }
//            $result['totals']['items'] = $items;
        }
        return $result;
    }
}