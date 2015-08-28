<?php

namespace Concise\Console;

use Concise\TestCase;

class CommandStub extends Command
{
    public function setArgument($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    public function setCI($ci)
    {
        $this->ci = $ci;
    }
}

class CommandTest extends TestCase
{
    public function testCommandExtendsPHPUnit()
    {
        $this->aassert(new Command())->instanceOf('PHPUnit_TextUI_Command');
    }

    protected function getCommandMock()
    {
        return $this->niceMock('Concise\Console\CommandStub')->expose(
            'createRunner'
        )->get();
    }

    public function testCreateRunnerReturnsAConciseRunner()
    {
        $command = $this->getCommandMock();
        $this->aassert($command->createRunner())
            ->instanceOf('Concise\Console\TestRunner\DefaultTestRunner');
    }

    public function testPrinterUsesProxy()
    {
        $command = $this->getCommandMock();
        $this->aassert($command->createRunner()->getPrinter())
            ->instanceOf('Concise\Console\ResultPrinter\ResultPrinterProxy');
    }

    public function testVerboseIsFalseByDefault()
    {
        $command = $this->getCommandMock();
        $this->aassert(
            $command->createRunner()
                ->getPrinter()
                ->getResultPrinter()
                ->isVerbose()
        )->isFalse;
    }

    public function testVerboseIsTurnedOnIfItExistsAndHasBeenSet()
    {
        $command = $this->getCommandMock();
        $command->setArgument('verbose', true);
        $this->aassert(
            $command->createRunner()
                ->getPrinter()
                ->getResultPrinter()
                ->isVerbose()
        )->isTrue;
    }

    public function testVerboseIsNotTurnedOnIfItExistsButIfNotTrue()
    {
        $command = $this->getCommandMock();
        $command->setArgument('verbose', false);
        $this->aassert(
            $command->createRunner()
                ->getPrinter()
                ->getResultPrinter()
                ->isVerbose()
        )->isFalse;
    }

    public function testCIResultPrinterIsUsedIfCIIsTrue()
    {
        $command = $this->getCommandMock();
        $command->setCI(true);

        $this->aassert($command->getResultPrinter())
            ->instanceOf('Concise\Console\ResultPrinter\CIResultPrinter');
    }

    public function testDefaultResultPrinterIsNotUsedIfCIIsFalse()
    {
        $command = $this->getCommandMock();
        $command->setCI(false);

        $this->aassert($command->getResultPrinter())
            ->instanceOf('Concise\Console\ResultPrinter\DefaultResultPrinter');
    }

    public function testDefaultResultPrinterIsUsedByDefault()
    {
        $command = $this->getCommandMock();
        $this->aassert($command->getResultPrinter())
            ->instanceOf('Concise\Console\ResultPrinter\DefaultResultPrinter');
    }
}
