<?php

namespace Sholokhov\Utils\Resources;

use Countable;
use Serializable;

interface ResourceInterface extends Serializable, Countable
{
    /**
     * Добавление нового значения ресурса.
     *
     * @param mixed $value
     * @return self
     */
    public function push(mixed $value): self;

    /**
     * Добавление списка значений ресурса.
     *
     * @param array $values
     * @return self
     */
    public function bulk(array $values): self;

    /**
     * Удаление значения ресурса по идентификатору
     *
     * @param mixed $position
     * @return void
     */
    public function delete(mixed $position): void;

    /**
     * Получение значения итерации с изменением указателя.
     *
     * @return mixed
     */
    public function fetch(): mixed;

    /**
     * Достать все значения.
     *
     * @return array
     */
    public function fetchAll(): array;

    /**
     * Получение первого значения коллекции.
     *
     * @return mixed
     */
    public function first(): mixed;

    /**
     * Проверка на пустую коллекцию.
     *
     * @return bool
     */
    public function empty(): bool;
}