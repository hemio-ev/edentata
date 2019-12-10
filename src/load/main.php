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

namespace hemio\edentata;

use hemio\html;
use hemio\form;

require 'src/load/functions.php';

openlog('edentata', LOG_ODELAY, LOG_USER);
ini_set('log_errors', true);
ini_set('error_log', 'syslog');

set_error_handler(
        function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    $id = uniqid();
    syslog(LOG_ERR, sprintf('PHP error[%s] message: %s', $id, $errstr));
    syslog(LOG_ERR, sprintf('PHP error[%s] details: (%s) %s:%s', $id, $errno, $errfile, $errline));

    echo sprintf(
            _(
                    'An error has occured. We are sorry. '
                    . 'You should contact the support and reference to error id "%s". '
                    . 'You can use the back button of your browser and try again.'
            ), $id
    );

    trigger_error(
            sprintf(
                    'Made non fatal error fatal. (%s:%s %s)'
                    , $errfile
                    , $errline
                    , $errstr
            ), E_USER_ERROR
    );

    exit(1);
}
);

set_exception_handler(
        function ($e) {
    $id = uniqid();

    syslog(LOG_ERR, sprintf('PHP exception[%s] class: %s', $id, get_class($e)));
    syslog(LOG_ERR, sprintf('PHP exception[%s] message: %s', $id, $e->getMessage()));

    echo sprintf(
            _(
                    'An error has occured. We are sorry. '
                    . 'You should contact the support and reference to error id "%s". '
                    . 'You can use the back button of your browser and try again.'
            ), $id
    );

    throw $e;
}
);

System::init();

# external data
if (isset($_SERVER['EDENTATA_CONFIG_FILE']))
    $config = Config::load($_SERVER['EDENTATA_CONFIG_FILE']);
else
    $config = Config::load('/etc/edentata/config.yaml');

$loader->addPsr4('hemio\\edentata\\module\\', $config['module_load_dirs']);

$request = new Request($_GET, Utils::getPost(), urldecode($_SERVER['REQUEST_URI']), $config['base_url']);

$modulesNavi = $config['modules_nav'];
$modulesAllowed = $config->getAllowedModules();

I10n::$supportedLocales = $config['locales'];

$i10n = new I10n();

$activeModuleName = $request->module;
if (!$activeModuleName)
    $activeModuleName = 'home';

try {
# doc
    $title = new html\Str('Edentata');
    $doc = new form\Document($title);
    $doc->getHtml()->getHead()->addCssFile('static/design/style.css');
    $doc->getHtml()->setAttribute('lang', $i10n->getLang());
    $doc->getHtml()->getHead()->setBaseUrl($config['base_url']);

    $body = $doc->getHtml()->getBody();

    $header = new gui\TopBar();
    $body['header'] = $header;

    $body['main'] = new html\Div();
    $body['main']->addCssClass('main');

    $mainNav = new html\Div();
    $body['main']['main_nav'] = $mainNav;
    $mainNav->setId('main_nav');

    $mainContent = new html\Div();
    $body['main']['main_content'] = $mainContent;
    $mainContent->setId('main_content');
    $mainContent['messages'] = new \hemio\form\Container;

# db connect
    $pdo = new sql\Connection($config['database_dsn']);

    $pdo->addExceptionMapper(
            new sql\ExceptionMapping($request->derive())
    );

    $httpAuth = isset($_GET['auth']) && $_GET['auth'] === 'http';
    $authMethod = isset($_GET['auth']) ? $_GET['auth'] : null;
    $httpAuthSupplied = isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']);

# db auth
    if ($authMethod === 'http_logout') {
        header('WWW-Authenticate: Basic realm="Edentata"');
        header('HTTP/1.1 401 Unauthorized');
        throw new exception\Successful(_('Logout successful'));
    }

    if ($authMethod === 'logout') {
        require 'src/load/login.php';
        exit(0);
    }

    if ($authMethod === 'http' && !$httpAuthSupplied) {
        header('WWW-Authenticate: Basic realm="Edentata"');
        header('HTTP/1.1 401 Unauthorized');
        throw new exception\Error(_('Login failed'));
    }

    if (!$httpAuthSupplied) {
        require 'src/load/login.php';
        exit(0);
    }

    $usrData = [
        'p_login' => $_SERVER['PHP_AUTH_USER'],
        'p_password' => $_SERVER['PHP_AUTH_PW']
    ];

    try {
        $qryAuth = new sql\QuerySelectFunction($pdo, 'user.ins_login', $usrData);
        $loginData = $qryAuth->execute()->fetch();
    } catch (\Exception $e) {
        if ($httpAuth) {
            header('WWW-Authenticate: Basic realm="Edentata"');
            header('HTTP/1.1 401 Unauthorized');
            throw new exception\Error(_('Login failed'));
        } else {
            require 'src/load/login.php';
            exit(0);
        }
    }

    $headerNav = $header->getNavUl();

    $userStr = new html\Str(_('User') . ': ' . $loginData['user']);

    $headerNav->addLine(new gui\Link(
            $request->deriveRole('settings')->deriveModule($config['modules_settings'][0])
            , $userStr
    ));

    $userModule = (new LoadModule('user', $pdo, $i10n))->getInstance($request);
    $users = $userModule->db->selectDeputy()->fetchAll();

    if (!empty($users)) {
        $aSettings = new html\Button;
        $aSettings->addCssClass('popover');
        if (!$request->get('deputy'))
            $aSettings[] = new html\Str(_('Act as deputy'));
        else
            $aSettings[] = new html\Str(_('In Place of') .': ' . $request->get('deputy'));

        $aSettings->setAttribute('href', $request->getUrl() . '#');
        //$header[]    = $aSettings;

        $ul = $headerNav->addSubUl($aSettings);
        $ul->addCssClass('popover');

        $req = $request->deriveModule($request->module);

        $req->get['deputy'] = null;

        if ($request->get('deputy')) {
            $ul->addLine(new gui\Link($req, _('Stop acting as deputy')));
            $ul->addLine(new html\Hr());
        }

        foreach ($users as $represented) {
            $req->get['deputy'] = $represented['represented'];
            $ul->addLine(new gui\Link($req, $represented['represented']));
        }



        //$header[] = $ul;
    }

    if ($config->enabled('support_url')) {
        $aSupport = new html\A;
        $aSupport->setAttribute('href', $config['support_url']);
        $aSupport[] = new html\Str(_('Support'));
        $headerNav->addLine($aSupport);
    }

    $aLogout = new html\A;
    $aLogout->setAttribute('href', '?auth=logout');
    $aLogout[] = new html\Str(_('Logout'));
    $headerNav->addLine($aLogout);

    if ($request->get('deputy'))
        $userModule->db->actAsDeputy($request->get('deputy'));


# navi
    $nav = (new ContentNav($modulesNavi, $i10n))->getNav($request);
    while ($nav->unhandledEvents()) {
        try {
            $nav->handleEvent();
        } catch (exception\Printable $event) {
            $mainContent['messages'][] = new gui\Message($event);
        }
    }
    $mainNav[] = $nav->getContent();

# modules
    if ($request->role === 'settings') {

        $title->setValue(_msg($config['title'], ['module' => _('Settings')]));

        $nav = new gui\Window(_('Settings'));
        $list = new gui\Sidebar();
        $nav[] = $list;
        if (count($config['modules_settings']) > 1)
            $mainContent->addChild($nav);

        foreach ($config['modules_settings'] as $moduleId) {
            $loadedModule = new LoadModule($moduleId, $pdo, $i10n);

            $a = $list->addLinkEntry(
                    $request->deriveModule($moduleId)
                    , new html\Str($loadedModule->getName())
            );

            if ($moduleId === $request->module)
                $a->addCssClass('selected');
        }

        $loadedModule = new LoadModule($activeModuleName, $pdo, $i10n);

        $content = $loadedModule->getContent($request, $i10n);
        $mainContent->addChild($content);
    } else {
        if (!in_array($activeModuleName, $modulesAllowed))
            throw new exception\Error(_('Tried to access unknown or disabled module'));

        $loadedModule = new LoadModule($activeModuleName, $pdo, $i10n);

        $title->setValue(_msg($config['title'], ['module' => $loadedModule->getName()]));

# generate content
        $content = $loadedModule->getContent($request, $i10n);
        $mainContent->addChild($content);
    }
} catch (\PDOException $e) {
    $mainContent['messages']
            ->addChild(new gui\Message(new exception\Error('*DB operation failed* ' . $e->getMessage())));
} catch (exception\Printable $e) {
    $mainContent['messages']->addChild(new gui\Message($e));
}

$body['footer'] = new ContentFooter($config);

echo $doc->__toString();
//echo System::reportString();
