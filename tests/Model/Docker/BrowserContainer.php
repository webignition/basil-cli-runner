<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

class BrowserContainer extends Container
{
    public function __construct(
        string $name,
        string $image,
        string $localPath,
        string $path
    ) {
        parent::__construct(
            $name,
            $image,
            [
                '-p 8000',
                '-v ' . $localPath . ':' . $path,
            ]
        );
    }
}
