<?php

namespace FormRelay\Core\Model\Queue;

use DateTime;
use FormRelay\Core\Queue\QueueInterface;

class Job implements JobInterface
{
    /** @var int */
    protected $id;

    /** @var DateTime */
    protected $created;

    /** @var DateTime  */
    protected $changed;

    /** @var int */
    protected $status;

    /** @var bool */
    protected $skipped;

    /** @var string */
    protected $statusMessage;

    /** @var array */
    protected $data;

    /** @var string */
    protected $hash;

    /** @var string */
    protected $label;

    public function __construct()
    {
        $this->created = new DateTime();
        $this->changed = new DateTime();
        $this->status = QueueInterface::STATUS_PENDING;
        $this->skipped = false;
        $this->statusMessage = '';
        $this->data = [];
        $this->hash = '';
        $this->label = '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function getSkipped(): bool
    {
        return $this->skipped;
    }

    public function setSkipped(bool $skipped)
    {
        $this->skipped = $skipped;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(string $message)
    {
        $this->statusMessage = $message;
    }

    public function getChanged(): DateTime
    {
        return $this->changed;
    }

    public function setChanged(DateTime $changed)
    {
        $this->changed = $changed;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
    }
}
