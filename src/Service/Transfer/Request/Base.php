<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Request;

class Base extends \Praxigento\Core\Service\Base\Request
{
    /**
     * Force transfer if any or all validation conditions are violated.
     */
    const COND_FORCE_ALL = 'condition_force_all';
    /**
     * Force transfer if credit and debit accounts belong to the customers not from the same country.
     */
    const COND_FORCE_COUNTRY = 'condition_force_country';
    /**
     * Force transfer if credit account belongs to the customers not from the downline of the debit account customer.
     */
    const COND_FORCE_DOWNLINE = 'condition_force_downline';
    const DATE_APPLIED = 'date_applied';
    /** Note to add to operation */
    const NOTE_OPERATION = 'note_operation';
    /** Note to add to transaction  */
    const NOTE_TRANSACTION = 'note_transaction';
    /**
     * PV transfer value.
     */
    const VALUE = 'value';

    /**
     * @return bool
     */
    public function getConditionForceAll()
    {
        $result = parent::get(self::COND_FORCE_ALL);
        return $result;
    }

    /**
     * @return bool
     */
    public function getConditionForceCountry()
    {
        $result = parent::get(self::COND_FORCE_COUNTRY);
        return $result;
    }

    /**
     * @return bool
     */
    public function getConditionForceDownline()
    {
        $result = parent::get(self::COND_FORCE_DOWNLINE);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateApplied()
    {
        $result = parent::get(self::DATE_APPLIED);
        return $result;
    }

    /**
     * @return string
     */
    public function getNoteOperation()
    {
        $result = parent::get(self::NOTE_OPERATION);
        return $result;
    }

    /**
     * @return string
     */
    public function getNoteTransaction()
    {
        $result = parent::get(self::NOTE_TRANSACTION);
        return $result;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        $result = parent::get(self::VALUE);
        return $result;
    }

    /**
     * @param bool $data
     */
    public function setConditionForceAll($data)
    {
        parent::set(self::COND_FORCE_ALL, $data);
    }

    /**
     * @param bool $data
     */
    public function setConditionForceCountry($data)
    {
        parent::set(self::COND_FORCE_COUNTRY, $data);
    }

    /**
     * @param bool $data
     */
    public function setConditionForceDownline($data)
    {
        parent::set(self::COND_FORCE_DOWNLINE, $data);
    }

    /**
     * @param string $data
     */
    public function setDateApplied($data)
    {
        parent::set(self::DATE_APPLIED, $data);
    }

    /**
     * @param string $data
     */
    public function setNoteOperation($data)
    {
        parent::set(self::NOTE_OPERATION, $data);
    }

    /**
     * @param string $data
     */
    public function setNoteTransaction($data)
    {
        parent::set(self::NOTE_TRANSACTION, $data);
    }

    /**
     * @param float $data
     */
    public function setValue($data)
    {
        parent::set(self::VALUE, $data);
    }
}