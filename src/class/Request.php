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
 * Description of Request
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Request {

    public function __construct(array $get = []) {
        $this->get = $get;

        $this->module = self::filter('module', $this->get('module'));
        $this->action = self::filter('action', $this->get('action'));
        $this->subject = self::filter('subject', $this->get('subject'));
        $this->item = self::filter('item', $this->get('item'));
    }

    public static function filter($key, $value) {
        if (strlen($value) > 50) {
            $msg = sprintf(_('Invalid input with key "%s".'), $key);
            new exception\Error($msg);
        }

        return $value;
    }

    public function get($key) {
        if (array_key_exists($key, $this->get))
            return $this->get[$key];
        else
            return '';
    }

    protected function deriveArray($action = null, $subject = null, $item = null) {
        $get = [];

        $get['module'] = $this->module;
        if ($action)
            $get['action'] = $action;
        if ($subject)
            $get['subject'] = $subject;
        if ($item)
            $get['item'] = $item;

        return $get;
    }

    public function derive($action = null, $subject = null, $item = null) {
        $get = $this->deriveArray($action, $subject, $item);

        return new Request($get);
    }

    public function deriveModule($moduleId) {
        $get = ['module' => $moduleId];

        return new Request($get);
    }

    public function getUrl() {
        $exprs = [];
        foreach ($this->get as $key => $value)
            $exprs[] = $key . '=' . $value;

        return '?' . implode('&', $exprs);
    }

    /**
     * 
     * @var string 
     */
    public $module = '';

    /**
     *
     * @var string
     */
    public $action = '';

    /**
     *
     * @var string
     */
    public $subject = '';

    /**
     *
     * @var string
     */
    public $item = '';

    /**
     *
     * @var array
     */
    public $get = [];

    /**
     *
     * @var array
     */
    public $post = [];

}
