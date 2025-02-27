<?php

class Database
{

    // Singleton object. Leave $me alone.
    private static $me;
    public $db;
    public $host;
    public $name;
    public $username;
    public $password;
    public $dieOnError;
    public $queries;
    public $result;
    public $redirect = false;

    // Singleton constructor
    private function __construct($connect = false)
    {
        $Config = Config::getConfig();

        $this->host       = $Config->dbHost;
        $this->name       = $Config->dbName;
        $this->username   = $Config->dbUsername;
        $this->password   = $Config->dbPassword;
        $this->dieOnError = $Config->dbDieOnError;

        $this->db      = false;
        $this->queries = array();

        if ($connect === true)
        {
            $this->connect();
        }
    }

    // Waiting (not so) patiently for 5.3.0...
    public static function __callStatic($name, $args)
    {
        return self::$me->__call($name, $args);
    }

    // Get Singleton object
    public static function getDatabase($connect = true)
    {
        if (is_null(self::$me))
            self::$me = new Database($connect);
        return self::$me;
    }

    // Do we have a valid database connection?
    public function isConnected()
    {
        return is_object($this->db);
    }

    // Do we have a valid database connection and have we selected a database?
    public function databaseSelected()
    {
        if (!$this->isConnected())
        {
            return false;
        }

        $result = $this->db->query("SHOW TABLES");

        return is_object($result);
    }

    public function connect()
    {
        // check for the MySQL PDO driver
		if($this->havePDODriver() == false)
		{
			$this->notify('PDO driver unavailable. Please contact your host to request the MySQL PDO driver to be enabled within PHP.');
		}
        
        try
        {
            $this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->name . ";charset=utf8", $this->username, $this->password);
        }
        catch (Exception $e)
        {
            $this->notify('Failed connecting to the database with the supplied connection details. Please check the details are correct and your MySQL user has permissions to access this database.<br/><br/>(host: ' . $this->host . ', user: ' . $this->username . ', pass: ********)');
        }

        if ($this->db === false)
        {
            return false;
        }

        if ($this->isConnected())
        {
            $this->db->exec("SET NAMES utf8");
        }

