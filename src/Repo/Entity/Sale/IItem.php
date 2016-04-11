<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Sale;


interface IItem extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Referenced entity to address attributes.
     *
     * @return \Praxigento\Pv\Data\Entity\Sale\Item
     */
    public function getRef();
}