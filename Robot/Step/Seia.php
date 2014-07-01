<?php
namespace Robot\Step;

use \Robot\Debug;

class Seia implements \Robot\StepInterface
{

    /**
     * @see \Robot\StepInterface
     * @return void
     */
    public function exec()
    {
      Debug::log( sprintf( '%s: %s', __METHOD__,
                            'Rotina da etapa, blablabla...' ), 'INFO' );

      Debug::log( APPLICATION_ENV, 'env?' );
    }
}
