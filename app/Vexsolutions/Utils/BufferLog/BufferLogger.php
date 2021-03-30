<?php


namespace Vexsolutions\Utils\Logger;



use Carbon\Carbon;
use DateTime;
use Escom\App\DEBUG\EMDebug;
use DateTimeZone;

/**
 * Class documentation
 */
class BufferLogger
{

    /**
     *
     *  Anything options not considered 'core' to the logging library should be
     *  settable view the third parameter in the constructor
     *
     *  Core options include the log file path and the log threshold
     *
     * @var array
     */
    protected $options = array (
        'extension'      => 'txt',
        'dateFormat'     => 'Y-m-d G:i:s.u',
        'filename'       => false,
        'flushFrequency' => false,
        'prefix'         => 'log_',
        'logFormat'      => false,
        'appendContext'  => true,
    );


    /**
     * TimeZone
     * @var resource
     */
    private $tz;
    /**
     * This holds the last line logged to the logger
     *  Used for unit tests
     * @var string
     */
    private $lastLine = '';
    /**
     * Octal notation for default permissions of the log file
     * @var integer
     */
    private $defaultPermissions = 0777;


    /**
     * @var string
     */
    private $buffer="";

    /**
     * Class constructor
     *
     * @param string $logDirectory File path to the logging directory
     * @param string $logLevelThreshold The LogLevel Threshold
     * @param array $options
     *
     * @internal param string $logFilePrefix The prefix for the log file name
     * @internal param string $logFileExt The extension for the log file
     */
    public function __construct($options = array())
    {

        $this->tz   =  new DateTimeZone(config('app.timezone')); //new DateTimeZone();
    }


    /**
     * Escribe en el buffer del log el mensaje
     * @param string $level
     * @param string $message
     * @param array $data
     * @param int $index
     * @return $this
     */
    public function Debug($message='', $index=1, $eol=true){

        $tabs[1] = "  ";
        $tabs[2] = "    ";
        $tabs[3] = "        ";
        $tabs[4] = "            ";
        $tabs[5] = "                ";


        $space = "";
        if(key_exists($index,$tabs)) $space = $tabs[$index];

        //self::$nlog.= date('d-m-Y H:i:s.u') . " - ". $space. $text . "\n";
        $now  = DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''),$this->tz);

        //->setTimeZone(new DateTimeZone(config('app.timezone')));
        //->setTimezone(new \DateTimeZone(date('T')));
        $time = $now->format("Y-m-d H:i:s.u");
        //$time = microtime(true);
        //$time = $this->getTimestamp();

        if( $eol ){
            $message = str_replace(array("\r\n","\n","\t", "\n\t"),array(' ',' ',' ',' '), $message );
        }

        $this->buffer.= $time. $space. $message . "\n";

        //$text       = str_replace(array("\r\n","\n","\t", "\n\t"),array(' ',' ',' ',' '), $message );
        //$serialize  = serialize($data) ;
        //$this->debug.= $time . " - ". $space. $text . ", [".$serialize."]" .   "\n";

        return $this;
    }

    public function getDebug(){
        return $this->buffer;
    }

    /**
     * log - Registra las operaciones que hace
     */
    public function clearDebug(){
        $this->buffer = "";
    }


    /**
     * @param $object
     * @param string $text
     */
    function LogToDataBase($object, $text=""){
        $EMDebug = new EMDebug();
        $EMDebug->store($object, $text);

    }


    /**
     * @param $filename
     * @return bool
     */
    function LogToFile($filename, $create=true){

        $dirname = dirname($filename);
        if ($create)
        {
            //Check if the directory already exists.
            if(!is_dir($dirname)){
                //Directory does not exist, so lets create it.
                @mkdir($dirname, 0777, true);
            }
        }

        $fd = @fopen($filename,'a+');
        if(!$fd){
            return false;
        }
        @fwrite($fd,$this->buffer,strlen($this->buffer));
        @fclose($fd);
    }









    /**
     * Gets the correctly formatted Date/Time for the log entry.
     *
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work correctly, so here it is.
     *
     * @return string
     */
    private function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));
        return $date->format($this->options['dateFormat']);
    }

}
