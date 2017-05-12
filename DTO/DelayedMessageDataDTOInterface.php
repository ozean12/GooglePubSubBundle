<?php

namespace Ozean12\GooglePubSubBundle\DTO;

/**
 * Interface DelayedMessageDataDTOInterface
 *
 * A basic interface which should implement delayed messages handled to Publisher
 */
interface DelayedMessageDataDTOInterface extends MessageDataDTOInterface
{
    /**
     * @return \DateTime
     */
    public function getExecuteAt(): \DateTime;
}
