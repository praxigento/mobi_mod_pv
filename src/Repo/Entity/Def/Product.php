<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IBasic as IRepoBasic;
use Praxigento\Pv\Data\Entity\Product as Entity;
use Praxigento\Pv\Repo\Entity\IProduct as IEntityRepo;

class Product extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(IRepoBasic $repoBasic)
    {
        parent::__construct($repoBasic, Entity::ENTITY_NAME, Entity::ATTR_PROD_REF);
    }

}