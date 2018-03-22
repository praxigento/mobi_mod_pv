<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Repo\Data\Customer\Group as Group;
use Praxigento\Pv\Repo\Data\Product as Product;
use Praxigento\Pv\Repo\Data\Quote as Quote;
use Praxigento\Pv\Repo\Data\Quote\Item as QuoteItem;
use Praxigento\Pv\Repo\Data\Sale as Sale;
use Praxigento\Pv\Repo\Data\Sale\Item as SaleItem;
use Praxigento\Pv\Repo\Data\Stock\Item as StockItem;

class InstallSchema
    extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Customer / Group */
        $demEntity = $demPackage->get('package/Customer/entity/Group');
        $this->toolDem->createEntity(Group::ENTITY_NAME, $demEntity);

        /* Stock Item */
        $demEntity = $demPackage->get('package/Stock/entity/StockItem');
        $this->toolDem->createEntity(StockItem::ENTITY_NAME, $demEntity);

        /* Product */
        $demEntity = $demPackage->get('entity/Product');
        $this->toolDem->createEntity(Product::ENTITY_NAME, $demEntity);

        /* Sale */
        $demEntity = $demPackage->get('entity/SaleOrder');
        $this->toolDem->createEntity(Sale::ENTITY_NAME, $demEntity);

        /* Sale Item */
        $demEntity = $demPackage->get('package/SaleOrder/entity/OrderItem');
        $this->toolDem->createEntity(SaleItem::ENTITY_NAME, $demEntity);

        /* Quote */
        $demEntity = $demPackage->get('entity/SaleQuote');
        $this->toolDem->createEntity(Quote::ENTITY_NAME, $demEntity);

        /* Quote Item */
        $demEntity = $demPackage->get('package/SaleQuote/entity/QuoteItem');
        $this->toolDem->createEntity(QuoteItem::ENTITY_NAME, $demEntity);

    }

}