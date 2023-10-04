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

/**
 * Description of RedirectionDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class RedirectionDelete extends Window
{

    public function content($redirection)
    {
        $message = _msg(_('Are you sure you want to permanently delete the redirection "{address}"?'
                .'After deleting the redirection you will no longer be reachable via "{address}".')
            , ['address' => $redirection]);

        $window = $this->newDeleteWindow(
            'redirection_delete'
            , _('Delete Redirection')
            , $redirection
            , $message
            , _('Delete Redirection')
        );

        $this->handleSubmit($window->getForm(), $redirection);

        return $window;
    }

    public function handleSubmit(gui\FormPost $form, $redirection)
    {
        if ($form->correctSubmitted()) {
            $params = Db::emailAddressToArgs($redirection);

            $this->db->redirectionDelete($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
