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
 * Description of StatusList
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class StatusList extends html\Ul
{

    public function __construct()
    {
        $this->addCssClass('status');
    }

    /**
     *
     * @param string $text
     * @param boolean $status
     * @return html\Li
     */
    public function addEntry($text, $status)
    {
        $prefix = [
            'ok' => _('OK'),
            'error' => _('Error'),
            'warning' => _('Warning')
        ];


        $li = $this->addLine();
        $li->addChild(new html\Str('['.$prefix[$status].'] '.$text));

        $li->addCssClass($status);

        return $li;
    }

    /**
     *
     * @param array $data
     * @return html\Li
     */
    public function addEntryArray(array $data)
    {
        return $this->addEntry($data['text'], $data['status']);
    }
}
