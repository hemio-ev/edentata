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
 * @todo Get rid of the worst code in this project
 * @author Michael Herold <quabla@hemio.de>
 */
class Request
{
    const SPECIAL_GET_KEYS = ['role', 'module', 'action', 'subject', 'item'];

    public $role = '';

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

    public function __construct(array $get = [], array $post = [], $uri = null,
                                $baseUrl = '/')
    {
        $this->get  = $get;
        $this->post = $post;

        if ($uri !== null) {
            $i = 0;
            $s = self::SPECIAL_GET_KEYS;

            $uriSplit = explode('/', $uri);
            array_pop($uriSplit);

            $count = count(explode('/', $baseUrl)) - 1;

            $special = array_slice($uriSplit, $count);

            foreach ($special as $val)
                $this->get[$s[$i++]] = $val;
        }

        $this->role    = self::filter('role', $this->get('role'));
        if (!$this->role)
            $this->role    = 'service';
        $this->module  = self::filter('module', $this->get('module'));
        $this->action  = self::filter('action', $this->get('action'));
        $this->subject = self::filter('subject', $this->get('subject'));
        $this->item    = self::filter('item', $this->get('item'));
    }

    public static function filter($key, $value)
    {
        if (strlen($value) > 255) {
            $msg = sprintf(_('Invalid input with key "%s".'), $key);
            new exception\Error($msg);
        }

        return $value;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->get))
            return $this->get[$key];
        else
            return null;
    }

    public function getGet()
    {
        $specialKeys = array_flip(self::SPECIAL_GET_KEYS);
        return array_diff_key($this->get, $specialKeys);
    }

    protected function deriveArray($action = null, $subject = null, $item = null)
    {
        $get = [];

        $preserve = ['deputy'];

        foreach ($preserve as $key)
            if (isset($this->get[$key]))
                $get[$key] = $this->get[$key];

        $get['role']   = $this->role;
        $get['module'] = $this->module;

        if ($action === true)
            $get['action'] = $this->action;
        elseif ($action)
            $get['action'] = $action;

        if ($subject === true)
            $get['subject'] = $this->subject;
        elseif ($subject)
            $get['subject'] = $subject;

        if ($item === true)
            $get['item'] = $this->item;
        elseif ($item)
            $get['item'] = $item;

        return $get;
    }

    public function derive($action = null, $subject = null, $item = null)
    {
        $get = $this->deriveArray($action, $subject, $item);

        return new Request($get);
    }

    public function deriveModule($moduleId)
    {
        $request                = $this->derive();
        $request->get['module'] = $moduleId;
        $request->module        = $moduleId;

        return $request;
    }

    /**
     *
     * @return string
     */
    public function getPathname()
    {
        $activeSpecialKeys       = [];
        foreach (self::SPECIAL_GET_KEYS as $key)
            if ($this->get($key) !== null)
                $activeSpecialKeys[$key] = $this->get($key);

        $path = join('/', $activeSpecialKeys);

        if ($path)
            $path.='/';

        return $path;
    }

    public function getUrl()
    {
        $specialKeys = array_flip(self::SPECIAL_GET_KEYS);


        $exprs   = [];
        foreach (array_diff_key($this->get, $specialKeys) as $key => $value)
            if ($value !== null)
                $exprs[] = $key.'='.$value;

        $search = '?'.implode('&', $exprs);
        if ($search === '?')
            $search = '';

        return $this->getPathname().$search;
    }
}
