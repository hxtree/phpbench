<?php

namespace PhpBench\Expression\NodePrinter;

use PhpBench\Expression\Ast\FloatNode;
use PhpBench\Expression\Ast\Node;
use PhpBench\Expression\Ast\PercentDifferenceNode;
use PhpBench\Expression\NodePrinter;
use PhpBench\Expression\Printer;

class PercentageDifferencePrinter implements NodePrinter
{
    /**
     * {@inheritDoc}
     */
    public function print(Printer $printer, Node $node, array $params): ?string
    {
        if (!$node instanceof PercentDifferenceNode) {
            return null;
        }

        $prefix = $node->percentage() > 0 ? '+' : '';

        return sprintf(
            '%s%s%%',
            $prefix,
            $printer->print(new FloatNode($node->percentage()), $params)
        );
    }
}
