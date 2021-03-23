<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PhpBench\Report\Renderer;

use PhpBench\Expression\Printer;
use PhpBench\Registry\Config;
use PhpBench\Report\Model\Reports;
use PhpBench\Report\Model\Table;
use PhpBench\Report\RendererInterface;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders the report as a delimited list.
 */
class DelimitedRenderer implements RendererInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Printer
     */
    private $printer;

    public function __construct(OutputInterface $output, Printer $printer)
    {
        $this->output = $output;
        $this->printer = $printer;
    }

    /**
     * Render the table.
     *
     */
    public function render(Reports $reports, Config $config): void
    {
        foreach ($reports->tables() as $table) {
            $this->renderTable($table, $config);
        }
    }

    protected function renderTable(Table $table, $config): void
    {
        $rows = [];

        if (true === $config['header']) {
            $rows[] = $table->columnNames();
        }

        foreach ($table as $tableRow) {
            $row = [];

            foreach ($tableRow as $name => $node) {
                $row[$name] = $this->printer->print($node, []);
            }

            $rows[] = $row;
        }

        $fname = $config['file'] ?: 'php://temp';
        $pointer = fopen($fname, 'w+');

        if (false === $pointer) {
            throw new RuntimeException(sprintf(
                'Could not open file "%s"', $fname
            ));
        }

        foreach ($rows as $row) {
            // use fputcsv to handle escaping
            fputcsv(
                $pointer,
                $row,
                $config['delimiter']
            );
        }

        rewind($pointer);

        $contents = stream_get_contents($pointer);

        if (false === $contents) {
            throw new RuntimeException(sprintf(
                'Could not read stream "%s"', $fname
            ));
        }

        $this->output->write($contents);
        fclose($pointer);

        if ($config['file']) {
            $this->output->writeln('Dumped delimited file:');
            $this->output->writeln($config['file']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $options): void
    {
        $options->setDefaults([
            'delimiter' => "\t",
            'file' => null,
            'header' => true,
        ]);
    }
}
