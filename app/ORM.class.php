<?php

    namespace app;


    class ORM
    {
        private $PDOInstance = null;
        private static $instance = null;
        private $sqlDB;

        private function __construct()
        {
            try {
                require_once('config/database.php');
                $this->sqlDB = $DB_BASE;
                $this->PDOInstance = new \PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
                $this->PDOInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }

        public static function getInstance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new ORM();
            }
            return self::$instance;
        }

        public function findOne($table, $where)
        {
            $req = "SELECT * FROM " . $table . " WHERE 1 = 1";
            foreach ($where as $k => $v)
                $req .= " AND " . $k . " = :" . $k;
            $statment = $this->PDOInstance->prepare($req);
            foreach ($where as $k => $v)
                $statment->bindValue(':' . $k, $v);
            $statment->setFetchMode(\PDO::FETCH_CLASS, 'app\model\\'.ucfirst($table));
            $statment->execute();
            return $statment->fetch(\PDO::FETCH_CLASS);
        }

        public function findAll($table, $where, $order = null, $limit = null)
        {
            $req = "SELECT * FROM " . $table . " WHERE 1 = 1";
            foreach ($where as $k => $v)
                $req .= " AND " . $k . " = :" . $k;
            if (!empty($order))
                $req .= " ORDER BY ".$order[0]." ".$order[1];
            if (!empty($limit))
                $req .= " LIMIT ".$limit[0].",".$limit[1];
            $statment = $this->PDOInstance->prepare($req);
            foreach ($where as $k => $v)
                $statment->bindValue(':' . $k, $v);
            $statment->execute();
            return $statment->fetchAll(\PDO::FETCH_ASSOC);
        }

        private function getFields($table)
        {
            $statment = $this->PDOInstance->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema = :base AND table_name = :table");
            $statment->bindValue(':table', $table);
            $statment->bindValue(':base', $this->sqlDB);
            $statment->execute();
            return ($statment->fetchAll(\PDO::FETCH_COLUMN));
        }

        private function insert($table, $fields, $value)
        {
            $req_field = '';
            $req_value = '';
            unset($value['id']);
            foreach ($value as $k => $v)
            {
                if (in_array($k, $fields))
                {
                    $req_field .= '`'.$k.'`, ';
                    $req_value .= ':'.$k.', ';
                }
            }
            $req = 'INSERT INTO '.$table.' ('.rtrim($req_field, ', ').') VALUES ('.rtrim($req_value, ', ').')';
            $statment = $this->PDOInstance->prepare($req);
            foreach ($value as $k => $v)
            {
                if (in_array($k, $fields))
                {
                    $statment->bindValue(':' . $k, $v);
                }
            }
            try{
                $statment->execute();
            } catch(\Exception $e) {
                echo "<pre>";
                echo $req;
                print_r($value);
                echo $e->getMessage();
                exit();
            }
            return ($this->PDOInstance->lastInsertId());
        }

        private function update($table, $fields, $value)
        {
            $req_field = '';
            foreach ($value as $k => $v)
            {
                if (in_array($k, $fields))
                {
                    $req_field .= '`'.$k.'`=:'.$k.', ';
                }
            }
            $req = 'UPDATE '.$table.' SET '.rtrim($req_field, ', ').' WHERE id = :id';
            $statment = $this->PDOInstance->prepare($req);
            foreach ($value as $k => $v)
            {
                if (in_array($k, $fields))
                {
                    $statment->bindValue(':' . $k, $v);
                }
            }
            $statment->bindValue(':id', $value['id']);
            $statment->execute();
            return (true);
        }

        public function count($table, $where){
            $req = "SELECT count(*) FROM " . $table . " WHERE 1 = 1";
            foreach ($where as $k => $v)
                $req .= " AND " . $k . " = :" . $k;
            $statment = $this->PDOInstance->prepare($req);
            foreach ($where as $k => $v)
                $statment->bindValue(':' . $k, $v);
            $statment->execute();
            $tmp = $statment->fetch();
            return $tmp[0];
        }

        public function store($table, $value)
        {
            $fields = $this->getFields($table);
            if ($value['id'] == NULL)
                return ($this->insert($table, $fields, $value));
            else
                return ($this->update($table, $fields, $value));
        }

        public function delete($table, $id)
        {
            $req = 'DELETE FROM '.$table.' WHERE id = :id';
            $statment = $this->PDOInstance->prepare($req);
            $statment->bindValue(':id', $id);
            $statment->execute();
        }
    }
