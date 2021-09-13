<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Command\Async;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Contract\Command\CommandInterface;
use Bundle\UIBundle\Core\Contract\Command\HandlerInterface;

class Context extends AbstractContext
{
    protected ?HandlerInterface $handler;
    protected CommandInterface $command;

    public function __construct(
        CommandInterface $command,
        HandlerInterface $handler = null,
    ) {
        $this->handler = $handler;
        $this->command = $command;
    }

    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    public function setCommand(CommandInterface $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function getHandler(): ?HandlerInterface
    {
        return $this->handler;
    }

    public function setHandler(?HandlerInterface $handler): self
    {
        $this->handler = $handler;
        return $this;
    }
}
