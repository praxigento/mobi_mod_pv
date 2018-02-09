<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Customer\Block\Adminhtml\Group\Edit;

/**
 * Add more fields to customer group edit form (can see PV flag).
 */
class Form
{
    /** @var \Magento\Eav\Model\Entity\Attribute\Source\Boolean */
    private $srcBool;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\Boolean $srcBool
    ) {
        $this->srcBool = $srcBool;
    }

    public function beforeSetForm(
        \Magento\Customer\Block\Adminhtml\Group\Edit\Form $subject,
        \Magento\Framework\Data\Form $form
    ) {
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            'prxgt_pv_can_see',
            'select',
            [
                'name' => 'can_see_pv',
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
        return [$form];
    }
}