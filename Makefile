
docs: FORCE
	./vendor/bin/phpdoc -d ./gizmo -t ./docs

tests:
	./vendor/bin/peridot ./test

FORCE: