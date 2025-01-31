<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ValueObject;

use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class BlockInfoMetadata
{
    public function __construct(
        private readonly string $blockType,
        private readonly BlockInfo $blockInfo
    ) {
    }

    public function getBlockType(): string
    {
        return $this->blockType;
    }

    public function getBlockInfo(): BlockInfo
    {
        return $this->blockInfo;
    }
}
