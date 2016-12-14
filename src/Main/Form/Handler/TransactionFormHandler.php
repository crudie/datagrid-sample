<?php

namespace Main\Form\Handler;

use Main\Form\TransactionType;
use Main\Model\Transaction;
use Main\Repository\TransactionRepository;
use Main\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transaction form handler
 */
class TransactionFormHandler
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TransactionFormHandler constructor.
     * @param FormFactory $formFactory
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(FormFactory $formFactory, TransactionRepository $transactionRepository, UserRepository $userRepository)
    {
        $formBuilder = $formFactory->createBuilder(TransactionType::class);
        $this->transactionRepository = $transactionRepository;

        $users = $userRepository->findAll();
        $choices = [];

        foreach ($users as $user) {
            $choices[$user->getName()] = $user->getId();
        }

        $formBuilder->add('user', ChoiceType::class, [
            'required' => true,
            'choices' => $choices,
            'constraints' => [new Assert\NotBlank()],
            'label' => 'User'
        ]);

        $this->form = $formBuilder->getForm();
        $this->userRepository = $userRepository;
    }

    /**
     * Handle request
     *
     * @param Request $request
     *
     * @return bool
     */
    public function handle(Request $request)
    {
        $this->form->submit($request->request->all());
        $this->errors = [];

        if ($this->form->isValid()) {
            return $this->onSuccess();
        }

        $this->errors = $this->form->getErrors(true);

        return false;
    }

    /**
     * Save data on success
     */
    public function onSuccess()
    {
        $this->getData()->setUser($this->userRepository->findOne($this->getData()->getUser()));

        $this->transactionRepository->save($this->getData());

        return true;
    }

    /**
     * Set data
     *
     * @param Transaction $transaction
     *
     * @return $this
     */
    public function setData(Transaction $transaction)
    {
        $this->form->setData($transaction);

        return $this;
    }

    /**
     * Get data
     *
     * @return Transaction
     */
    public function getData()
    {
        return $this->form->getData();
    }

    /**
     * @return FormError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}