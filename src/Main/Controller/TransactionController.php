<?php

namespace Main\Controller;

use Main\Form\Handler\TransactionFormHandler;
use Main\Model\Transaction;
use Main\Repository\TransactionRepository;
use Main\Transformer\JsonFormErrorTransformer;
use Main\Transformer\JsonTransactionTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Transactions controller
 */
class TransactionController
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var TransactionFormHandler
     */
    private $formHandler;

    /**
     * TransactionController constructor.
     * @param TransactionRepository $transactionRepository
     * @param TransactionFormHandler $formHandler
     * @internal param FormFactory $formFactory
     */
    public function __construct(TransactionRepository $transactionRepository, TransactionFormHandler $formHandler)
    {
        $this->transactionRepository = $transactionRepository;
        $this->formHandler = $formHandler;
    }

    /**
     * Rest a list of transactions
     * Also return a form elements
     */
    public function listAction()
    {
        $this->formHandler->setData(new Transaction());

        $form = $this->formHandler->getForm();
        $formFields = $form->all();
        $columns = [];

        foreach ($formFields as $field) {
            $column = [
                'name' => $field->getName(),
                'type' => $field->getConfig()->getType()->getBlockPrefix(),
                'label' => $field->getConfig()->getOption('label') ? : null,
                'required' => $field->isRequired()
            ];

            if ($column['type'] === 'choice') {
                $column['choices'] = $field->getConfig()->getOption('choices');
            }

            $columns[] = $column;
        }
        return new JsonResponse(
            [
                'data' => JsonTransactionTransformer::transformAll($this->transactionRepository->findAll()),
                'columns' => $columns
            ]
        );
    }

    /**
     * Update transaction
     */
    public function updateAction($id, Request $request)
    {
        $transaction = $this->transactionRepository->findOne($id);

        if (null === $transaction) {
            throw new NotFoundHttpException();
        }

        $this->formHandler->setData($transaction);

        if ($this->formHandler->handle($request)) {
            return new JsonResponse(JsonTransactionTransformer::transform($this->formHandler->getData()));
        }

        return new JsonResponse([
            'errors' => (array) JsonFormErrorTransformer::transformAll($this->formHandler->getErrors())
        ], 400);
    }

    /**
     * Create new transaction
     */
    public function createAction(Request $request)
    {
        $this->formHandler->setData(new Transaction());

        if ($this->formHandler->handle($request)) {
            return new JsonResponse(JsonTransactionTransformer::transform($this->formHandler->getData()));
        }

        return new JsonResponse([
            'errors' => (array) JsonFormErrorTransformer::transformAll($this->formHandler->getErrors())
        ], 400);
    }

    /**
     * Delete transaction
     */
    public function deleteAction($id, Request $request)
    {
        $transaction = $this->transactionRepository->findOne($id);

        if (null === $transaction) {
            throw new NotFoundHttpException();
        }

        $this->transactionRepository->delete($transaction);

        return new JsonResponse();
    }
}