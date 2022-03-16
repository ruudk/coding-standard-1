<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\ParamNewliner;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\StandaloneLineConstructorParamFixerTest
 */
final class StandaloneLineConstructorParamFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Constructor property should be on standalone line';

    public function __construct(
        private ParamNewliner $paramNewliner
    ) {
    }

    /**
     * Must run before
     *
     * @see BracesFixer::getPriority()
     */
    public function getPriority(): int
    {
        return 40;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];

            if (! $token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $functionName = $this->getFunctionName($tokens, $position);
            if ($functionName !== MethodName::CONSTRUCTOR) {
                continue;
            }

            $this->paramNewliner->processFunction($tokens, $position);
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(public int $age, private string $name)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(
        public int $age,
        private string $name
    ) {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function getFunctionName(Tokens $tokens, int $position): ?string
    {
        $nextToken = $this->getNextMeaningfulToken($tokens, $position);
        if (! $nextToken instanceof Token) {
            return null;
        }

        return $nextToken->getContent();
    }
}