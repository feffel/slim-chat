<?php
declare(strict_types=1);

use Phinx\Migration\CreationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateGenerator implements CreationInterface
{
    protected ?InputInterface $input;

    protected ?OutputInterface $output;

    public function __construct(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    public function getInput(): ?InputInterface
    {
        return $this->input;
    }

    public function getOutput(): ?OutputInterface
    {
        return $this->output;
    }

    public function setInput(?InputInterface $input): AbstractTemplateGenerator
    {
        $this->input = $input;
        return $this;
    }

    public function setOutput(?OutputInterface $output): AbstractTemplateGenerator
    {
        $this->output = $output;
        return $this;
    }

    public function getMigrationTemplate()
    {
        return file_get_contents(__DIR__.'/template.dist');
    }

    /** @inheritDoc */
    public function postMigrationCreation($migrationFilename, $className, $baseClassName): void { }
}
