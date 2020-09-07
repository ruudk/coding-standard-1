<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule;

final class NoChainMethodCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ChainMethodCall.php', [[NoChainMethodCallRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return new NoChainMethodCallRule();
    }
}
