<?php
namespace App;

class Database
{
    private $con;


    public function __construct()
    {

        $this->host = $GLOBALS['database']['host'];
        $this->user = $GLOBALS['database']['user'];
        $this->password = $GLOBALS['database']['password'];
        $this->database = $GLOBALS['database']['database'];

        $this->con = "";
        $this->table = "";
        $this->data = "";
        $this->tableField = "*";
        $this->order = "";
        $this->cond = "";
        $this->limit = "";
        $this->gate = " AND ";
    }


    public function connectDb()
    {
        try {
            $this->con = new \PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            $this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    private function executes($sql)
    {
        try {
            $result = $this->con->exec($sql);
            return $result;
        } catch (\PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
            return false;
        }
    }

    private function fetchAssoc($rs)
    {
        $stmt = $this->con->prepare($rs);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_NAMED);
        return $stmt->fetchall();
    }

    public function totalRows()
    {
        $query = "SELECT count(*) FROM $this->table ";
        $carr = array();
        if ($this->cond != "") {
            foreach ($this->cond as $k => $v) {
                $carr[$k] = "$k = '$v'";
            }
            if (count($carr) > 0) {
                $cstr = " WHERE " . implode($this->gate, $carr);
                $query .= $cstr;
            }
        }

        if ($this->order != "") {
            $query .= " ORDER BY " . $this->order . " ";
        }

        if ($this->limit != "") {
            $query .= " LIMIT " . $this->limit . " ";
        }
        $result = $this->con->prepare($query);
        $result->execute();
        $count = $result->fetchColumn();

        return $count;
    }

    public function insert()
    {
        $query = "INSERT INTO $this->table SET ";

        foreach ($this->data as $k => $v) {
            $arr[$k] = " $k = '$v' ";
        }

        if (count($arr) > 0) {
            $str = implode(",", $arr);
        }

        $query = $query . $str;


        $result = $this->executes($query);

        return $result;
    }

    public function update()
    {
        $query = "UPDATE $this->table SET ";

        foreach ($this->data as $k => $v) {
            if ($v == null) {
                $arr[$k] = "$k=null";
            } else {
                $arr[$k] = "$k='$v'";
            }
        }
        if (count($arr) > 0) {
            $query .= implode(",", $arr);
        }
        foreach ($this->cond as $k => $v) {
            $carr[$k] = "$k='$v'";
        }
        if (count($carr) > 0) {
            $query .= " WHERE " . implode($this->gate, $carr);
        }
        $this->executes($query);
    }

    public function delete()
    {
        $query = "DELETE FROM $this->table WHERE ";
        foreach ($this->data as $k => $v) {
            $arr[$k] = "$k='$v'";
        }
        if (count($arr) > 0) {
            $query .= implode(" AND ", $arr);
        } else {
            die("wrong query!");
        }
        $this->executes($query);
    }

    public function select()
    {
        $query = "SELECT $this->tableField FROM $this->table ";
        $carr = array();
        if ($this->cond != "") {
            foreach ($this->cond as $k => $v) {
                $carr[$k] = "$k = '$v'";
            }
            if (count($carr) > 0) {
                $cstr = " WHERE " . implode($this->gate, $carr);
                $query .= $cstr;
            }
        }

        if ($this->order != "") {
            $query .= " ORDER BY " . $this->order . " ";
        }

        if ($this->limit != "") {
            $query .= " LIMIT " . $this->limit . " ";
        }

        $result = $this->fetchAssoc($query);
        if (sizeof($result) != 0) {
            return $result[0];
        } else {
            return $result;
        }
    }

    public function redirectTo($url)
    {
        header("Location: " . $url);
        exit;
    }

    public function flushTable()
    {
        $this->table = "";
        $this->data = array();
        $this->tableField = "*";
        $this->order = "";
        $this->cond = array();
        $this->limit = "";
    }
}
