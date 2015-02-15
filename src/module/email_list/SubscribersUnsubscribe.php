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
use hemio\edentata\gui;

/**
 * Description of SubscribersUnsubscribe
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SubscribersUnsubscribe extends \hemio\edentata\Window {

    public function content($list) {
        $msg = _(sprintf(
                        'Do you want to unsubscribe the following addresses ' .
                        'from the list "%s"?'
                        , $list
        ));
        $window = $this->newDeleteWindow(
                'subscribers_unsubscribe'
                , _('Unsubscribe from List')
                , $list
                , $msg
                , _('Unsubscribe')
        );

        $selectedSubscribers = (new ListDetails($this->module))
                ->content($list)
                ->getForm()['selectbox']
                ->getItemCheckboxFields();

        $ul = new html\Ul;
        $inputs = [];

        foreach ($selectedSubscribers as $subscr) {
            $address = $subscr->getValueUser();

            if ($address !== null)
                $ul->addLine(new html\String($address));

            $inputs[] = $window->getForm()->addChild(
                    new \hemio\form\InputHidden($subscr->getName(), $address)
            );
        }

        $this->unsubscribeSubscribers($window->getForm(), $inputs, $list);

        $window['mid']->addChild($ul);

        return $window;
    }

    private function unsubscribeSubscribers(gui\FormPost $form, array $inputs, $list) {
        if ($form->correctSubmitted()) {
            $this->db->beginTransaction();
            foreach ($inputs as $input) {
                $address = $input->getValueUser();
                if ($address) {
                    $this->db->subscriberDelete($list, $address);
                }
            }
            $this->db->commit();
        }
    }

}
