<?php

use Contao\CoreBundle\ContaoCoreBundle;

$GLOBALS['BE_MOD']['merconis']['discountcampaign'] = array(
	'tables' => array('tl_discountcampaign')
);

$GLOBALS['TL_CRON']['minutely'][]  = array('PredcaCron', 'run');

$GLOBALS['FE_MOD']['discountcampaign'] = array
(
	'discountcampaign_list'     => 'ModuleDiscountCampaignList',
);