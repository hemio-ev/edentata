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

namespace hemio\edentata\module\email;

use hemio\edentata\gui;
use hemio\edentata\exception;

/**
 * Description of MailboxDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class MailboxDelete extends Window
{

    public function content($address)
    {
        $msg = _msg(
            _(
                'Are you sure you want to permanently delete the'
                .' mailbox "{address}"? If you delete a mailbox, all stored '
                .' emails will be permanently lost and you will no'
                .' longer be reachable via "{address}".'
            ), ['address' => $address]);

        $window = $this->newDeleteWindow(
            'mailbox_delete'
            , _('Delete Mailbox')
            , $address
            , $msg
            , _('Delete Mailbox')
            , true
        );

        $this->handleSubmit($window->getForm(), $address);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $address)
    {

        if ($form->submitted()) {
            if ($form->dataValid()) {
                $args = Db::emailAddressToArgs($address);

                $this->db->mailboxDelete($args);

                throw new exception\Successful();
            }
        }
    }
}
