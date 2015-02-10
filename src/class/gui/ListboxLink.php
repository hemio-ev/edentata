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
 * Description of ListboxLink
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ListboxLink extends html\Li {

    public function __construct($text, $url) {
        $this['a'] = new html\A();
        $this['a']->setAttribute('href', $url->getUrl());
        $this['a']['text'] = new \hemio\form\Container;
        $this['a']['text'][] = $text;
    }

    public function setPending($status) {
        if (isset($this['a']['div'])) {
            $elem = $this['a']['div'];
        } else {
            $elem = $this['a'];
        }

        if ($status !== null) {

            switch ($status) {
                case 'del':
                    $msg = _('deletion pending');
                    break;

                case 'upd':
                    $msg = _('changes pending');
                    break;

                case 'ins':
                    $msg = _('setup pending');
                    break;

                default:
                    $msg = _('unknown status');
            }

            $elem['text']['pending'] = new \hemio\html\Span();
            $elem['text']['pending']->addCssClass('progress');
            $elem['text']['pending'][] = new html\String($msg);
        }
    }

    /**
     * 
     * @return \hemio\html\Ul
     */
    public function addList() {

        $div = new html\Div;
        $div['text'] = $this['a']['text'];
        unset($this['a']['text']);

        $this['a']['div'] = $div;
        $this['a']['div']['ul'] = new html\Ul;

        return $this['a']['div']['ul'];
    }

}
