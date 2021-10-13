<?php

namespace FormRelay\Core\Queue;

use DateTime;

interface JobInterface
{
    public function getId(): int;
    public function setId(int $id);

    public function getCreated(): DateTime;
    public function setCreated(DateTime $created);

    public function getStatus(): int;
    public function setStatus(int $status);

    public function getStatusMessage(): string;
    public function setStatusMessage(string $message);

    public function getChanged(): DateTime;
    public function setChanged(DateTime $changed);

    public function getData(): array;
    public function setData(array $data);
}
