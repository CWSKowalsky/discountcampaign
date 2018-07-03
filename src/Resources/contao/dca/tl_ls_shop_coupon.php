<?php
    $GLOBALS['TL_DCA']['tl_ls_shop_coupon']['palettes']['default'] = str_replace
    (
        '{title_legend},title;{status_legend},published;{generalSettings_legend},productCode,couponCode,couponValueType,couponValue,description,minimumOrderValue,allowedForGroups,start,stop;{numAvailable_legend},limitNumAvailable',
        '{title_legend},title;{status_legend},published;{generalSettings_legend},productCode,couponCode,couponValueType,couponValue,description,minimumOrderValue,allowedForGroups,products,start,stop;{numAvailable_legend},limitNumAvailable',
        $GLOBALS['TL_DCA']['tl_ls_shop_coupon']['palettes']['default']
    );

    $GLOBALS['TL_DCA']['tl_ls_shop_coupon']['fields']['products'] = array
    (
        'label' => &$GLOBALS['TL_LANG']['tl_ls_shop_coupon']['products'],
        'exclude' => true,
        'inputType' => 'ls_shop_productSelectionWizard',
        'eval' => array(
            'tl_class' => 'clr'
        ),
        'sql' => "blob NULL"
    );

?>