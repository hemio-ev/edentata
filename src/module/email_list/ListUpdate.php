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

use hemio\form;
use hemio\edentata\gui;
use hemio\edentata\exception;
use hemio\edentata\module\email;

/**
 * Description of ListUpdate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ListUpdate extends Window
{

    public function content($list)
    {
        $window = $this->newFormWindow(
            'list_update'
            , _('Email List')
            , $list
            , _('Save')
        );

        $admin = new form\FieldEmail('admin', _('List Owner'));
        $window->getForm()->addChild($admin);

        $window->getForm()->addChild(
            new gui\Hint(_('This address is NOT automatically subscribed to the mailing list.')));

        $listData = $this->db->listSelect($list)->fetch();
        if (!$listData) {
            throw new exception\Error(_('List not found.'));
        }
        $window->getForm()->setStoredValues($listData);

        $this->handleSubmit($window->getForm(), $list);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $list)
    {
        if ($form->correctSubmitted()) {
            $params = email\Db::emailAddressToArgs($list);
            $params += $form->getVal(['admin']);

            $this->db->listUpdate($params);

            throw new exception\Successful;
        }
    }
}
