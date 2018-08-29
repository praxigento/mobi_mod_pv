<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Praxigento\Pv\Config as Cfg;

/**
 * Process previously saved CSV items and compose report for UI.
 */
class Preview
    extends \Praxigento\Core\App\Action\Back\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_TRANSFERS_UPLOAD;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_TRANSFERS_UPLOAD;
        $breadcrumbLabel = 'PV Transfers Preview';
        $breadcrumbTitle = 'PV Transfers Preview';
        $pageTitle = 'PV Transfers Preview';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }

}