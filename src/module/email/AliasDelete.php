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

namespace hemio\edentata\module\email;

use hemio\edentata\gui;

/**
 * Description of AliasDelete
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AliasDelete extends Window
{

    public function content($mailbox, $alias)
    {
        $message = _msg(_(
                'Are you sure you want to permanently delete the alias "{alias}"? After'.
                ' deleting the alias you will no longer be reachable via "{alias}".')
            , ['alias' => $alias]
        );

        $window = $this->newDeleteWindow(
            'alias_delete'
            , _('Delete Alias')
            , $alias
            , $message
            , _('Delete Alias')
        );

        $this->handleSubmit($window->getForm(), $mailbox, $alias);

        return $window;
    }

    public function handleSubmit(gui\FormPost $form, $mailbox, $alias)
    {
        if ($form->correctSubmitted()) {
            $params = Db::emailAddressToArgs($alias);
            $params += Db::emailAddressToArgs($mailbox, 'mailbox_');
            $this->db->aliasDelete($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
