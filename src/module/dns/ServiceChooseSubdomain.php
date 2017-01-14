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

use hemio\form;

/**
 * Description of ServiceCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ServiceChooseSubdomain extends Window
{

    public function content($registered)
    {
        $window = $this->newFormWindow(
                'service_create'
                , _('Activate Service for Subdomain')
                , $registered
                , _('Continue …')
        );

        $domain = new form\FieldText('domain', _('Subdomain'));
        $domain->getControlElement()->setAttribute('placeholder',
                                                   'subdomain.' . $registered);
        $domain->setRequired();

        $window->getForm()->addChild($domain);

        if ($window->getForm()->correctSubmitted()) {
            $e         = new \hemio\edentata\exception\Successful;
            $e->backTo = $this->request->derive(
                'service_details'
                , $registered
                , $domain->getValueUser()
            );
            throw $e;
        }

        return $window;
    }
}
