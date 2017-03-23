<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Response;

/**
 * @method bool getIsInvalidCountries() - country should be the same for both customers
 * @method setIsInvalidCountries(bool $data)
 * @method bool getIsInvalidDownline() - receiver is not in the downline of sender
 * @method setIsInvalidDownline(bool $data)
 */
class BetweenCustomers extends Base
{

}