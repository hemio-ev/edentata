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

namespace hemio\edentata\gui;

use hemio\html;
use hemio\html\Interface_\HtmlCode;

/**
 *
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Sidebar extends html\Ul
{

    public function __construct()
    {
        $this->addCssClass('sidebar');
    }

    /**
     *
     * @param \hemio\edentata\Request $url
     * @param HtmlCode $content
     * @param type $backendStatus
     * @return \hemio\edentata\gui\A
     */
    public function addLinkEntry(
    \hemio\edentata\Request $url
    , HtmlCode $content
    )
    {

        $a = new html\A;
        $a->setAttribute('href', $url->getUrl());

        $a->addChild(new html\Span)->addChild($content);

        $this->addChild(new html\Li)->addChild($a);

        return $a;
    }

    /**
     *
     * @param HtmlCode $content
     * @param type $backendStatus
     * @param HtmlCode $buttons
     * @return \hemio\html\Li
     */
    public function addEntry(
    HtmlCode $content
    , $backendStatus = null
    , HtmlCode $buttons = null
    )
    {

        $li = new html\Li();
        $this->addChild($li);

        $li->addChild(new html\Span)->addChild($content);
        $li->addChild(new Progress($backendStatus));
        if ($buttons !== null)
            $li->addChild($buttons);

        $li->addCssClass('listbox_content');

        return $li;
    }
}
