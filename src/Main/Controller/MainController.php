<?php

namespace Main\Controller;


class MainController
{
    /**
     * @var
     */
    private $twig;

    /**
     * MainController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Render index page
     */
    public function indexAction()
    {
        return $this->twig->render('Main/Views/index.html.twig');
    }
}