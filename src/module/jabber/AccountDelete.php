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

namespace hemio\edentata\module\jabber;

use hemio\edentata\gui;

/**
 * Description of AccountDelete
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AccountDelete extends Window {

    public function content($account) {
        $message = _('Do you really want to delete this jabber account?');

        $window = $this->newDeleteWindow(
                'account_delete'
                , _('Delete Account')
                , $account
                , $message
                , _('Delete Account')
        );

        $this->handleSubmit($window->getForm(), $account);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $account) {
        if ($form->correctSubmitted()) {
            $params = Db::accountToArgs($account);

            $this->db->accountDelete($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }

}
