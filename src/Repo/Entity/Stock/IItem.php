<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Stock;


interface IItem extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Referenced entity to address attributes.
     *
     * @return \Praxigento\Pv\Data\Entity\Stock\Item
     */
    public function getRef();
}