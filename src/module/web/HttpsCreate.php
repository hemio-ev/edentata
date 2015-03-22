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

namespace hemio\edentata\module\web;

use hemio\edentata\gui;
use hemio\form;

/**
 * Description of HttpsCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HttpsCreate extends Window
{

    public function content($address)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $window = $this->newFormWindow(
            'https_create', _('New HTTPS Configuration'), $address, _('Create'));

        $existing   = [];
        foreach ($this->db->httpsSelect($domain, $port) as $cert)
            $existing[] = $cert['identifier'];

        $default = (new \DateTime)->format('Y');
        $chr     = 97;
        while (in_array($default, $existing) && $chr < 123)
            $default = (new \DateTime)->format('Y-').chr($chr++);

        $identifier = new form\FieldText('identifier', _('Identifier'));
        $identifier->setDefaultValue($default);

        $window->getForm()->addChild($identifier);

        $this->handleSubmit($domain, $port, $window->getForm());

        return $window;
    }

    public function handleSubmit($domain, $port, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $params = ['p_domain' => $domain, 'p_port' => $port] + $form->getVal(['identifier']);

            $this->db->httpsCreate($params);

            throw new \hemio\edentata\exception\Successful();
        }
    }
}
