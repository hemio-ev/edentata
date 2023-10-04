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

namespace hemio\edentata\module\server_access;

use hemio\edentata\gui;
use hemio\form;
use hemio\html;

/**
 * Description of Overview
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Server Access'), null, false);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->request->derive('service')
            , _('Create User')
            )
            , true
        );

        $window->addChild($this->users());

        return $window;
    }

    protected function users()
    {
        $users = $this->db->userSelect()->fetchAll();

        if (empty($users)) {
            return new gui\Hint(
                _('You do not own a server access.')
            );
        } else {
            $list = new gui\Listbox();
            foreach ($users as $user) {
                $container = new form\Container();

                $container->addChild(new html\Str($user['user']));

                $ul = new html\Ul();
                $container->addChild($ul);

                $ul->addLine(new html\Str(
                    sprintf(_('Host: %s'), $user['service_entity_name'])));

                $ul->addLine(new html\Str(
                    sprintf(
                        _('Protocol: %s'), strtoupper($user['subservice'])
                    )
                ));

                $list->addLinkEntry(
                    $this->request->derive(
                        'details'
                        , $user['user']
                        , $user['service_entity_name']
                    )
                    , $container
                    , $user['backend_status']
                );
            }
            return $list;
        }
    }
}
