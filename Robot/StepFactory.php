<?php
namespace Robot;

use Robot\Debug;

class StepFactory
{
    public static function initialize( $step )
    {
        Debug::log( sprintf( '%s: %s', __METHOD__, "Etapa: {$step}" ) );
        $stepClass = "\\Robot\\Step\\{$step}";
        $stepInstance = new $stepClass;
        if (false === ($stepInstance instanceof StepInterface)) {
            throw new \Exception( "Etapa '{$step}' não reconhecida. Verifique se a classe implementa a interface StepInterface." );
        }
        return $stepInstance;
    }
}
