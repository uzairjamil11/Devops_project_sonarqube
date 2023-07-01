<?php
namespace Phppot;
use PDO;
class DataSource {
    private $conn;

    private const HOST = 'mysql';
    private const USERNAME = 'admin';
    private const PASSWORD = 'admin';
    private const DATABASENAME = 'user-registration';

    public function __construct() {
        $this->conn = $this->getConnection();
    }

    public function getConnection() {
        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DATABASENAME;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
        ];

        try {
            $conn = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
        } catch (PDOException $e) {
            trigger_error("Problem with connecting to database: " . $e->getMessage());
        }

        return $conn;
    }

    public function select($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);
    
        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
    
        $stmt->execute();
        $resultset = $stmt->fetchAll();
    
        return $resultset;
    }
    

    public function insert($query, $paramType, $paramArray)
    {
        $stmt = $this->conn->prepare($query);
        $this->bindQueryParams($stmt, $paramType, $paramArray);

        $stmt->execute();
        $insertId = $stmt->insert_id;
        return $insertId;
    }

    public function execute($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }

        $stmt->execute();
    }

    public function bindQueryParams($stmt, $paramType, $paramArray = array())
{
    if (!empty($paramType) && !empty($paramArray)) {
        foreach ($paramArray as $key => $value) {
            $stmt->bindValue($key + 1, $value, $this->getParamType($paramType[$key]));
        }
    }
}

private function getParamType($value)
{
    switch (gettype($value)) {
        case 'integer':
            return PDO::PARAM_INT;
        case 'boolean':
            return PDO::PARAM_BOOL;
        case 'NULL':
            return PDO::PARAM_NULL;
        default:
            return PDO::PARAM_STR;
    }
}


    public function getRecordCount($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }

        $stmt->execute();
        $stmt->store_result();
        $recordCount = $stmt->num_rows;

        return $recordCount;
    }
}
?>
