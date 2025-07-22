<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
{
    $roles = $token->getRoleNames();   // e.g. ['ROLE_ADMIN', 'ROLE_USER']

    if (in_array('ROLE_ADMIN', $roles, true)) {
        $redirectUrl = $this->router->generate('admin_dashboard');

    } elseif (in_array('ROLE_TECHNICIAN', $roles, true)) {
        $redirectUrl = $this->router->generate('app_technicianpage');

    } elseif (in_array('ROLE_CENTERMANAGER', $roles, true)) {
        $redirectUrl = $this->router->generate('app_center_manager');

    } else {
        $redirectUrl = $this->router->generate('home');
    }

    return new RedirectResponse($redirectUrl);
}}