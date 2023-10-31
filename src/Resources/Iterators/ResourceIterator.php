<?php

namespace Sholokhov\Utils\Resources\Iterators;

use ArrayAccess;

use Sholokhov\Utils\Resources\ResourceKeyException;
use Sholokhov\Utils\Exceptions\Iterator\StopIteratorException;

/**
 * Итератор ресурсов.
 *
 * @author Daniil Sholokhov <sholohkov.daniil@gmail.com>
 */
class ResourceIterator implements IteratorInterface, ArrayAccess
{
    /**
     * Список доступных ресурсов.
     *
     * @var array
     */
    protected array $data;

    public function __construct($resources = [])
    {
        if (!is_array($resources)) {
            $resources = [$resources];
        }

        $this->data = $resources;
    }

    public function __serialize(): array
    {
        return $this->data;
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Получение всех ресурсов.
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->data;
    }

    /**
     * Получение первого значения коллекции.
     *
     * @return mixed
     * @throws ResourceKeyException
     * @throws StopIteratorException
     */
    public function first(): mixed
    {
        $this->rewind();
        $this->checkKey($this->key());
        return $this->current();
    }

    /**
     * Получение количества ресурсов.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Проверка на пустую коллекцию.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return $this->count() <= 0;
    }

    /**
     * Добавление нового значения в конец итерации
     *
     * @param mixed $value
     * @return $this
     */
    public function push(mixed $value): self
    {
        $this->data[] = $value;
        return $this;
    }

    /**
     * Добавление нового списка ресурсов.
     *
     * @param array $values
     * @return $this
     */
    public function bulk(array $values): self
    {
        array_walk($values, function($value, $key) {
            $this->offsetSet($key, $value);
        });

        return $this;
    }

    /**
     * Получение значения итерации с изменением указателя.
     *
     * @return mixed
     * @throws ResourceKeyException
     * @throws StopIteratorException
     */
    public function fetch(): mixed
    {
        $this->checkEmpty();
        $resource = $this->current();
        $this->delete($this->key());

        return $resource;
    }

    /**
     * Удаление ресурса по позиции.
     *
     * @throws ResourceKeyException
     * @param mixed $position
     * @return void
     */
    public function delete(mixed $position): void
    {
        if (!$this->exist($position)) {
            throw new ResourceKeyException();
        }

        unset($this->data[$position]);
        $this->data = array_values($this->data);
    }

    /**
     * Получение текущего ресурса.
     *
     * @return mixed
     * @throws StopIteratorException
     */
    public function current(): mixed
    {
        $this->checkEmpty();
        return current($this->data);
    }

    /**
     * Перевод указателя на следующий ресурс
     *
     * @return void
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * Получение ключа ресурса.
     *
     * @return string|int|null
     */
    public function key(): string|int|null
    {
        return key($this->data);
    }

    /**
     * Проверка доступности ресурса.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->offsetExists($this->key());
    }

    /**
     * Перевод на указателя на первый ресурс.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * Проверка наличия значения по ключу.
     *
     * @param mixed $key
     * @return bool
     */
    public function exist(mixed $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Проверка валидности ключа.
     * Если проверка не будет пройдена, то произойдет вызов исключения.
     *
     * @param mixed $key
     * @return void
     * @throws ResourceKeyException
     */
    protected function checkKey(mixed $key): void
    {
        if (!$this->exist($key)) {
            throw new ResourceKeyException();
        }
    }

    /**
     * Проверка коллекции на пустоту.
     * Если проверка не будет пройдена, то произойдет вызов исключения.
     *
     * @throws StopIteratorException
     * @return void
     */
    protected function checkEmpty(): void
    {
        if ($this->empty()) {
            $this->stop();
        }
    }

    /**
     * Остановка работы итератора.
     *
     * @return void
     * @throws StopIteratorException
     */
    protected function stop(): void
    {
        throw new StopIteratorException();
    }

    /**
     * Сериализация данных итератора
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize($this->data);
    }

    /**
     * Создание итератора из сериализованной строки.
     *
     * @param string $data
     * @return $this
     */
    public function unserialize(string $data): self
    {
        return new static($data);
    }

    /**
     * Проверка наличия итерационного значения по ключу.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Получение значения по ключу.
     *
     * @param mixed $offset
     * @return mixed
     * @throws ResourceKeyException
     */
    public function offsetGet(mixed $offset): bool
    {
        $this->checkKey($offset);

        return $this->data[$offset];
    }

    /**
     * Добавление значения в итерационный список с указанием произвольного ключа.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * Исключение значения из итерационного списка по индивидуальному ключу.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($this->exist($offset)) {
            unset($this->data[$offset]);
        }
    }
}