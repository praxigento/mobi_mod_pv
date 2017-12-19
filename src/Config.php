<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv;

use Praxigento\Downline\Config as DownlineCfg;

class Config extends \Praxigento\Core\Config
{
    const CODE_TOTAL_DISCOUNT = 'prxgt_pv_discount';
    const CODE_TOTAL_GRAND = 'prxgt_pv_grand';
    const CODE_TOTAL_SUBTOTAL = 'prxgt_pv_subtotal';
    const CODE_TYPE_ASSET_PV = 'PV';
    const CODE_TYPE_OPER_PV_SALE_PAID = 'PV_SALE_PAID';
    const CODE_TYPE_OPER_PV_TRANSFER = 'PV_TRANSFER';
    const DTPS = DownlineCfg::DTPS;
    const INIT_DEPTH = DownlineCfg::INIT_DEPTH;
    const MODULE = 'Praxigento_Pv';
}