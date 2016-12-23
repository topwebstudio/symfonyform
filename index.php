<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Form\Forms;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Translation\Translator;

$viewsDir = realpath(__DIR__ . '/views');
$vendorDir = realpath(__DIR__ . '/vendor');

$appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
$vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

$twig = new Twig_Environment(new Twig_Loader_Filesystem(array($viewsDir, $vendorTwigBridgeDir . '/Resources/views/Form')));
$twig->addRuntimeLoader(new Twig_FactoryRuntimeLoader(array(TwigRenderer::class => function () use ($twig) {
    return new TwigRenderer(
        new TwigRendererEngine(array('form_div_layout.html.twig'), $twig),
        new CsrfTokenManager(new UriSafeTokenGenerator(),  new NativeSessionTokenStorage())
    );
})));

$twig->addExtension(new FormExtension());
$twig->addExtension(new TranslationExtension(new Translator('en')));

$formFactory = Forms::createFormFactoryBuilder()
        ->getFormFactory();

$form = $formFactory->createBuilder()->add('task', TextType::class)->getForm();

echo $twig->render('form.html.twig', array('form' => $form->createView()));
