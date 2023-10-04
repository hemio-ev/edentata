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
 * Description of SiteDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class SiteDelete extends Window
{

    public function content($address)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $message = _('Do you really want to delete this webiste?');
        $window  = $this->newDeleteWindow(
            'site_delete'
            , _('Delete Website')
            , $domain
            , $message
            , _('Delete Website')
            , true
        );

        $this->handleSubmit($window->getForm(), $domain, $port);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $domain, $port)
    {
        if ($form->correctSubmitted()) {
            $this->db->siteDelete(['p_domain' => $domain, 'p_port' => $port]);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
