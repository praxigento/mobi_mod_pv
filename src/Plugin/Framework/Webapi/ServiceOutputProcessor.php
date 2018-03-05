<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Framework\Webapi;

class ServiceOutputProcessor
{
    /** @var \Praxigento\Pv\Helper\ConfigProvider */
    private $hlpCfgProvider;

    public function __construct(
        \Praxigento\Pv\Helper\ConfigProvider $hlpCfgProvider
    ) {
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
        }
        return $result;
    }
}