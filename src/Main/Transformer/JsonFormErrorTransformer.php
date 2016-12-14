<?php

namespace Main\Transformer;

use Main\Model\Transaction;
use Symfony\Component\Form\FormError;

/**
 * Transform FormError to an array
 */
class JsonFormErrorTransformer
{
    /**
     * Transform transaction to an array
     *
     * @param FormError $formError
     *
     * @return array
     */
    public static function transform(FormError $formError)
    {
        return [
            'field' => $formError->getOrigin()->getName(),
            'message' => $formError->getMessage()
        ];
    }
    /**
     * Transform array of form errors to a json array
     *
     * @param FormError[] $data
     *
     * @return array
     */
    public static function transformAll($data)
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = self::transform($item);
        }

        return $result;
    }
}