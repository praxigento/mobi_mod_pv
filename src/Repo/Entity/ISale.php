<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity;

use Praxigento\Pv\Data\Entity\Sale as Entity;

interface ISale extends \Praxigento\Core\Repo\IEntity
{
    /**
     * @param int $id
     * @return Entity
     */
    public function getById($id);

}