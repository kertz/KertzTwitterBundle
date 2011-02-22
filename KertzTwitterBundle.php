<?php

namespace Kertz\TwitterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KertzTwitterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return __DIR__;
    }
}
