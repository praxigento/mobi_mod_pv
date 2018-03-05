<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\Model;

/**
 * Add PV related data to cart/quote (totals & items PV) in checkout configuration (JSON, controllers).
 */
class CompositeConfigProvider
{
    /** @var \Praxigento\Pv\Helper\ConfigProvider */
    private $hlpCfgProvider;

    public function __construct(
        \Praxigento\Pv\Helper\ConfigProvider $hlpCfgProvider
    ) {
        $this->hlpCfgProvider = $hlpCfgProvider;
    }

    /**
     * Add PV data to cart/quote JSON used in checkout configuration on front:
     * {code:js}
     *      window.checkoutConfig = {};
     * {code}
     *
     * @param \Magento\Checkout\Model\CompositeConfigProvider $subject
     * @param array $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\CompositeConfigProvider $subject,
        $result
    ) {
        $result = $this->hlpCfgProvider->addPvData($result);
        return $result;
    }
}