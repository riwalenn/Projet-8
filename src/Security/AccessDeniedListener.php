<?php


namespace App\Security;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedListener implements EventSubscriberInterface
{
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return ['kernel.exception' => 'onKernelException'];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = sprintf(
            $exception->getMessage(),
            $exception->getCode()
        );

        $response = new Response();
        $response->setContent($message);

        if (!$exception instanceof HttpException) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            switch ($exception) {
                case $exception instanceof NotFoundHttpException :
                    $response->setStatusCode($exception->getStatusCode());
                    $response->setContent("Ressource non trouvée");
                    break;

                case $exception instanceof UnauthorizedHttpException :
                    $response->setStatusCode($exception->getStatusCode());
                    $response->setContent("Vous n'avez pas les autorisations pour accéder à cette page.");
                    break;

                default:
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->replace($exception->getHeaders());
                    break;
            }
        }
    }
}