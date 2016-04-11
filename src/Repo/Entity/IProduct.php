<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity;


interface IProduct extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Referenced entity to address attributes.
     *
     * @return \Praxigento\Pv\Data\Entity\Product
     */
    public function getRef();
}