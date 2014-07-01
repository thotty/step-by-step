<?php
namespace Robot;

/**
 * Interface das etapas do robo
 * 
 */
interface StepInterface
{
    /**
     * Executa o procedimento da etapa
     * 
     * @return void
     */
    public function exec();
}
