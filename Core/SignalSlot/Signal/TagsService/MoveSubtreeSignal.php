<?php

namespace EzSystems\TagsBundle\Core\SignalSlot\Signal\TagsService;

use eZ\Publish\Core\SignalSlot\Signal;

class MoveSubtreeSignal extends Signal
{
    /**
     * Source tag ID
     *
     * @var mixed
     */
    public $sourceTagId;

    /**
     * New parent tag ID
     *
     * @var mixed
     */
    public $newParentTagId;
}