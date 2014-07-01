<?php
namespace Robot;

class Main
{

    /**
     * @var \Robot\ArgumentsManipulator
     */
    private $_args = null;

    /**
     * @var \Exception
     */
    private $_expection = null;

    /**
     * @var \Robot\Main
     */
    private static $_instance = null;

    /**
     * @access public
     * @return \Robot\Main
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Main();
        }

        return self::$_instance;
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function setExpection( \Exception $exception )
    {
        $this->_expection = $exception;
    }

    /**
     * @return \Robot\Main Provides the fluent interface
     */
    public function argsManipulator()
    {
        Debug::log( __METHOD__ );

        //argumentos passados por linha de comando...
        $this->_args = new ArgumentsManipulator();

        return $this;
    }

    /**
     * @return \Robot\Main Provides the fluent interface
     */
    public function bootstrap()
    {
        Debug::log();
        Debug::log( 'Startup...', 'LOG' );

         $this->_initLock();

        return $this;
    }

    /**
     * @access public
     * @return void
     */
    public function run()
    {
        Debug::log( __METHOD__ );
        foreach ($this->_args->getSteps() as $step) {
            $this->_writeLock( $this->_lockInformation( 'RUNNING_' . strtoupper( $step ) ) );
            $stepInstance = StepFactory::initialize($step);
            $stepInstance->exec();
        }
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function shutdown()
    {
        Debug::log( __METHOD__ );

        $exception = $this->_expection;
        $error     = error_get_last();

        if ($exception instanceof \Exception) {
            $this->_dirtyExit( $exception );
            exit( PHP_EOL .
                    $exception->getMessage() .
                    PHP_EOL .
                    print_r( $exception->getTraceAsString(), true ) .
                    PHP_EOL );
        } else {
            if ( (is_null( $error )) &&
                    (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)) ) {
                $message = "Msg: {$error['message']}" . PHP_EOL . "File: {$error['file']}:{$error['line']}";
                $this->_dirtyExit( $message );
                exit( PHP_EOL . $message . PHP_EOL );
            }
        }

        $this->_cleanExit();
        exit( 0 );
    }

    /**
     * @return void
     */
    public function __clone()
    {
        trigger_error( __CLASS__ . ': Não pode clonar essa classe!', E_USER_ERROR );
    }

    /**
     * @access private
     */
    private function __construct()
    {}

    /**
     * @param string|int $info Rotulo da informação ou valor
     * @return string|int
     * @throws \Core\Exception
     */
    private function _lockInformation( $info, $data = '' )
    {
        $locks = array(
            'STARTUP',
            'RECOVERED',
            'ERROR',
            'EXCEPTION'
        );
        if ($this->_args instanceof ArgumentsManipulator) {
            foreach($this->_args->getSteps() as $step) {
                array_push($locks,'RUNNING_'. strtoupper($step));
            }
        }

        $data = trim( $data );
        if (in_array( $info, $locks )) {
            if (empty( $data )) {
                return $info;
            }
            return $info . PHP_EOL . $data;
        }

        trigger_error( "Tipo de informação de lock '{$info}' inválida.", E_USER_ERROR );
    }

    /**
     * @return string
     */
    private function _getLockFile()
    {
        return  $_SERVER['PWD'] . DIRECTORY_SEPARATOR . '.~robot.lock';//@todo fixar pasta
    }

    /**
     * @return void
     * @todo ver rotina de reparação...
     */
    private function _repairLock()
    {
        $lockInfo = file_get_contents( $this->_getLockFile() );

        Debug::log( sprintf( "LOCK INFO: %s", $lockInfo ) );

        $this->_writeLock( $this->_lockInformation( 'RECOVERED' ) );
    }

    /**
     * @return void
     */
    private function _writeLock( $data )
    {
        $write = sprintf('%s%sem: %s%s', $data, PHP_EOL, date( 'd/m/Y H:i:s' ), PHP_EOL);
        file_put_contents( $this->_getLockFile(), $write );
    }

    /**
     * @return void
     */
    private function _clearLock()
    {
        if (file_exists($this->_getLockFile())) {
            unlink( $this->_getLockFile() );
        }
    }

    /**
     * Verificar e executar os procedimentos do lock
     *
     * @return void
     */
    private function _initLock()
    {
        Debug::log( __METHOD__ );

        if (file_exists( $this->_getLockFile() )) {
            $this->_repairLock();
        } else {
            $this->_writeLock( $this->_lockInformation( 'STARTUP' ) );
        }
    }

    /**
     * @param \Exception|string $fail
     * @return void
     */
    private function _dirtyExit( $fail )
    {
        Debug::log( 'Ops, um erro ocorreu :-(', 'ERROR' );

        if ($fail instanceof \Exception) {
            $this->_writeLock( $this->_lockInformation( 'EXCEPTION', $fail->getMessage() ) );
        } else {
            $this->_writeLock( $this->_lockInformation( 'ERROR', (string) $fail ) );
        }
    }

    /**
     * @return void
     */
    private function _cleanExit()
    {
        Debug::log( 'Zero Kill' );

        $this->_clearLock();
    }

}
