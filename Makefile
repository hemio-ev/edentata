
all: composer     l10n css version

dev: composer-dev l10n css version

l10n:
	# generate l10n
	./gettextfmtall . ./locale edentata

css:
	scss --sourcemap=none --force --update src/scss:src/htdocs/static/design

composer:
	composer install --no-dev

composer-dev:
	composer install

version:
	git describe --tags > VERSION

fonts:
	cd src/htdocs/static/design && \
	 cp /usr/share/doc/fonts-cantarell/copyright ./LICENSE-CANTARELL
	 cp /usr/share/fonts/opentype/cantarell/*.otf ./ && \
	 for f in *.otf; do sfnt2woff $$f; done && \
	 rm *.otf

