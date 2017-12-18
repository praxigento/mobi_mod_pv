<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Repo\Entity\Data\Product as Product;
use Praxigento\Pv\Repo\Entity\Data\Quote as Quote;
use Praxigento\Pv\Repo\Entity\Data\Quote\Item as QuoteItem;
use Praxigento\Pv\Repo\Entity\Data\Sale as Sale;
use Praxigento\Pv\Repo\Entity\Data\Sale\Item as SaleItem;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as StockItem;

class InstallSchema
    extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Stock Item */
        $demEntity = $demPackage->get('package/Stock/entity/StockItem');
        $this->_toolDem->createEntity(StockItem::ENTITY_NAME, $demEntity);

        /* Product */
        $demEntity = $demPackage->get('entity/Product');
        $this->_toolDem->createEntity(Product::ENTITY_NAME, $demEntity);

        /* Sale */
        $demEntity = $demPackage->get('entity/SaleOrder');
        $this->_toolDem->createEntity(Sale::ENTITY_NAME, $demEntity);

        /* Sale Item */
        $demEntity = $demPackage->get('package/SaleOrder/entity/OrderItem');
        $this->_toolDem->createEntity(SaleItem::ENTITY_NAME, $demEntity);

        /* Quote */
        $demEntity = $demPackage->get('entity/SaleQuote');
        $this->_toolDem->createEntity(Quote::ENTITY_NAME, $demEntity);

        /* Quote Item */
        $demEntity = $demPackage->get('package/SaleQuote/entity/QuoteItem');
        $this->_toolDem->createEntity(QuoteItem::ENTITY_NAME, $demEntity);

    }

}