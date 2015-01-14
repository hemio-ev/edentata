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
        $nav = new html\Nav();
        $nav
                ->addChild(new html\Header())
                ->addChild(new html\H1())
                ->addChild(new html\String(_('Services')));

        $contentEvents = new ContentEvents($nav);

        $ul = new html\Ul();
        $nav->addChild($ul);
        $ul->addCssClass('listbox');

        foreach ($this->modules as $moduleId) {
            try {
                $module = new LoadModule($moduleId);

                $str = new html\String($module->getName());
                $a = new html\A();
                $url= (new Request())->deriveModule($moduleId)->getUrl();
                $a->setAttribute('href', $url);
                $a->addChild($str);
                $ul->addLine($a);
            } catch (exception\Event $event) {
                $contentEvents->addEvent($event);
            }
        }

        return $contentEvents;
    }

}
