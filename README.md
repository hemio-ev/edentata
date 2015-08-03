Edentata
========

A toothless frontend for the hosting-service management carnivora.

### Available on Packagist
[![Latest Stable Version](https://poser.pugx.org/hemio/edentata/version)](https://packagist.org/packages/hemio/edentata)
[![Total Downloads](https://poser.pugx.org/hemio/edentata/downloads)](https://packagist.org/packages/hemio/edentata)
[![Reference Status](https://www.versioneye.com/php/hemio:edentata/reference_badge.svg?style=flat)](https://www.versioneye.com/php/hemio:edentata/references)

### Tests and Code Quality
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/qua-bla/edentata/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/qua-bla/edentata/?branch=master)
[![Build Status](https://travis-ci.org/qua-bla/edentata.svg?branch=master)](https://travis-ci.org/qua-bla/edentata)
[![Dependency Status](https://www.versioneye.com/php/hemio:edentata/0.2.0/badge.svg)](https://www.versioneye.com/php/hemio:edentata/0.2.0)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/beba6c45-ba42-42e5-bd99-b9cfadc0bd00/mini.png)](https://insight.sensiolabs.com/projects/beba6c45-ba42-42e5-bd99-b9cfadc0bd00)

### Requiremenets

Required Debian packages
- cracklib-runtime
- gettext
- make
- php5 (>= 5.6)
- php5-pgsql

### Installation

- Run `make` to build localizations
- Based on the *examples/config.yaml* create a config at */etc/edentata/config.yaml*
- Configure your webserver to serve *src/htdocs/index.php* as default (see *examples/apache2.conf*)

