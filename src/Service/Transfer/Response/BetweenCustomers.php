<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Response;

class BetweenCustomers extends Base
{
    const CODE_IS_NOT_DOWNLINE = 'receiver is not in the downline of sender';
    const CODE_NOT_THE_SAME_COUNTRIES = 'country should be the same for both cusotmers';
}