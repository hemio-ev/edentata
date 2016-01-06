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
use hemio\form;

/**
 * Description of ServiceDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ServiceDetails extends Window
{

    public function content($registered, $domain)
    {
        $window = $this->newFormWindow('service_details',
                                       _('Service Activation'), $domain,
                                         _('Save'));

        var_dump($domain);

        \hemio\edentata\Utils::idnToAscii($domain);

        $idnaInfo = [];
        $x        = idn_to_ascii($domain,
                                 IDNA_CHECK_BIDI | IDNA_CHECK_CONTEXTJ | IDNA_NONTRANSITIONAL_TO_ASCII
            | IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46, $idnaInfo);
        var_dump($x);
        var_dump($idnaInfo);

        $idnaInfo = [];
        $x        = idn_to_utf8($x,
                                IDNA_CHECK_BIDI | IDNA_CHECK_CONTEXTJ | IDNA_NONTRANSITIONAL_TO_UNICODE,
                                INTL_IDNA_VARIANT_UTS46, $idnaInfo);
        var_dump($x);
        var_dump($idnaInfo);

        $activeServices = [];
        $dnsService     = $this->db->serviceSelect($domain)->fetchAll();
        foreach ($dnsService as $service) {
            $activeServices[$service['service']] = $service['service_entity_name'];
        }

        $services = $this->db->activatableServiceSelect()->fetchAll();

        $delServices = [];
        $insServices = [];
        foreach ($services as $service) {
            $srv = $service['service'];

            $fieldset = new gui\Fieldset(ucwords(str_replace('_', ' ', $srv)));
            $window->getForm()->addChild($fieldset);

            $switch = new gui\FieldSwitch($srv, 'Activate Service');
            $switch->getControlElement()->addCssClass('display_control');
            $fieldset->addChild($switch);

            $names = $this->db->activatableServiceNameSelect($srv);

            $select = new form\FieldSelect('service_entity_name_'.$srv,
                                           _('Host/Server'));
            foreach ($names as $name) {
                $select->addOption($name['service_entity_name']);
            }

            if (isset($activeServices[$srv])) {
                $switch->getControlElement()->setAttribute('checked', true);
                $select->setDefaultValue($activeServices[$srv]);
            }

            $fieldset->addChild($select);

            if ($switch->getValueUser() && !isset($activeServices[$srv])) {
                $insServices[$srv] = $select->getValueUser();
            }

            if (!$switch->getValueUser() && isset($activeServices[$srv])) {
                $delServices[] = $srv;
            }
        }

        $this->handleSubmit($window->getForm(), $insServices, $delServices,
                            $registered, $domain);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , array $insServices
    , array $delServices
    , $registered
    , $domain
    )
    {
        if ($form->correctSubmitted()) {
            $this->db->beginTransaction();

            foreach ($delServices as $service) {
                $delParams = [
                    'p_domain' => $domain,
                    'p_service' => $service
                ];
                $this->db->serviceDelete($delParams);
            }


            foreach ($insServices as $service => $serviceName) {
                $insParams = [
                    'p_registered' => $registered,
                    'p_domain' => $domain,
                    'p_service_entity_name' => $serviceName,
                    'p_service' => $service
                ];
                $this->db->serviceInsert($insParams);
            }
            $this->db->commit();

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
