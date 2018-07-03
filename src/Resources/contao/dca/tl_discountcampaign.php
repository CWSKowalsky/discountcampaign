<?php

$GLOBALS['TL_DCA']['tl_discountcampaign'] = array
(

	'config' => array
	(
		'dataContainer' => 'Table',
		'enableVersioning' => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
		'onsubmit_callback' => array ( 
		   array('Predca', 'applySettings')
		),
		'ondelete_callback' => array (
			array('Predca', 'redoSettings')
		)
	),

	'list' => array
	(
		'sorting' => array
		(
			'mode' => 2,
			'fields' => array('title'),
			'flag' => 1,
			'panelLayout' => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields' => array('title'),
			'format' => '%s',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href' => 'act=select',
				'class' => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['edit'],
				'href' => 'act=edit',
				'icon' => 'edit.gif'
			),
			'delete' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['delete'],
				'href' => 'act=delete',
				'icon' => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'

			),
			'show' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['show'],
				'href' => 'act=show',
				'icon' => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),
		)
    ),
    
	'palettes' => array
	(
		'default' => '{title_legend},title,products;{discountcampaign_legend},lsShopProductIsOnSale,lsShopProductSaving,lsShopProductSavingType, start, stop'
	),

	'fields' => array
	(
		'id' => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'old_data' => array
		(
			'sql'                     => "blob NULL"
		),
		'title' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['title'],
			'inputType' => 'text',
			'exclude' => true,
			'sorting' => true,
			'flag' => 1,
			'search' => true,
			'eval' => array('mandatory'=>true, 'unique'=>true, 'maxlength'=>255),
			'sql' => "varchar(255) NOT NULL default ''"
		),
		'products' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['products'],
			'exclude' => true,
			'inputType' => 'ls_shop_productSelectionWizard',
			'sql'	=> "blob NULL"
		),
		'lsShopProductIsOnSale' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['lsShopProductIsOnSale'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'doNotCopy' => true
            ),
			'filter' => true,
			'sql'	=> "int(1) NOT NULL default '0'"
        ),
		'lsShopProductSaving' => array
		(
            'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['lsShopProductSaving'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('rgxp' => 'numberWithDecimals', 'tl_class'=>'w50'),
			'sql'	=> "varchar(10) NOT NULL default '0'"
		),
		'lsShopProductSavingType' => array
		(
            'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['lsShopProductSavingType'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => array('savingTypePercentaged','savingTypeAbsolute'),
			'reference' => $GLOBALS['TL_LANG']['tl_discountcampaign']['options']['savingType'],
			'eval' => array('tl_class'=>'w50', 'mandatory'=>true),
			'sql'	=> "varchar(30) NULL"
		),
		'start' => array
		(
            'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['start'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array(
                'rgxp' => 'datim',
				'datepicker' => true,
				'tl_class'=>'w50',
                'mandatory' => true
			),
			'sql'	=> "varchar(20) NULL"
        ),
		'stop' => array
		(
            'label' => &$GLOBALS['TL_LANG']['tl_discountcampaign']['stop'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array(
                'rgxp' => 'datim',
				'datepicker' => true,
				'tl_class'=>'w50',
                'mandatory' => true
			),
			'sql'	=> "varchar(20) NULL"
        )
	)
);

?>