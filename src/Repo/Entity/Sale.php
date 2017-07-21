<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Pv\Data\Entity\Sale as Entity;

class Sale extends BaseEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param \Praxigento\Pv\Data\Entity\Sale|array $data
     * @return \Praxigento\Pv\Data\Entity\Sale
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * Generic method to get data from repository.
     *
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param null $columns
     * @param null $group
     * @param null $having
     * @return \Praxigento\Pv\Data\Entity\Sale[] Found data or empty array if no data found.
     */
    public function get(
        $where = null,
        $order = null,
        $limit = null,
        $offset = null,
        $columns = null,
        $group = null,
        $having = null
    )
    {
        $result = parent::get($where, $order, $limit, $offset, $columns, $group, $having);
        return $result;
    }

    /**
     * Get the data instance by ID.
     *
     * @param int $id
     * @return \Praxigento\Pv\Data\Entity\Sale|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

}