<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Sale\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric;
use Praxigento\Pv\Data\Entity\Sale\Item as Entity;
use Praxigento\Pv\Repo\Entity\Sale\IItem as IEntityRepo;

class Item extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, new Entity());
    }

}