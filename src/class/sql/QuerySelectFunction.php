<?php
/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
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

use hemio\edentata\exception;

/**
 * Description of Query
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class QuerySelectFunction extends QuerySelect
{
    /**
     *
     * @var Connection
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
    protected $select  = null;
    protected $as      = null;

    /**
     *
     * @param \PDO $pdo
     * @param string $functionName
     * @param array $funcParams
     */
    public function __construct(\PDO $pdo, $functionName, array $funcParams = [])
    {
        $this->pdo          = $pdo;
        $this->functionName = $functionName;
        $this->funcParams   = $funcParams;
    }

    /**
     * @param string $name
     * @return string
     */
    public function sqlName($name)
    {
        if (strpos($name, '"') !== false)
            throw new \Exception('SQL names with double quotes not supported.');

        $arr = array_map(function ($str) {
            return '"'.$str.'"';
        }, explode('.', $name));

        return implode('.', $arr);
    }

    public function getFunctionCall()
    {
        $sqlParams = [];
        foreach (array_keys($this->funcParams) as $name) {
            $sqlParams[] = $this->sqlName($name).' := :'.$name;
        }

        return $this->sqlName($this->functionName).'('.implode(', ', $sqlParams).')';
    }

    public function options($options)
    {
        $this->options = $options;
    }

    public function select(array $select)
    {
        $this->select = $select;
    }

    public function selectAs($as)
    {
        $this->as = $as;
    }

    /**
     *
     * @return \PDOStatement
     */
    public function execute($params = [])
    {
        if ($this->select === null) {
            $selectString = '*';
        } else {
            $selectParts = [];
            foreach ($this->select as $key => $value) {
                if (is_int($key))
                    $selectParts[] = $value;
                else
                    $selectParts[] = $key.' AS '.$value;
            }
            $selectString = implode(', ', $selectParts);
        }

        $as = '';
        if ($this->as)
            $as = ' AS '.$this->as;

        $sqlCode = sprintf('SELECT %s FROM %s%s %s'
            , $selectString
            , $this->getFunctionCall()
            , $as
            , $this->options
        );

        $stmt = $this->pdo->prepare($sqlCode);

        try {
            $stmt->execute($this->funcParams + $params);
        } catch (\PDOException $e) {
            $errMessage = $e->errorInfo[2];
            $reg        = '/DETAIL:\s+\$carnivora:(.*):(.*)\$/';
            $matches    = [];
            if (preg_match($reg, $errMessage, $matches)) {
                $carnivoraKey = $matches[1].':'.$matches[2];

                $eIn = new exception\SqlSpecific(
                    $carnivoraKey
                    , 0
                    , $e);

                foreach ($this->pdo->getExceptionMapper() as $mapper) {
                    $eOut = $mapper->map($eIn);
                    if (!$eOut instanceof exception\SqlSpecific)
                        break;
                }

                throw $eOut;
            } else {
                throw $e;
            }
        }

        return $stmt;
    }
}
