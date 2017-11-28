<?php


class DataBase {

    private $conn;
    private $result;
    private $RowCount;
    private $queryInsert;
    private $dbname;



    /**
     * Método construtor, que conecta ao banco de dados.
     *
     */
    public function __construct(){
        

        try {
            $this->dbname = "dev";
            $this->conn = new PDO("mysql:host=www.connect-bus.com.br;port=3306;dbname=dev;charset=utf8",
            "root", "picollo", array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getDbName(){
        return $this->dbname;
    }
    /**
     * Select no banco de dados
     *
     * @param string $fields
     * @param $table
     * @param array $conditions
     * @return mixed
     */
    public function find($table, $fields = "all", $conditions = null){
        if(!empty($table)){

            $this->Exec("SELECT {$this->CheckField($fields)} FROM {$this->dbname}.{$table} {$this->Conditions($conditions)}");
            return $this->result;
        }
        die("table name does not exist!");
    }

    public function Query($qry){
        $this->Exec($qry);
        return $this->result;
    }

    /**
     * Insert no banco de dados
     *
     * @param $table
     * @param $insert
     */
    public function insert($table, $insert){
           $this->dealsInsert($insert);
            try {
                $this->conn->query("INSERT INTO {$this->dbname}.{$table} (" . $this->queryInsert['fields'] . ") VALUES " . $this->queryInsert['values']);
            } catch (Exception $e) {
                echo $e->getMessage();
//                $this->error();
            }
    }

    private function error(){
        $error = $this->conn->errorInfo();
        echo "COD {$error[1]} ({$error[0]})";
    }
    /**
     * Insert free
     * @param $qry
     */
    public function insertFree($qry){
        try{
            $this->conn->query($qry);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function lastId(){
        return $this->conn->lastInsertId();
    }




    /**
     * Cria o objeto dos dados da consulta find()
     *
     * @param $sql
     */
    private function Exec($sql){
        try {
            // echo $sql; die();
            $find = $this->conn->query($sql);
            $this->RowCount = $find->rowCount();
            $this->result = $find->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Retorna o número de dados de uma consulta.
     *
     * @return int
     */
    public function getRowCount(){
        return $this->RowCount;
    }

    /**
     * Verifica se o array tem um proximo elemento.
     *
     * @param $array
     * @return bool
     */
    private function has_next($array) {
        if (is_array($array)) {
            if (next($array) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Método que trata o array do insert com os dados,
     * verifica se é um array, depois cria os campos e chama o método $this->MultipleValue
     * e retorna o valor para função.
     *
     * @param $array
     * @return array
     */
    private function dealsInsert($array){
        $save = $qnt = "";
        if(is_array($array)){
            $values = array('fields' => '','values' => '');
            for ($i = 0; $i < count($array); $i++) {
                if ($this->has_next($array)) {
                    $values['fields'] = $values['fields'] . key($array) . ",";
                } else {
                    $values['fields'] = $values['fields'] . key($array);
                }
                $qnt = count(current($array));
                $save = $save . implode(".-.",current($array)). ".-.";
                next($array);
            }
            $values['values'] = $this->MultipleValue($save, $qnt, count($array));

            return $this->queryInsert = $values;
        } else {
            die("The insert should be done in array");
        }
    }

    /**
     * método que cria os valores da inserção
     *
     * @param $str
     * @param $qnt
     * @param $segFor
     * @return string
     */
    private function MultipleValue($str, $qnt, $segFor){
        $str = explode(".-.",$str);
        $result = "";
        $aux = 0;
        for($i = 1; $i <= $qnt; $i++){
            $new = "( ";
            for($j = 0; $j < $segFor; $j++){
                $j == $segFor -1 ? $new = $new . $str[$aux] : $new = $new . $str[$aux] . ", ";
                $aux = $aux + $qnt;
            }
            $i == $qnt ? $new = $new . ") " :  $new = $new . " ), ";

            $result = $result . $new;
            $aux = $i;
        }
        return $result;
    }

    /**
     * Cria a condição para select, update, inserts
     *
     * @param $array
     * @return null|string
     */
    private function Conditions($array){
        if($array != null) {
            $where = " WHERE ";
            for ($i = 0; $i < count($array); $i++) {
                if ($this->has_next($array)) {
                    $where = $where . key($array) . current($array) . " AND ";
                } else {
                    $where = $where . key($array) . current($array);
                }
                next($array);
            }
            return $where;
        }
        return null;
    }

    /**
     *  verifica se os deseja um select com todos os campos ou os desejados
     *
     * @param $field
     * @return string
     */
    private function CheckField($field){
        return $field === "all" ? "*" : $field;
    }

}