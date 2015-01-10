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

use hemio\html;

/**
 * Description of ContentNav
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ContentNav {

    public $modules = [];

    /**
     * 
     * @param array $modules
     */
    public function __construct(array $modules) {
        $this->modules = $modules;
    }

    /**
     * 
     * @return ContentEvents
     */
    public function getNav() {
        $ul = new html\Ul();
        $contentEvents = new ContentEvents($ul);

        foreach ($this->modules as $moduleName) {
            try {
                $module = new LoadModule($moduleName);
                $ul->addLine(new html\String($module->getName()));
            } catch (exception\Event $event) {
                $contentEvents->addEvent($event);
            }
        }

        return $contentEvents;
    }

}
