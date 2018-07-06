<?php

use gizmo\Path;

describe('Path Object', function(){
    beforeEach(function() {
        $this->A = new Path('road/to/nowhere');
        $this->B = new Path(array('road', 'to', 'nowhere'));
        $this->C = new Path('/here/is/an/absolute/path');
        $this->D = new Path('/here/is/an/absolute/path/');
        $this->E = new Path('/');
        $this->F = new Path('');
    });

    it('->__construct(string)', function(){
        assert($this->A->getPath()[0] === 'road', 'Expected "road"');
        assert($this->A->getPath()[1] === 'to', 'Expected "to"');
        assert($this->A->getPath()[2] === 'nowhere', 'Expected "nowhere"');
    });

    it('->__construct(array)', function(){
        assert($this->B->getPath()[0] === 'road', 'Expected "road"');
        assert($this->B->getPath()[1] === 'to', 'Expected "to"');
        assert($this->B->getPath()[2] === 'nowhere', 'Expected "nowhere"');
    });

    it('->is_absolute = false', function(){
        assert(!$this->A->is_absolute, 'Expect "is_absolute" to be "false"');
    });

    it('->is_absolute = true', function(){
        assert($this->C->is_absolute, 'Expect "is_absolute" to be "true"');
    });
    
    it('->__toString()', function(){
        $expected = 'road/to/nowhere';
        $actual = (string)$this->A;
        assert($expected === $actual, 'Expected "road/to/nowhere"');
    });

    it('->length()', function(){
        assert($this->A->length() === 3, 'Expect A->length() to equal 3');
        assert($this->B->length() === 3, 'Expect B->length() to equal 3');
        assert($this->C->length() === 5, 'Expect C->length() to equal 5');
        assert($this->D->length() === 5, 'Expect D->length() to equal 5');
        assert($this->E->length() === 0, 'Expect E->length() to equal 0');
        assert($this->F->length() === 0, 'Expect F->length() to equal 0');
    });

    it('->convert()', function(){
        $array1 = $this->A->convert('/some/path');
        $array2 = $this->A->convert('/some/path/');
        $array3 = $this->A->convert('some/path');
        assert(count($array1) === 2, 'Expect to equal 2');
        assert(count($array2) === 2, 'Expect to equal 2');
        assert(count($array3) === 2, 'Expect to equal 2');
    });

    it('->head()', function(){
        assert($this->A->head() === 'road', 'Expect to equal "road"');
        assert($this->B->head() === 'road', 'Expect to equal "road"');
        assert($this->C->head() === 'here', 'Expect to equal "this"');
        assert($this->D->head() === 'here', 'Expect to equal "this"');
        assert($this->E->head() === '', 'Expect E->head() to equal ""');
    });

    it('->tail()', function(){
        assert($this->A->tail() === 'nowhere', 'Expect to equal "nowhere"');
        assert($this->B->tail() === 'nowhere', 'Expect to equal "nowhere"');
        assert($this->C->tail() === 'path', 'Expect to equal "path"');
        assert($this->D->tail() === 'path', 'Expect to equal "path"');
        assert($this->E->tail() === '', 'Expect E->tail() to equal ""');
    });

    it('->isPrefix()', function(){
        // True examples
        // assert($this->A->isPrefix(new Path('road/to')), 'Expect A->isPrefix("road/to") to be true');
        // assert($this->C->isPrefix(new Path('/here/is/an/absolute')), 'Expect C->isPrefix("/here/is/an/absolute") to be true');
        // assert($this->E->isPrefix(new Path('')), 'Expect E->isPrefix("") to be true');
        // False examples
        // assert(!$this->A->isPrefix(new Path('to/')), 'Expect A->isPrefix("to/") to be false');
        assert(!$this->C->isPrefix(new Path('/here/an/absolute')), 'Expect C->isPrefix("/here/an/absolute") to be false');
        // assert(!$this->E->isPrefix(new Path('to/')), 'Expect E->isPrefix("to/") to be false');
    });

    // it('->decapitate()', function(){
    //     assert($this->A->decapitate(new Path('road/to'))->length() === 1, 'Expect A->decapitate("road/to")->length() === 1');
    //     assert($this->C->decapitate(new Path('/here/is/an'))->length() === 2, 'Expect C->decapitate("/here/is/an/absolute")->length() === 2');
    //     assert($this->E->decapitate($this->F)->length() === 0, 'Expect E->decapitate("")->length() === 0');
    // });
});