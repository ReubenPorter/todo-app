<?php


namespace App\Listeners;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshTokenListener implements EventSubscriberInterface
{
    private $secure = false;
    private $ttl;

    public function __construct($ttl)
    {
        $this->ttl = $ttl;
    }

    public function setRefreshToken(AuthenticationSuccessEvent $event)
    {
        $refreshToken = $event->getData()['refresh_token'];
        $response = $event->getResponse();

        if ($refreshToken) {
            $currentTime = new \DateTime();
            $expireTime = $currentTime->add(new \DateInterval('PT' .$this->ttl . 'S'));

            $response->headers->setCookie(
                new Cookie('REFRESH_TOKEN', $refreshToken, $expireTime, '/', null, $this->secure)
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => [
                ['setRefreshToken']
            ]
        ];
    }
}