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

namespace hemio\edentata\module\dns;

use hemio\edentata\gui;
use hemio\html;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Domains'), null, false);

        $menu = $window->addHeaderbarMenu();

        $menu->addEntry(
            $this->request->derive('handle_create')
            , _('Create new handle')
        );

        $window->addButtonRight(
            new \hemio\edentata\gui\LinkButton(
            $this->request->derive('registered_create')
            , _('Register Domain')
            )
        );

        $window->addChild($this->domains());
        $window->addChild($this->handles());

        return $window;
    }

    protected function domains()
    {
        $fieldset = new gui\Fieldset(_('Registered Domains'));

        $list = new gui\Listbox();
        foreach ($this->db->registeredSelect() as $domain) {
            $unmanaged = $domain['subservice'] == 'unmanaged' ?
                ' ['._('unmanged').']' : '';
            $list->addLinkEntry(
                $this->request->derive(
                    'registered_details'
                    , $domain['domain']
                )
                ,
                    new \hemio\html\String(
                sprintf('%s (%s)%s', $domain['domain'],
                        $domain['public_suffix'], $unmanaged))
                , $domain['backend_status']
            );
        }

        if (!count($list))
            return new gui\Hint(_('You do not own a registered domain.'));

        $fieldset->addChild($list);

        return $fieldset;
    }

    protected function handles()
    {
        $fieldset = new gui\Fieldset(_('Handles'));

        $list = new gui\Listbox();
        foreach ($this->db->handleSelect(true) as $value) {

            $list->addLinkEntry(
                $this->request->derive('handle_details', $value['alias'])
                ,
                                       new html\String(
                sprintf(
                    '%s %s (%s, ID: %s)'
                    , $value['fname']
                    , $value['lname']
                    , $value['alias']
                    , $value['id']
                ))
                , $value['backend_status']
            );
        }

        if (!count($list))
            return new html\Nothing();

        $fieldset->addChild($list);

        return $fieldset;
    }
}
