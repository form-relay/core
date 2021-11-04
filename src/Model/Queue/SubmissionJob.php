<?php

namespace FormRelay\Core\Model\Queue;

use DateTime;
use FormRelay\Core\Queue\JobInterface;
use FormRelay\Core\Queue\QueueInterface;

class SubmissionJob implements JobInterface
{
    protected $id;
    protected $created;
    protected $status;
    protected $statusMessage;
    protected $changed;
    protected $data;

    public function __construct()
    {
        $this->created = new DateTime();
        $this->changed = new DateTime();
        $this->status = QueueInterface::STATUS_PENDING;
        $this->statusMessage = '';
        $this->data = [];
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
}
