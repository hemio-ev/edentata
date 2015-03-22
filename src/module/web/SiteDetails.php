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
use hemio\html;
use hemio\form;

/**
 * Description of SiteCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SiteDetails extends Window
{

    public function content($address)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $container = new form\Container();

        $window = $this->newWindow(_('Website'), $address);

        $data = $this->db->siteSelectSingle($domain, $port)->fetch();

        $details = new gui\Fieldset(_('Site Details'));

        $details->addChild(new gui\Output(_('Host'), $data['service_name']));
        $user = $details->addChild(new gui\Output(_('User'), ''));

        $serverAccessRequest = $this->request
            ->deriveModule('server_access')
            ->derive('details', $data['user'], $data['service_name']);


        $user['p']['output'][] = new gui\Link($serverAccessRequest,
                                              $data['user']);


        if ($data['https'] === null)
            $details->addChild(new gui\Output(_('HTTPS'), _('disabled')));
        else
            $details->addChild(new gui\Output(_('HTTPS'), _('enabled')));

        if (!Utils::defaultPort($port, $data['https']))
            $details->addChild(new gui\Output(_('Port'), $data['port']));

        if ($data['https'] === null)
            $windowHttps = new html\Nothing;
        else
            $windowHttps = $this->httpsWindow($data);

        $window->addChild($details);
        $window->addChild($this->aliases($domain, $port));
        $window->addChild($this->actions($address));

        $container->addChild($window);
        $container->addChild($windowHttps);

        return $container;
    }

    protected function actions($address)
    {
        $selecting = new gui\Selecting(_('Possible Actions'));

        $selecting->addLink(
            $this->request->derive('alias_create', $address), _('Create alias'));

        $selecting->addLink(
            $this->request->derive('site_delete', $address)
            , _('Delete entire site')
        );

        return $selecting;
    }

    protected function aliases($domain, $port)
    {
        $fieldset = new gui\Fieldset(_('Aliases'));

        $listbox = new gui\Listbox();

        $aliases = $this->db->aliasSelect($domain, $port)->fetchAll();
        if (empty($aliases))
            return new html\Nothing ();

        foreach ($aliases as $alias) {
            $listbox->addEntry(
                new html\String($alias['domain'])
                , $alias['backend_status']
                ,
                                new gui\LinkButton(
                $this->request->derive('alias_delete', $domain.':'.$port,
                                       $alias['domain'])
                , _('Delete')
            ));
        }

        $fieldset->addChild($listbox);

        return $fieldset;
    }

    protected function httpsWindow(array $site)
    {
        $domain = $site['domain'];
        $port   = $site['port'];

        $window = $this->newWindow(_('HTTPS'), null, false);

        $httpsDetails  = new HttpsDetails($this->module);
        $httpsCertInfo = $httpsDetails->https($domain, $port, $site['https']);

        $window->addChild($this->httpsActiveCert($site));
        $window->addChild($httpsCertInfo);
        $window->addChild($this->httpsAvailableCerts($domain, $port));

        return $window;
    }

    protected function httpsActiveCert(array $site)
    {
        $fieldset = new gui\Fieldset(_('Active Certificate'));

        $list = new gui\Listbox();
        $fieldset->addChild($list);

        $url = $this->request->derive('site_https', true);
        $list->addEntry(new html\String($site['https']), null,
                                        new gui\LinkButton($url, _('Change')));

        return $fieldset;
    }

    protected function httpsAvailableCerts($domain, $port)
    {
        $address  = $domain.':'.$port;
        $fieldset = new gui\Fieldset(_('Available HTTPS Configurations'));

        $selecting = new gui\Selecting();

        $selecting->addLink(
            $this->request->derive('https_create', $address)
            , _('New configuration (new SSL key an certificate)')
        );

        $certs = $this->db->httpsSelect($domain, $port)->fetchAll();

        $list = new gui\Listbox();
        foreach ($certs as $cert) {
            $url = $this->request->derive('https_details', $address,
                                          $cert['identifier']);

            $container   = new form\Container;
            $container[] = new html\String($cert['identifier']);
            $container[] = Utils::certSummary($domain, $port,
                                              $cert['identifier'], $this->db);

            $list->addLinkEntry($url, $container, $cert['backend_status']);
        }

        $fieldset->addChild($selecting);
        $fieldset->addChild($list);

        return $fieldset;
    }
    /*

      protected function tryFindingTrustChain($site)
      {
      $cert = $this->db->httpsSelect(
      $site['domain'], $site['port'], $site['https'])->fetch();

      $certData = new Cert($cert['x509_certificate']);

      $newChain = $certData->suggestChain($this->db);

      if (!$certData->trusted($newChain))
      return;

      $this->db->beginTransaction();
      $this->db->intermediateChainDelete([
      'p_domain' => $site['domain'],
      'p_port' => $site['port'],
      'p_identifier' => $site['https']
      ]);
      $i = 0;
      foreach ($newChain as $chainCert) {
      $params = [
      'p_domain' => $site['domain'],
      'p_port' => $site['port'],
      'p_identifier' => $site['https'],
      'p_order' => $i++,
      'p_subject_key_identifier' => $chainCert->subjectKeyIdentifier()
      ];
      $this->db->intermediateChainInsert($params);
      }

      $this->db->commit();

      $e         = new \hemio\edentata\exception\Successful(
      _('The system has generated a valid chain of trust for your certificate.'
      .' It is now ready for use with your website.'));
      $e->backTo = $this->request->derive(true, true, true);

      throw $e;
      }

      protected function https(array $site)
      {
      $https = new gui\Fieldset(_('HTTPS Status'));

      $identifier = $site['https'];

      $cert = $this->db->httpsSelect(
      $site['domain'], $site['port'], $identifier)->fetch();

      $status     = Utils::certStatus($site, $this->db);
      $statusList = new gui\StatusList();
      $selecting  = new gui\Selecting();

      if ($cert['x509_request'] !== null)
      $statusList->addEntry(_('Certificate request available'), 'ok');

      $suggestIntermediate = false;

      switch ($status['code']) {
      case 'no_csr':
      $statusList->addEntryArray($status);
      break;

      case 'no_cert':
      $statusList->addEntryArray($status);

      $link = $selecting->addLink($this->request->derive(
      'https_cert', $site['domain'].':'.$site['port'],
      $site['https']),
      _('Request certificate / Supply HTTPS certificate'));
      $link->setSuggested();

      break;

      case 'not_trusted':
      $this->tryFindingTrustChain($site);
      $suggestIntermediate = true;
      $statusList->addEntryArray($status);
      break;

      case 'expired':
      $statusList->addEntryArray($status);
      break;

      case 'expiring':
      $statusList->addEntryArray($status);
      break;

      case 'ok':
      $statusList->addEntryArray($status);
      break;

      default:
      $statusList->addEntry('Unknown status!', 'error');
      }

      if ($status['cert']) {
      $diff = Utils::certUncoveredNames($site, $this->db);
      if (!empty($diff))
      $statusList->addEntry(
      sprintf(
      _('The following names are not covered by the certificate: %s.')
      , implode(', ', $diff)
      )
      , 'error'
      );
      }


      $https->addChild($statusList);

      $selecting->addLink($this->request->derive(
      'intermediate_create', $site['domain'].':'.$site['port'],
      $site['https']), _('Add intermediate certificates'))
      ->setSuggested($suggestIntermediate);

      $https->addChild($selecting);

      return $https;
      }
     */
}
