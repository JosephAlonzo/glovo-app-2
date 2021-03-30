<?php

namespace Vexsolutions\Utils\Logger\Facades;

use Illuminate\Support\Facades\Facade;

class BufferLog  extends Facade {

    protected static function getFacadeAccessor() { return 'BufferLogger'; }
}