        return $this->isConnected();
    }

    public function close()
    {
        self::closeDB();
    }

    public static function closeDB()
    {
        if (!is_null(self::$me))
        {
            self::$me->db = null;
            self::$me = null;
        }
    }

    public function query($sql, $args_to_prepare = null, $exception_on_missing_args = true)
    {
        if (!$this->isConnected())
        {
            $this->connect();
        }

        // Allow for prepared arguments. Example:
        // query("SELECT * FROM table WHERE id = :id", array('id' => $some_val));
        $sth = $this->db->prepare($sql);
        $debugSql = $sql;
        $params = array();
        if (is_array($args_to_prepare))
        {
            foreach ($args_to_prepare AS $name => $val)
            {
                $params[':' . $name] = $val;
                $debugSql = preg_replace('/:'.$name.'/',"'".$val."'", $debugSql);
            }
        }

        $start = microtime();
        $startEx = explode(' ', $start);
        $start = $startEx[1] + $startEx[0];
        try
        {
            $sth->execute($params);
        }
        catch (Exception $e)
        {
            $this->notify($e);
        }
        $end = microtime();
        $endEx = explode(' ', $end);
        $end = $endEx[1] + $endEx[0];

        $total = number_format($end-$start, 6);
        $this->queries[] = array('sql'=>$debugSql, 'start'=>$start, 'end'=>$end, 'total'=>$total);
        
        $this->result = $sth;

        return $this->result;
    }

    // Returns the number of rows.
    // You can pass in nothing, a string, or a db result
    public function numRows($arg = null)
    {
        $result = $this->resulter($arg);

        return ($result !== false) ? $result->rowCount() : false;
    }

    // Returns true / false if the result has one or more rows
    public function hasRows($arg = null)
    {
        $result = $this->resulter($arg);

        return is_object($result) && ($result->rowCount() > 0);
    }

    // Returns the number of rows affected by the previous operation
    public function affectedRows()
    {
        if (!$this->isConnected())
        {
            return false;
        }

        return $this->result->rowCount();
    }

    // Returns the auto increment ID generated by the previous insert statement
    public function insertId()
    {
        if (!$this->isConnected())
        {
            return false;
        }

        $id = $this->db->lastInsertId();
        if ($id === 0 || $id === false)
        {
            return false;
        }

        return $id;
    }

    // Returns a single value.
    // You can pass in nothing, a string, or a db result
    public function getValue($arg = null)
    {
        $result = $this->resulter($arg);
        $data = false;
        if($result)
        {
            $row = $result->fetch(PDO::FETCH_BOTH);
            $data = $row[0];
        }
        
        return $data;
    }

    // Returns an array of the first value in each row.
    // You can pass in nothing, a string, or a db result
    /*
    public function getValues($arg = null)
    {
        $result = $this->resulter($arg);
        if (!$result)
        {
            return array();
        }

        $values   = array();
        mysql_data_seek($result, 0);
        while ($row      = mysql_fetch_array($result, MYSQL_ASSOC))
            $values[] = array_pop($row);
        return $values;
    }
     * 
     */

    // Returns the first row.
    // You can pass in nothing, a string, or a db result
    public function getRow($arg = null)
    {
        $result = $this->resulter($arg);
        $data = $result->fetch(PDO::FETCH_BOTH);

        return $result->rowCount() ? $data : false;
    }

    // Returns an array of all the rows.
    // You can pass in nothing, a string, or a db result
    public function getRows($arg = null)
    {
        $result = $this->resulter($arg);
        $data = $result->fetchAll(PDO::FETCH_BOTH);

        return $result->rowCount() ? $data : array();
    }

    // Escapes a value and wraps it in single quotes.
    public function quote($var)
    {
        if (!$this->isConnected())
        {
            $this->connect();
        }
        
        return $this->db->quote($var);
    }

    // Escapes a value.
    public function escape($var)
    {
        if (!$this->isConnected())
        {
            $this->connect();
        }
        
        $str = $this->db->quote($var);
        if(strlen($str) > 2)
        {
            $str = substr($str, 1, strlen($str)-2);
        }
        
        return $str;
    }

    public function numQueries()
    {
        return COUNT($this->queries);
    }

    public function lastQuery()
    {
        if ($this->numQueries() > 0)
        {
            return $this->queries[$this->numQueries() - 1];
        }

        return false;
    }

    private function notify($err_msg = null)
    {
        if ($err_msg === null)
        {
            $errors = $this->db->errorInfo();
            $err_msg = implode(".", $errors);
        }
        error_log($err_msg);

        if ($this->dieOnError === true)
        {
            echo "<p style='border:5px solid red;background-color:#fff;padding:12px;font-family: verdana, sans-serif;'><strong>Database Error:</strong><br/>$err_msg</p>";
            if (strlen($this->lastQuery()))
            {
                echo "<p style='border:5px solid red;background-color:#fff;padding:12px;font-family: verdana, sans-serif;'><strong>Last Query:</strong><br/>" . $this->lastQuery() . "</p>";
            }
            //echo "<pre>";
            //debug_print_backtrace();
            //echo "</pre>";
            exit;
        }

        if (is_string($this->redirect))
        {
            header("Location: {$this->redirect}");
            exit;
        }
    }

    // Takes nothing, a MySQL result, or a query string and returns
    // the correspsonding MySQL result resource or false if none available.
    private function resulter($arg = null)
    {
        if (is_null($arg) && is_object($this->result))
        {
            return $this->result;
        }
        elseif (is_object($arg))
        {
            return $arg;
        }
        elseif (is_string($arg))
        {
            $this->query($arg);
            if (is_object($this->result))
            {
                return $this->result;
            }

            return false;
        }

        return false;
    }
    
    private function havePDODriver()
	{
		// check for pdo driver
		if (!defined('PDO::ATTR_DRIVER_NAME'))
		{
			return false;
		}
		
		return true;
	}

}
