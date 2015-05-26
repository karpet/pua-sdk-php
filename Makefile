test:
	prove -r t

all: install test

install:
	composer install

.PHONY: test all install
