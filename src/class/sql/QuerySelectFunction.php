<?php

/*
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace hemio\edentata\sql;

/**
 * Description of Query
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class QuerySelectFunction extends QuerySelect {

    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     *
     * @var string
     */
    protected $functionName = '';

    /**
     * @var array
     */
    protected $funcParams = [];

    /**
     *
     * @var string 
     */
    protected $options = '';
    protected $select = null;

    /**
     *
     * @param \PDO $pdo
     * @param string $functionName
     * @param array $funcParams
     */
    public function __construct(\PDO $pdo, $functionName, array $funcParams = []) {
        $this->pdo = $pdo;
        $this->functionName = $functionName;
        $this->funcParams = $funcParams;
    }

    /**
     * @todo there might be more to escape
     * @param string $name
     * @return string
     */
    public function sqlName($name) {
        $arr = array_map(function ($str) {
            return '"' . $str . '"';
        }, explode('.', $name));

        return implode('.', $arr);
    }

    public function getFunctionCall() {
        $sqlParams = [];
        foreach (array_keys($this->funcParams) as $name) {
            $sqlParams[] = $this->sqlName($name) . ' := :' . $name;
        }

        return $this->sqlName($this->functionName) . '(' . implode(', ', $sqlParams) . ')';
    }

    public function options($options) {
        $this->options = $options;
    }

    public function select(array $select) {
        $this->select = $select;
    }

    /**
     * 
     * @return \PDOStatement
     */
    public function execute($params = []) {
        if ($this->select === null) {
            $selectString = '*';
        } else {
            $selectParts = [];
            foreach ($this->select as $key => $value) {
                if (is_int($key))
                    $selectParts[] = $value;
                else
                    $selectParts[] = $key . ' AS ' . $value;
            }
            $selectString = implode(', ', $selectParts);
        }

        $stmt = $this->pdo->prepare('SELECT ' . $selectString . ' FROM ' . $this->getFunctionCall() . ' ' . $this->options);
        try {
            $stmt->execute($this->funcParams + $params);
        } catch (\PDOException $e) {
            $errMessage = $e->errorInfo[2];
            $reg = '/DETAIL:\s+\$carnivora:(.*):(.*)\$/';
            $matches = [];
            if (preg_match($reg, $errMessage, $matches)) {
                $carnivoraKey = $matches[2];

                ExceptionMapping::throwMapped(new \hemio\edentata\exception\SqlSpecific($carnivoraKey, 0, $e));
            } else {
                throw $e;
            }
        }

        return $stmt;
    }

}
