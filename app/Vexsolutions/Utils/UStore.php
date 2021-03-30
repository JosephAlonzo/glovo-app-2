<?php

namespace Utils;
use App\Models\EMVexlanguages;

class UStore{





    public static function DefLang($shopifyshop) {

        $shopifyshop->primary_locale = substr($shopifyshop->primary_locale,0,2);

        $default_lang   = EMVexlanguages::where('LANG_CODE', $shopifyshop->primary_locale)->count() == 0  ? 'en' : $shopifyshop->primary_locale;

        return $default_lang;


    }


}
