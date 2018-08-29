<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Repo\Dao\Trans\Batch;

use Praxigento\Pv\Repo\Data\Trans\Batch\Item as Entity;

class Item
    extends \Praxigento\Core\App\Repo\Dao
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

    /**
     * @param Entity|array $data
     * @return Entity
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $batchId
     * @return \Praxigento\Pv\Repo\Data\Trans\Batch\Item[]
     */
    public function getByBatchId($batchId)
    {
        $where = Entity::A_BATCH_REF . '=' . (int)$batchId;
        $result = $this->get($where);
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
     * @return Entity[] Found data or empty array if no data found.
     */
    public function get(
        $where = null,
        $order = null,
        $limit = null,
        $offset = null,
        $columns = null,
        $group = null,
        $having = null
    ) {
        $result = parent::get($where, $order, $limit, $offset, $columns, $group, $having);
        return $result;
    }

    /**
     * Get the data instance by ID.
     *
     * @param int $id
     * @return Entity|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

}