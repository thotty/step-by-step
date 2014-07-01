<?php
namespace Robot;

class Debug
{
    private static $_canDebug = false;

    /**
     * @param boolean $bool
     * @return void
     */
    public static function initializeDebugNoise( $bool=null )
    {
        self::$_canDebug = $bool;
    }

    /**
     * @return void
     */
    public static function log( $debug=PHP_EOL, $label='DEBUG' )
    {

        $log = '';
        if (PHP_EOL === $debug) {
            $log = $debug;
        } else {
            if (in_array( $label, array( 'DEBUG','INFO','WARN','LOG','ERROR' ) ) && is_string( $debug )) {
                $log .= sprintf('%s [%5s] %s%s',date('d/m/Y h:i:s'),$label,$debug,PHP_EOL);
            } else {
                $log .= sprintf('%s [%s]%s',date('d/m/Y h:i:s'),$label,PHP_EOL);
                $log .= var_export($debug, true);
                $log .= PHP_EOL;
            }
            if (self::$_canDebug === true) {
                echo $log;
            }
        }
        error_log($log, 3, self::_getLogFilename());
    }

    private static function _getLogFilename()
    {
        $filename = $_SERVER['PWD'] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'Robot-Debug_' . date('Ymd') . '.log';
        return $filename;
    }
}

