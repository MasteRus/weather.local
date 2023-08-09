<?php

namespace App\Exceptions;


class DataSourceMisconfigurationException extends AbstractRenderableException
{
    protected function getAdditionalJsonErrorItems(): array
    {
        return [];
    }
}
