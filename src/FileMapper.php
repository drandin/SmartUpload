<?php

/**
 * @author      Igor Drandin <idrandin@gmail.com>
 * @copyright   2015 Igor Drandin
 */

namespace SmartUpload;

/**
 * Class FileMapper
 * @package SmartUpload\
 */
class FileMapper
{
    /**
     * @var array
     */
    protected $stmt = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $fieldsName = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $arrParameters = array(
        'fileStorage'
    );

    /**
     * @param $parameters
     * @param \PDO $db
     * @throws \Exception
     */
    public function __construct($parameters, \PDO $db)
    {
        if (!empty($parameters) && is_array($parameters)) {

            foreach ($this->arrParameters as $item) {
                if (!empty($parameters[$item])) $this->parameters[$item] = $parameters[$item];
                else throw new \Exception("Parameter {$item} is wrong!");
            }

            $this->db = $db;
        }
    }

    /**
     * Задаёт параметры полей таблицы в массив $this->fields
     * @param $table
     * @return $this
     */
    protected function getFields($table)
    {
        if (!empty($table) && is_string($table) && empty($this->fields[$table])) {
            if (!isset($this->stmt['showColumns'])) {
                $this->stmt['showColumns'] = $this->db->prepare("SHOW COLUMNS FROM {$table}");
            }

            $sth = $this->stmt['showColumns'];

            if ($sth instanceof \PDOStatement) {
                if ($sth->execute()) {
                    if ($fields = $sth->fetchAll(\PDO::FETCH_ASSOC)) {
                        $this->fields[$table] = $fields;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Возвращает массив имён полей таблицы
     * @param $table
     * @return array
     */
    protected function getFieldsName($table)
    {
        if (!empty($table) && empty($this->fieldsName[$table])) {
            $this->fieldsName[$table] = array_column($this->fields[$table], 'Field');
        }

        return $this->fieldsName[$table];
    }

    /**
     * Возвращает имя метода для получения свойства $nameProperty
     * @param $nameProperty
     * @param $obj
     * @return bool|string
     */
    private function getNameMethod($nameProperty, $obj)
    {
        if (preg_match('/^[a-z]{1}[0-9A-Za-z_]+$/', $nameProperty)) {
            $nameMethod = 'get'.ucfirst($nameProperty);
            if (method_exists($obj, $nameMethod)) return $nameMethod;
        }

        return false;
    }

    /**
     * @param $nameProperty
     * @param $obj File
     * @return bool
     */
    private function getProperty($nameProperty, File $obj)
    {
        $nameMethod = $this->getNameMethod($nameProperty, $obj);

        if ($nameMethod) {
            return $obj->$nameMethod();
        }

        return null;
    }


    /**
     * @param $tbl
     * @return array
     */
    protected function fieldsTableData($tbl)
    {
        $tableFileStorage = $this->parameters[$tbl];

        return $this
            ->getFields($tableFileStorage)
            ->getFieldsName($tableFileStorage);
    }

    /**
     * @param File $obj
     * @return int
     */
    public function insertData(File $obj)
    {
        $fields = $values = [];
        $tableFileStorage = $this->parameters['fileStorage'];

        foreach ($this->fieldsTableData($tableFileStorage) as $field) {
            $value = $this->getProperty($field, $obj);
            if (!empty($value)) {
                list($fields[], $values[]) = array($field, $value);
            }
        }

        if (sizeof($values) > 0 ) {

            $sql = "INSERT INTO {$tableFileStorage} (" . implode(", ", $fields) . ")
                         VALUES (" . rtrim(str_repeat('?, ', sizeof($fields)), ', ') . ");";

            $sth = $this->db->prepare($sql);

            if ($sth->execute($values)) {
                return (int)$this->db->lastInsertId();
            }
        }

        return 0;
    }

    /**
     * @param $data
     * @return File
     */
    protected function createObject($data)
    {
        return new File($data);
    }

    /**
     * @param $idFile
     * @return array
     */
    protected function getOneArray($idFile)
    {
        if ($idFile > 0) {

            $tableFileStorage = $this->parameters['fileStorage'];

            $sql = "SELECT " . implode(',', $this->fieldsTableData($tableFileStorage)) . "
                     FROM {$tableFileStorage}
                    WHERE idFile = :idFile";

            $sth = $this->db->prepare($sql);

            $sth->bindValue(':idFile', (int)$idFile, \PDO::PARAM_INT);

            if ($sth->execute()) {
                $data = $sth->fetch(\PDO::FETCH_ASSOC);
            }
        }

        return (!empty($data)) ? $data : [];
    }


    /**
     * @param $idFile
     * @return bool
     */
    public function delete($idFile)
    {
        if ($idFile > 0 && !empty($this->parameters['fileStorage'])) {

            $sql = "DELETE FROM {$this->parameters['fileStorage']}
                     WHERE idFile = :idFile;";

            $sth = $this->db->prepare($sql);
            $sth->bindValue(':idFile', (int)$idFile, \PDO::PARAM_INT);
            return $sth->execute();
        }

        return false;
    }

    /**
     * @param array $searchCriteria
     * @param null $orderBy
     * @param int $skip
     * @param int $show
     * @return bool|FileCollection
     */
    public function getCollection($searchCriteria = [], $skip = 0, $show = 0, $orderBy = null)
    {
        $fileCollection = false;

        $tableFileStorage = $this->parameters['fileStorage'];

        $skip = (int)$skip;
        $show = (int)$show;

        $sql = "SELECT ".implode(', ', $this->fieldsTableData($tableFileStorage))."
                  FROM {$tableFileStorage} ";

        if (is_array($searchCriteria)) {
            foreach ($searchCriteria as $name => $value) {
                $where[] = "{$name} = ? ";
            }
        }

        if (!empty($where)) {
            $sql.= " WHERE ".implode(' AND ', $where);
        }

        if (!empty($orderBy) && is_string($orderBy)) {
            $sql.= " ORDER BY ".$orderBy;
        }

        if ($skip >= 0 && $show > 0) {
            $sql.= " LIMIT {$skip}, {$show}";
        }

        $sth = $this->db->prepare($sql);

        if ($sth->execute(array_values($searchCriteria))) {
            $fileCollection = new FileCollection;
            while ($data = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $fileCollection->addItem($this->createObject($data));
            }
        }

        return $fileCollection;
    }


    /**
     * @param array $searchCriteria
     * @return int
     */
    public function countFiles($searchCriteria = [])
    {
        $tableFileStorage = $this->parameters['fileStorage'];

        $sql = "SELECT COUNT(*) FROM {$tableFileStorage} ";

        if (is_array($searchCriteria)) {
            foreach ($searchCriteria as $name => $value) {
                $where[] = "{$name} = ? ";
            }
        }

        $sth = $this->db->prepare($sql);

        if ($sth->execute(array_values($searchCriteria))) {
            $data = $sth->fetch(\PDO::FETCH_NUM);
            if (isset($data[0])) return (int)$data[0];
        }

        return 0;
    }

    /**
     * @param $idFile
     * @return File
     */
    public function getOne($idFile)
    {
        return $this->createObject($this->getOneArray($idFile));
    }

    /**
     * Change $codeSubject and $essence
     * @param $codeSubject
     * @param $codeSubjectNew
     * @param $essence
     * @param $essenceNew
     * @return bool
     */
    public function changeCodeSubject($codeSubject, $codeSubjectNew, $essence, $essenceNew)
    {
        if ($codeSubject >= 0 && $codeSubjectNew >= 0 && ctype_alnum($essence) && ctype_alnum($essenceNew)) {

            $sql = "UPDATE {$this->parameters['fileStorage']}
                       SET codeSubject = :codeSubjectNew,
                           essence = :essenceNew
                     WHERE codeSubject = :codeSubject
                       AND essence = :essence";

            $sth = $this->db->prepare($sql);
            $sth->bindValue(':codeSubjectNew', (int)$codeSubjectNew, \PDO::PARAM_INT);
            $sth->bindValue(':codeSubjectCurrent', (int)$codeSubject, \PDO::PARAM_INT);
            $sth->bindValue(':essenceNew', $essenceNew, \PDO::PARAM_STR);
            $sth->bindValue(':essenceCurrent', $essence, \PDO::PARAM_STR);
            return $sth->execute();
        }

        return false;
    }



}