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

namespace hemio\edentata\module\login_http;

use hemio\form;
use hemio\html;
use hemio\edentata\gui;
use hemio\edentata\exception;

class Login extends Window
{

    public function content()
    {
        $container = new form\Container();

        $container->addChild($this->logout());
        $container->addChild($this->nojsLogin());
        $container->addChild($this->jsLogin());

        return $container;
    }

    public static function loginTitle()
    {
        global $config;

        return _msg($config['title'], ['module' => _('User Login')]);
    }

    protected function nojsLogin()
    {
        $noscript = new html\Noscript();
        $window   = $this->newWindow(self::loginTitle(), null, false);
        $window->addCssClass('login_window');

        $selecting = new gui\Selecting();

        $request              = clone $this->request;
        $request->get['role'] = '';

        if ($this->request->get('auth') === 'logout') {
            $request->get['auth'] = 'http_logout';
            $selecting->addLink($request, _('Perform Logout'))
                ->setSuggested();
        } else {
            $request->get['auth'] = 'http';
            $selecting->addLink($request, _('Open Login Prompt'));
        }

        $noscript
            ->addChild($window)
            ->addChild($selecting);

        return $noscript;
    }

    protected function jsLogin()
    {
        $window = $this->newFormWindow(
            'login'
            , self::loginTitle()
            , null
            , _('Login')
            , false
        );

        $window->setAttribute('data-js-hidden', true);
        $window->setId('js_window');
        $window->addCssClass('login_window');

        $user = new form\FieldText('username', _('User Account'));
        $user->setPlaceholder(_('Username or Email Address'));
        $user->setRequired();

        $password = new form\FieldPassword('password', _('Password'));
        $password->setRequired();

        $window->getForm()->addChild($user);
        $window->getForm()->addChild($password);

        $window->getForm()->addChild($this->error());

        return $window;
    }

    protected function error()
    {
        $event   = new exception\Error(_('Login failed: Invalid username or password. Please try again.'));
        $message = new gui\Explanation($event);
        $message['div']->setAttribute('data-js-hidden', true);
        $message['div']->setId('message_login_failed');
        $message['div']->addCssClass('login_window');

        return $message;
    }

    protected function logout()
    {
        $event   = new exception\Successful(_('Logout successful.'));
        $message = new gui\Message($event);
        $message['article']->setAttribute('data-js-hidden', true);
        $message['article']->setId('message_logout_successful');
        $message['article']->addCssClass('login_window');

        return $message;
    }
}
