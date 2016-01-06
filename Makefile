
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

