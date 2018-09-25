<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Magento\Customer\Api;

use Praxigento\Pv\Plugin\Magento\Customer\Block\Adminhtml\Group\Edit\Form as AForm;
use Praxigento\Pv\Repo\Data\Customer\Group as EPvCustGroup;

/**
 * Save 'Can See PV' flag on group saving in adminhtml.
 */
class GroupRepositoryInterface
{
    /** @var \Magento\Framework\App\Action\Context */
    private $context;
    /** @var \Praxigento\Pv\Repo\Dao\Customer\Group */
    private $daoPvCustGroup;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Praxigento\Pv\Repo\Dao\Customer\Group $daoPvCustGroup
    ) {
        $this->context = $context;
        $this->daoPvCustGroup = $daoPvCustGroup;
    }

    public function afterSave(
        \Magento\Customer\Api\GroupRepositoryInterface $subject,
        \Magento\Customer\Api\Data\GroupInterface $result
    ) {
        if ($result) {
            /* this is HTTP request to save customer group from adminhtml */
            $request = $this->context->getRequest();
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            if (
                ($controller == 'group') &&
                ($action == 'save')
            ) {
                $id = $result->getId();
                $canSeePv = (bool)$request->getParam(AForm::PARAM_PRXGT_PV_CAN_SEE);
                $entity = new EPvCustGroup();
                $entity->setGroupRef($id);
                $entity->setCanSeePv($canSeePv);
                $this->daoPvCustGroup->replace($entity);
            }
        }
        return $result;
    }
}