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

namespace hemio\edentata;

use hemio\html;

/**
 * Description of ContentNav
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ContentNav
{
    public $modules = [];

    /**
     *
     * @var I10n
     */
    protected $i10n;

    /**
     *
     * @param array $modules
     */
    public function __construct(array $modules, I10n $i10)
    {
        $this->modules = $modules;
        $this->i10n    = $i10;
    }

    /**
     *
     * @return ContentEvents
     */
    public function getNav(Request $request)
    {
        $nav = new html\Nav();
        $nav
            ->addChild(new html\Header())
            ->addChild(new html\H1())
            ->addChild(new html\Str(_('Services')));
        $nav->addCssClass('window');

        $contentEvents = new ContentEvents($nav);

        $ul = new gui\Sidebar();
        $nav->addChild($ul);

        foreach ($this->modules as $moduleId) {
            try {
                $module = new LoadModule($moduleId);
                $url    = $request->deriveRole('service')->deriveModule($moduleId);

                $this->i10n->setDomainModule($module);
                $a = new gui\Link($url, $module->getName());
                $this->i10n->setDomainMain();

                if ($request->module === $moduleId)
                    $a->addCssClass('selected');

                $ul->addLine($a);
            } catch (exception\Event $event) {
                $contentEvents->addEvent($event);
            }
        }

        return $contentEvents;
    }
}
