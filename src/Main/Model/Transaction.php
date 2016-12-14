<?php

namespace Main\Model;

/**
 * Transaction model
 */
class Transaction
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $sum;

    /**
     * @var string
     */
    private $comment;

    /**
     * Transaction constructor.
     * @param mixed $id
     * @param User $user
     * @param int $sum
     * @param string $comment
     */
    public function __construct($id = null, User $user = null, $sum = null, $comment = null)
    {
        $this->id = $id ? strval($id) : null;
        $this->user = $user;
        $this->sum = intval($sum);
        $this->comment = $comment;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Transaction
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param int $sum
     * @return Transaction
     */
    public function setSum($sum)
    {
        $this->sum = intval($sum);
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Transaction
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}