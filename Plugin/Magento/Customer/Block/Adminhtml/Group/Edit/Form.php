<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Magento\Customer\Block\Adminhtml\Group\Edit;

/**
 * Add more fields to customer group edit form (can see PV flag).
 */
class Form
{
    const ELEM_PRXGT_PV_CAN_SEE = 'prxgt_pv_can_see';
    const PARAM_PRXGT_PV_CAN_SEE = 'pv_can_see';

    /** @var \Praxigento\Pv\Repo\Dao\Customer\Group */
    private $daoPvCustGroup;
    /** @var \Magento\Eav\Model\Entity\Attribute\Source\Boolean */
    private $srcBool;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\Boolean $srcBool,
        \Praxigento\Pv\Repo\Dao\Customer\Group $daoPvCustGroup
    ) {
        $this->srcBool = $srcBool;
        $this->daoPvCustGroup = $daoPvCustGroup;
    }

    public function beforeSetForm(
        \Magento\Customer\Block\Adminhtml\Group\Edit\Form $subject,
        \Magento\Framework\Data\Form $form
    ) {
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');
        if ($fieldset) {
            $fieldset->addField(
                self::ELEM_PRXGT_PV_CAN_SEE,
                'select',
                [
                    'name' => self::PARAM_PRXGT_PV_CAN_SEE,
                    'label' => __('Can see PV'),
                    'title' => __('Can see PV'),
                    'note' => __(
                        'Ability for customers from this group to see products PV in catalog.',
                        \Magento\Customer\Model\GroupManagement::GROUP_CODE_MAX_LENGTH
                    ),
                    'class' => '',
                    'required' => true,
                    'values' => $this->srcBool->toOptionArray(),
                ]
            );
        }
        /* load 'Can See PV' flag and add to the form */
        $fldId = $form->getElement('id');
        if ($fldId) {
            $groupId = $fldId->getValue();
            $entity = $this->daoPvCustGroup->getById($groupId);
            if ($entity) {
                $canSeePv = (bool)$entity->getCanSeePv();
            } else {
                $canSeePv = false;
            }
            $form->addValues([self::ELEM_PRXGT_PV_CAN_SEE => $canSeePv]);
        }
        return [$form];
    }
}