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
use hemio\edentata\exception;

/**
 * Description of HttpsCert
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HttpsCert extends Window
{

    public function content($domain, $identifier)
    {
        $window = $this->newFormWindow(
            'https_cert'
            , _('Supply Certificate')
            , sprintf('%s (%s)', $domain, $identifier)
            , _('Update')
        );

        $cert = new form\FieldTextarea('x509_certificate', _('Certificate'));


        $window->getForm()->addChild($cert);

        $this->handleSubmit($window->getForm(), $cert, $domain, $identifier);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form,
                                    form\FieldTextarea $cert, $domain,
                                    $identifier)
    {
        if ($form->correctSubmitted()) {
            $certs = Cert::extract($cert->getValueUser());
            if (count($certs) !== 1)
                throw new exception\Error('Expecting exactly one cert');

            $crt = array_pop($certs);

            $params = [
                'p_domain' => $domain,
                'p_identifier' => $identifier,
                'p_authority_key_identifier' => $crt->authorityKeyIdentifier(),
                'p_x509_certificate' => $crt->raw()
            ];

            $this->db->httpsUpdate($params);
        }
    }
}