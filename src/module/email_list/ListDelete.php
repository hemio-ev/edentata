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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\edentata\module\email;

/**
 * Description of ListDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ListDelete extends Window
{

    public function content($list)
    {
        $message = _msg(_('Are you sure you want to permanently delete the list "{address}"?')
            , ['address' => $list]);

        $window = $this->newDeleteWindow(
            'list_delete'
            , _('Delete List')
            , $list
            , $message
            , _('Delete List')
            , true
        );

        $this->handleSubmit($window->getForm(), $list);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $list)
    {
        if ($form->correctSubmitted()) {
            $params = email\Db::emailAddressToArgs($list);

            $this->db->listDelete($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
