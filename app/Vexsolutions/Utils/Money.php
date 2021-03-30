<?php

namespace Utils;

class Money
{





    public static function Format ($amount, $format) {


        $format = str_replace("{{amount_with_comma_separator}}", $amount, $format );
        $text = str_replace("{{amount}}", $amount, $format );

        return $text;

    }


}
