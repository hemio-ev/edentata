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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\html\String;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Mailing Lists'), null, false);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->module->request->derive('list_create')
            , _('New List')
            )
            , true);


        $window->addChild($this->lists());

        return $window;
    }

    protected function lists()
    {
        $lists = $this->db->listSelect()->fetchAll();

        if (!count($lists)) {
            return new gui\Hint(_('You do not own a mailing lists.'));
        } else {
            $listbox = new gui\Listbox;
            foreach ($lists as $list) {
                $addr = $list['localpart'].'@'.$list['domain'];
                $listbox->addLinkEntry(
                    $this->module->request->derive('list_details', $addr)
                    , new String($addr)
                    , $list['backend_status']
                );
            }

            return $listbox;
        }
    }
}
