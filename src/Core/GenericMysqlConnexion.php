<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Exceptions\GenericDatabaseException;
use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use CaptainKant\Generics\Traits\GenericAutowiringServiceTrait;

class GenericMysqlConnexion implements GenericAutowiringServiceInterface
{

    use GenericAutowiringServiceTrait;

    public $host;
    public $user;
    public $password;
    public $port;
    public $database;
    public $socket;

    private $ress;

    /**
     * GenericMysqlConnexion constructor.
     * @param $host
     * @param $user
     * @param $password
     * @param $port
     * @param $database
     * @param $socket
     */
    public function __construct($host = null, $user = null, $password = null, $database = null, $port = null, $socket = null)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port ?? 3306;
        $this->database = $database;
        $this->socket = $socket;
    }

    public function __invoke()
    {
        $this->connect();
    }


    public function connect()
    {
        $this->ress = mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
        if (false === $this->ress) {
            throw new GenericDatabaseException('Connexion KO ' . $this->user . '@' . $this->host);
        }
        mysqli_select_db($this->ress, $this->database);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function sprintfForQuery($string, $tabOfArgs)
    {
        if (false === $this->ress) {
            throw new GenericDatabaseException("Ress is false");
        }
        $tabOfArgs = array_map(function ($arg) {
            return mysqli_real_escape_string($this->ress, $arg);
        }, $tabOfArgs);
        return vsprintf($string, $tabOfArgs);
    }

    public function formatForQuery($string, $tabOfArgs)
    {
        $replace_pairs = [];
        foreach ($tabOfArgs as $index => $val) {
            $replace_pairs['{' . $index . '}'] = mysqli_real_escape_string($this->ress, $val);
        }
        return strtr($string, $replace_pairs);
    }

    public function getInsertOfUpdateSqlCode($tablename, $tabData, string $whereClause)
    {
        $return = $this->getDeleteSqlCode($tablename, $whereClause);
        $return .= $this->getInsertSqlCode($tablename, $tabData);

        return $return;
    }

    public function getDeleteSqlCode($tablename, string $whereClause)
    {
        return "DELETE FROM $tablename WHERE $whereClause;";
    }

    public function getInsertSqlCode($tablename, $tabData)
    {
        $tabDataEscaped = [];
        $tabColsEscaped = [];
        foreach ($tabData as $index => $val) {
            $strVal = null === $val ? 'NULL' : '"' . mysqli_real_escape_string($this->ress, $val) . '"';
            $tabDataEscaped[$index] = $strVal;
            $tabColsEscaped[] = mysqli_real_escape_string($this->ress, $index);
        }
        $strSqlValues = implode(',', $tabDataEscaped);
        $strSqlKeys = implode(',', $tabColsEscaped);
        return "\nINSERT INTO $tablename ($strSqlKeys) VALUES ($strSqlValues);";
    }

    public function execute(string $sql)
    {
        mysqli_multi_query($this->ress, $sql);
        while (mysqli_more_results($this->ress)) {
            mysqli_next_result($this->ress);
        }
    }


}