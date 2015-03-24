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

namespace hemio\edentata\module\email_list;

use hemio\html;
use hemio\form;
use hemio\edentata\gui;
use hemio\edentata\module\email;

/**
 * Description of SubscribersUnsubscribe
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SubscribersMove extends Window
{

    public function content($listSource)
    {
        $window = $this->newFormWindow(
            'subscribers_move'
            , _('Move Email List Subscribers')
            , $listSource
            , _('Move')
        );

        $selectedSubscribers = (new ListDetails($this->module))
            ->content($listSource)
            ->getForm()
            ->getInheritableAppendage('selected_subscribers')
            ->getItemCheckboxFields();

        $ul     = new html\Ul;
        $inputs = [];

        foreach ($selectedSubscribers as $subscr) {
            $address = $subscr->getValueUser();

            if ($address !== null)
                $ul->addLine(new html\String($address));

            $inputs[] = $window->getForm()->addChild(
                new \hemio\form\InputHidden($subscr->getName(), $address)
            );
        }

        $listTarget = new form\FieldSelect('list', _('Target Email List'));
        $listTarget->setRequired();
        $listTarget->addOption('');

        foreach ($this->db->listSelect() as $listData)
            if (email\Utils::toAddr($listData) !== $listSource)
                $listTarget->addOption(email\Utils::toAddr($listData));

        $window->getForm()->addChild(new gui\Output(_('Source Email List'),
                                                      $listSource));
        $window->getForm()->addChild($listTarget);

        $window->getForm()
            ->addChild(new gui\Fieldset(_('Subscribers')))
            ->addChild($ul);

        $this->handleSubmit($window->getForm(), $inputs, $listSource,
                            $listTarget->getValueUser());

        return $window;
    }

    private function handleSubmit(gui\FormPost $form, array $inputs, $listOld,
                                  $listTarget)
    {
        if ($form->correctSubmitted()) {
            $this->db->beginTransaction();

            foreach ($inputs as $input) {
                $address = $input->getValueUser();
                if ($address) {
                    $this->db->subscriberDelete($listOld, $address);
                    $params = ['p_address' => $address] + email\Db::emailAddressToArgs($listTarget,
                                                                                       'list_');
                    $this->db->subscriberCreate($params);
                }
            }

            $this->db->commit();

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
