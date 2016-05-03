<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Request;

class Base extends \Praxigento\Core\Service\Base\Request {
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
    /**
     * PV transfer value.
     */
    const VALUE = 'value';
}