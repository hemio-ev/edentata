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

namespace hemio\edentata\module\dns;

use hemio\edentata\gui;

/**
 * Description of CustomDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class CustomDelete extends Window
{

    public function content($recordId)
    {
        $data = $this->db->customSelectSingle($recordId)->fetch();

        $window = $this->newDeleteWindow(
            'custom_delete'
            , _('Delete Custom Record')
            , sprintf('%s (%s)', $data['domain'], $data['type'])
            ,
                      sprintf(
                _('Are you sure you want to delete the %s-record for "%s"?')
                , $data['type']
                , $data['domain']
            )
            , _('Delete Record')
        );

        $this->handleSubmit($recordId, $window->getForm());

        return $window;
    }

    protected function handleSubmit($recordId, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $this->db->customDelete($recordId);

            throw new \hemio\edentata\exception\Successful();
        }
    }
}
