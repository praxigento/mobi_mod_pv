<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity;

interface ISale
    extends \Praxigento\Core\Repo\IEntity
{
    /**
     * @param \Praxigento\Pv\Data\Entity\Sale|array $data
     * @return \Praxigento\Pv\Data\Entity\Sale
     */
    public function create($data);

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
    );

    /**
     * Get the data instance by ID.
     *
     * @param int $id
     * @return \Praxigento\Pv\Data\Entity\Sale|bool Found instance data or 'false'
     */
    public function getById($id);

}