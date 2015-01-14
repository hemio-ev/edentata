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

/**
 * Description of Listbox
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Listbox extends html\Ul {

    public function __construct() {
        $this->addCssClass('listbox');
        $this->addCssClass('scroll');
    }
    
    /**
     * 
     * @param mixed $url
     * @param string $text
     * @return html\Li
     */
    public function addLink($url, $text) {
        $a = new html\A();
        $a->setAttribute('href', $url);
        $a->addChild(new html\String($text));
                
        return $this->addLine($a);
    }

}
