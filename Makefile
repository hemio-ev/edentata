
all: l10n css

l10n:
	# generate l10n
	./gettextfmtall . ./locale edentata

css:
	scss --sourcemap=none --force --update src/scss:src/htdocs/static/design

