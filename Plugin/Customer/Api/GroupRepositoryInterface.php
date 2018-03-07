<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Customer\Api;

use Praxigento\Pv\Plugin\Customer\Block\Adminhtml\Group\Edit\Form as AForm;
use Praxigento\Pv\Repo\Entity\Data\Customer\Group as EPvCustGroup;

/**
 * Save 'Can See PV' flag on group saving in adminhtml.
 */
class GroupRepositoryInterface
{
    /** @var \Magento\Framework\App\Action\Context */
    private $context;
    /** @var \Praxigento\Pv\Repo\Entity\Customer\Group */
    private $repoPvCustGroup;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Praxigento\Pv\Repo\Entity\Customer\Group $repoPvCustGroup
    ) {
        $this->context = $context;
        $this->repoPvCustGroup = $repoPvCustGroup;
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
                $this->repoPvCustGroup->replace($entity);
            }
        }
        return $result;
    }
}