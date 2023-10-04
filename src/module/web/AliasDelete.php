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

namespace hemio\edentata\module\web;

use hemio\edentata\gui;

/**
 * Description of AliasDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class AliasDelete extends Window
{

    public function content($siteAddr, $alias)
    {
        $sitePort = Utils::getPort($siteAddr);

        $message = _('Are you sure you want to delete this website alias?');

        $window = $this->newDeleteWindow(
            'alias_delete'
            , _('Delete Alias')
            , $alias
            , $message
            , _('Delete Alias')
        );

        $this->handleSubmit($window->getForm(), $alias, $sitePort);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $alias, $sitePort)
    {
        if ($form->correctSubmitted()) {
            $this->db->aliasDelete(['p_domain' => $alias, 'p_site_port' => $sitePort]);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
