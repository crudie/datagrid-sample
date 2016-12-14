<?php

namespace Main\Model;

/**
 * User model
 */
class User
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * User constructor.
     * @param mixed $id
     * @param string $name
     */
    public function __construct($id = null, $name = null)
    {
        $this->id = $id ? strval($id) : null;
        $this->name = $name;
    }

    /**
     * Convert model to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? : 'Пользователь';
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        if (!$this->id) {
            $this->id = strval($id);

            return $this;
        }

        throw new \BadMethodCallException('Id was already set');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}