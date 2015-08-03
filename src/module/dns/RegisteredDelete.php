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

/**
 * Description of RegisteredDelete
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class RegisteredDelete extends Window
{

    public function content($domain)
    {
        $message = sprintf(_('Irrevokably delete the   registered domain "%s". '
                .'This operation cannot be made undone.'), $domain);

        $window = $this->newDeleteWindow(
            'registered_delete'
            , _('Delete Registered Domain')
            , $domain
            , $message
            , _('Delete Registered Domain')
            , true);

        $this->handleSubmit($window->getForm(), $domain);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $domain)
    {
        if ($form->correctSubmitted()) {
            $this->db->registeredDelete($domain);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
