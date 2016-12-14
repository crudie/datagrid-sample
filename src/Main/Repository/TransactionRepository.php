<?php

namespace Main\Repository;

use Main\Model\Transaction;


/**
 * Transaction repository
 */
interface TransactionRepository
{
    /**
     * Find all transactions
     *
     * @return Transaction[]
     */
    public function findAll();

    /**
     * Find one user by $Id
     *
     * @param mixed$id
     *
     * @return Transaction|null
     */
    public function findOne($id);

    /**
     * Save transaction
     *
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction);

    /**
     * Delete transaction
     *
     * @param Transaction $transaction
     */
    public function delete(Transaction $transaction);
}