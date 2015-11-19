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

use hemio\html;
use hemio\form;

/**
 * Description of FieldRadio
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class FieldRadioList extends form\Container
{
    protected $name;
    protected $template;
    protected $title;

    public function __construct($name, $title = null)
    {
        $this->name  = $name;
        $this->title = $title;

        $this['controls'] = new form\Container();

        $this['ul'] = new html\Ul;
        $this['ul']->addCssClass('listbox');
        $this['ul']->addCssClass('radio');

        $this['style'] = new html\Style();
        $this['style']->setAttribute('scoped', true);

        $this->template = new form\template\FormPlainControl;
        $this->template->setPostInitHook(function ($template) {
            $id              = $template->field->getHtmlId();
            $template->getControl()->addCssClass('list');
            $template->field->getInheritableAppendage('label')
                ->setAttribute('for', $id);
            $this['style'][] = new html\Str(
                'input:not(:checked)[id='.$id.'] ~ * *[for='.$id.'] { '.
                'color: inherit; background-color: inherit; }'
            );
        });
    }

    public function addOption($value, $content = null)
    {
        if ($content === null)
            $content = $value;

        $title = $content;
        if ($this->title !== null)
            $title = $this->title;

        $radio = new form\FieldRadio($this->name, $title, $value);
        $radio->addInheritableAppendage(
            FormPost::FORM_FIELD_TEMPLATE.'_RADIO', $this->template
        );

        $this['controls'][] = $radio;

        $li = new html\Li;
        $li->addCssClass('listbox_link');

        $label   = new html\Label;
        $label[] = new html\Str($content);
        $label->addCssClass('listbox_content');

        $radio->addInheritableAppendage('label', $label);
        $li[] = $label;

        $this['ul']->addChild($li);

        return $radio;
    }

    public function getValueUser()
    {
        return $this['controls'][0]->getValueUser();
    }
}
