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

namespace hemio\edentata\gui;

use hemio\form\Abstract_\TemplateFormField;
use hemio\html;

/**
 * Description of FieldSwitch
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class FieldSwitch extends \hemio\form\FieldCheckbox {

    public function fill() {
        // patch template
        $template = $this->getForm()->getSingleControlTemplate();
        $templateSwitch = $this->patchTemplateForSwitch(clone $template);
        $this->addInheritableAppendage(
                FormPost::FORM_FIELD_TEMPLATE . '_CHECKBOX', $templateSwitch
        );
        parent::fill();
    }

    protected function patchTemplateForSwitch($templateSwitch) {
        $templateSwitch->setPostInitHook(function (TemplateFormField $template) {
            $template->getControl()->addCssClass('switch');
            $template->addChildBeginning($template->getControl());
            unset($template['P']['CONTROL']);

            $labelText = $template['P']['LABEL'][0];
            unset($template['P']['LABEL'][0]);
            $spanLabel = new html\Span();
            $spanLabel[] = $labelText;

            $template['P']['LABEL'][] = $spanLabel;
            $template['P']['LABEL']['SWITCH'] = new html\Span();
            $template['P']['LABEL']['SWITCH']->addCssClass('switch');
        });

        return $templateSwitch;
    }

}
