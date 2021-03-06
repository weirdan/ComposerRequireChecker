<?php

namespace ComposerRequireCheckerTest\DefinedSymbolsLocator;

use ArrayObject;
use ComposerRequireChecker\DefinedSymbolsLocator\LocateDefinedSymbolsFromASTRoots;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Trait_;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ComposerRequireChecker\DefinedSymbolsLocator\LocateDefinedSymbolsFromASTRoots
 */
class LocateDefinedSymbolsFromASTRootsTest extends TestCase
{
    /** @var LocateDefinedSymbolsFromASTRoots */
    private $locator;

    protected function setUp()
    {
        parent::setUp();

        $this->locator = new LocateDefinedSymbolsFromASTRoots();
    }

    public function testNoRoots()
    {
        $symbols = $this->locate([]);

        $this->assertCount(0, $symbols);
    }

    public function testBasicLocateClass()
    {
        $roots = [
            [new Class_('MyClassA'), new Class_('MyClassB')],
            [new Class_('MyClassC')],
        ];

        $symbols = $this->locate([$roots]);

        $this->assertInternalType('array', $symbols);
        $this->assertCount(3, $symbols);

        $this->assertContains('MyClassA', $symbols);
        $this->assertContains('MyClassB', $symbols);
        $this->assertContains('MyClassC', $symbols);
    }

    public function testBasicLocateFunctions()
    {
        $roots = [
            [new Function_('myFunctionA')],
            [new Class_('myFunctionB')],
        ];

        $symbols = $this->locate([$roots]);

        $this->assertInternalType('array', $symbols);
        $this->assertCount(2, $symbols);

        $this->assertContains('myFunctionA', $symbols);
        $this->assertContains('myFunctionB', $symbols);
    }

    public function testBasicLocateTrait()
    {
        $roots = [
            [new Trait_('MyTraitA'), new Trait_('MyTraitB')],
            [new Trait_('MyTraitC')],
        ];

        $symbols = $this->locate([$roots]);

        $this->assertInternalType('array', $symbols);
        $this->assertCount(3, $symbols);

        $this->assertContains('MyTraitA', $symbols);
        $this->assertContains('MyTraitB', $symbols);
        $this->assertContains('MyTraitC', $symbols);
    }

    public function testBasicLocateAnonymous()
    {
        $roots = [
            [new Class_(null)],
        ];

        $symbols = $this->locate([$roots]);

        $this->assertInternalType('array', $symbols);
        $this->assertCount(0, $symbols);
    }

    private function locate(array $roots): array
    {
        return ($this->locator)(new ArrayObject($roots));
    }
}
