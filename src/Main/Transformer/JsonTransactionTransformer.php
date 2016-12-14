<?php

namespace Main\Transformer;

use Main\Model\Transaction;

/**
 * Transform Transaction to an array
 */
class JsonTransactionTransformer
{
    /**
     * Transform transaction to an array
     *
     * @param Transaction $transaction
     *
     * @return array
     */
    public static function transform(Transaction $transaction)
    {
        return [
            'id' => $transaction->getId(),
            'user' => JsonUserTransformer::transform($transaction->getUser()),
            'sum' => $transaction->getSum(),
            'comment' => $transaction->getComment()
        ];
    }
    /**
     * Transform array of transactions to a json array
     *
     * @param Transaction[] $data
     *
     * @return array
     */
    public static function transformAll(array $data)
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = self::transform($item);
        }

        return $result;
    }
}