<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $router;
    private $flashBag;
    private $translator;
    private $defaultLocale;
    private $appLocales;

    public function __construct(
        RouterInterface $router,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        $defaultLocale,
        string $appLocales
    ) {
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
        $this->appLocales = $appLocales;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['redirectToLocale', 0],
            ]
        ];
    }

    public function redirectToLocale(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        // 'https://your-domain.com' or 'https://your-domain.com/app_dev.php'
        $base = $request->getSchemeAndHttpHost() . $request->getBaseUrl();

        // Redirect to previous referer if valid
        do {
            break; // fixme: for some reason on frontend the flash message added here is not shown

            $referer = $request->headers->get('referer');

            if (empty($referer)) {
                break;
            }

            $refererPath = preg_replace('/^'. preg_quote($base, '/') .'/', '', $referer);

            if ($refererPath === $referer) {
                // nothing was replaced. referer is an external site
                break;
            } elseif ($refererPath === $request->getPathInfo()) {
                // current page and referer are the same (prevent redirect loop)
                break;
            }

            try {
                // if this will throw an exception then the route doesn't exist
                $this->router->match(
                    // '/en/hello?foo=bar' -> '/en/hello'
                    preg_replace('/\?.*$/', '', $refererPath)
                );

                // '/app_dev.php' . '/en/article/3'
                $redirectUrl = $request->getBaseUrl() . $refererPath;

                $this->flashBag->add('error', $this->translator->trans('flash.error_404'));

                return $event->setResponse(new RedirectResponse($redirectUrl));
            } catch (ResourceNotFoundException $e) {}
        } while (false);

        // path must start with locale
        if (!preg_match(
            '/^\/('. $this->appLocales .')\/?/',
            $request->getPathInfo()
        )) {
            // '/app_dev.php' . '/en' . '/article/3'
            $redirectUrl = $request->getBaseUrl() . '/' . $this->defaultLocale . $request->getPathInfo();

            return $event->setResponse(new RedirectResponse($redirectUrl));
        }
    }
}
