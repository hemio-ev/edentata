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

use Symfony\Component\Yaml;

class Config implements \ArrayAccess
{
    const DEFAULT_CONFIG = __DIR__.'/../../default/config.yaml';

    public static function load($file)
    {
        return new Config([self::DEFAULT_CONFIG, $file]);
    }
    protected $values;

    public function __construct(array $files)
    {
        $this->values = [];
        foreach ($files as $file) {
            if (!file_exists($file))
                throw new \Exception(
                sprintf('Config file "%s" not found.', $file));

            $next = Yaml\Yaml::parse(file_get_contents($file));

            if ($next !== null)
                $this->values = array_merge($this->values, $next);
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset))
            return $this->values[$offset];
        else
            throw new exception\Error(sprintf(_('Unknown setting "%s"'), $offset));
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception('Configs cannot be changed');
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Configs cannot be changed');
    }

    /**
     *
     * @return array
     */
    public function getAllowedModules()
    {
        return array_merge(
            $this['modules_nav']
            , $this['modules_hidden']
            , $this['modules_settings']
        );
    }
}
