
all: composer     l10n css version

dev: dev-composer l10n css version dev-fonts

l10n:
	./dev/gettextfmtall . ./locale edentata

css:
	scss --style compressed --sourcemap=none --force \
	    --update src/scss:src/htdocs/static/design

composer:
	composer install --no-dev

version:
	git describe --tags > VERSION

# Developement

test:
	phpunit

dev-composer:
	composer install

dev-fonts:
	cd src/htdocs/static/design && \
	 cp /usr/share/doc/fonts-cantarell/copyright ./LICENSE-CANTARELL && \
	 cp /usr/share/fonts/opentype/cantarell/*.otf ./ && \
	 for f in *.otf; do sfnt2woff $$f; done && \
	 rm *.otf

dev-serve:
	php -S localhost:8080 -t src/htdocs src/htdocs/index.php
