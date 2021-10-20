<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract\Command;

/**
 * UIBundleFoundation
 */
interface HandlerInterface
{
    /**
     * @param CommandInterface $command
     * @return OutputContractInterface|void
     */
    public function handle(CommandInterface $command);
}
