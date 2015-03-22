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

use hemio\form;
use hemio\edentata\gui;

/**
 * Description of SiteHttps
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SiteHttps extends Window
{

    public function content($address)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $window = $this->newFormWindow(
            'site_https'
            , _('Select HTTPS Configuration')
            , $address, _('Change')
        );

        $identifier = new form\FieldSelect('identifier', _('Identifier'));

        foreach ($this->db->httpsSelect($domain, $port)->fetchAll() as $config) {
            $identifier->addOption($config['identifier']);
        }

        $site = $this->db->siteSelectSingle($domain, $port)->fetch();
        $identifier->setDefaultValue($site['https']);

        $window->getForm()->addChild($identifier);

        $this->handleSubmit($domain, $port, $window->getForm());

        return $window;
    }

    protected function handleSubmit($domain, $port, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $this->db->siteUpdate(
                ['p_domain' => $domain, 'p_port' => $port] + $form->getVal(['identifier'])
            );

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
