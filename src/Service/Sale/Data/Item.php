<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Service\Sale\Data;

use Flancer32\Lib\Data as DataObject;

/**
 * Items to transfer data to registry PV on invoice payment.
 *
 * @method int getItemId()
 * @method void setItemId(int $data)
 * @method int getProductId()
 * @method void setProductId(int $data)
 * @method double getQuantity()
 * @method void setQuantity(double $data)
 * @method int getStockId()
 * @method void setStockId(int $data)
 */
class Item extends DataObject
{

}