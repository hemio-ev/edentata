edentata
========

A toothless frontend for the hosting-service management carnivora.

https://packagist.org/packages/hemio/edentata

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/qua-bla/edentata/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/qua-bla/edentata/?branch=master)
[![Build Status](https://travis-ci.org/qua-bla/edentata.svg?branch=master)](https://travis-ci.org/qua-bla/edentata)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/beba6c45-ba42-42e5-bd99-b9cfadc0bd00/mini.png)](https://insight.sensiolabs.com/projects/beba6c45-ba42-42e5-bd99-b9cfadc0bd00)
[![Reference Status](https://www.versioneye.com/php/hemio:edentata/reference_badge.svg?style=flat)](https://www.versioneye.com/php/hemio:edentata/references)

## Requiremenets

Debian packages
- php5-pgsql
- cracklib-runtime
- php5 (>= 5.6)

# Apache2 configuration

```
AliasMatch ^<BASE_URL>(?!static).*/(.*)$ /usr/share/edentata/src/htdocs/index.php`
Alias <BASE_URL>/static/ /usr/share/edentata/src/htdocs/static/
```

Where the edentata `base_url` option is set to `<BASE_URL>`

Example:

```
AliasMatch ^/edentata/(?!static).*/(.*)$ /usr/share/edentata/src/htdocs/index.php
Alias /edentata/static/ /usr/share/edentata/src/htdocs/static/
```