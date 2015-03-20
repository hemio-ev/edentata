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
class IntermediateCreate extends Window
{

    public function content($domain, $identifier)
    {
        $window = $this->newFormWindow(
            'intermediate_add'
            , _('Intermediate Certificate')
            , null
            , _('Add')
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

            $this->db->beginTransaction();
            foreach ($certs as $crt) {
                $params = [
                    'p_authority_key_identifier' => $crt->authorityKeyIdentifier(),
                    'p_subject_key_identifier' => $crt->subjectKeyIdentifier(),
                    'p_x509_certificate' => $crt->raw()
                ];

                $this->db->intermediateCertCreate($params);
            }
            $this->db->commit();

            $e = new exception\Successful(sprintf(
                    ngettext(
                        'Added one intermediate certificate.'
                        ,
                        'Added %s intermediate certificates.'
                        , count($certs)
                    )
                    , count($certs))
            );

            $e->backTo = $this->request->derive('site_details', $domain);

            throw $e;
        }
    }
}
