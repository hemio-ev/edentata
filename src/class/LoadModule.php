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

namespace hemio\edentata;

/**
 * Description of LoadModule
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class LoadModule {

    /**
     *
     * @var string
     */
    protected $moduleClass;

    /**
     *
     * @var string
     */
    protected $moduleId;

    /**
     *
     * @var sql\Connection 
     */
    protected $pdo;

    /**
     * Returns true if the given string is a valid module name. Module names
     * must start with a lowercase letter and continue with at least one other
     * lowercase letter, number or underscore. It must not contain more then
     * 50 characters.
     * 
     * @param type $name
     * @return type
     */
    public static function validName($moduleId) {
        $match = preg_match('/^[a-z][a-z0-9_]+$/', $moduleId);
        $length = strlen($moduleId) <= 50;
        return $match && $length;
    }

    public static function absolutePathToClass($moduleId) {
        return sprintf(
                '\hemio\edentata\module\%s\Module%s', $moduleId, ucfirst($moduleId)
        );
    }

    /**
     * 
     * @param string $moduleId
     */
    public function __construct($moduleId, sql\Connection $pdo = null) {
        $this->moduleId = $moduleId;
        $this->pdo = $pdo;

        if (!self::validName($moduleId)) {
            $msg = sprintf(
                    _('Failed to load module "%s". The module id is invalid.'), $moduleId
            );
            throw new exception\Error($msg);
        }

        // construct absolute path to module class
        $moduleClassPath = self::absolutePathToClass($moduleId);

        if (!class_exists($moduleClassPath)) {
            $msg = sprintf(
                    _('Failed to load module "%s". Module not found.'), $moduleId
            );
            throw new exception\Error($msg);
        }

        $this->moduleClass = $moduleClassPath;
    }

    public function getName() {
        $moduleClass = $this->moduleClass;
        return $moduleClass::getName();
    }

    /**
     * 
     * @param Request $request
     * @return Module
     */
    public function getInstance(Request $request) {
        $moduleClass = $this->moduleClass;
        return new $moduleClass($request, $this->pdo);
    }

    public function getContent(Request $request) {
        $module = $this->getInstance($request);
        return $module->getContent();
    }

}
