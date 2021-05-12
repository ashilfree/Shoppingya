<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\WishList;
use App\Entity\Customer;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var WishList
     */
    private $wishlist;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, Cart $cart, WishList $wishlist)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("/{locale}/reset-password", name="app_forgot_password_request", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @param Mailer $mailer
     * @return Response
     */
    public function request($locale, Request $request, Mailer $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $locale
            );
        }
        $path = ($locale == "en") ? 'authentication/reset_password.html.twig' : 'authentication/reset_passwordAr.html.twig';
        return $this->render($path, [
            'requestForm' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'reset_password'
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/{locale}/reset-password/check-email", name="app_check_email", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function checkEmail($locale): Response
    {
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request', ['locale' => $locale]);
        }
        $path = ($locale == "en") ? 'authentication/check_email.html.twig' : 'authentication/check_emailAr.html.twig';
        return $this->render($path, [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'check_email'
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/{locale}/reset-password/reset/{token}", name="app_reset_password", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param string|null $token
     * @return Response
     */
    public function reset($locale, Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password', ['locale' => $locale]);
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            $message = ($locale == "en") ? 'No reset password token found in the URL or in the session.' : 'لم يتم العثور على رمز إعادة تعيين كلمة المرور في عنوان أو في الجلسة.';
            throw $this->createNotFoundException($message);
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $message = ($locale == "en") ? 'There was a problem validating your reset request - %s' : 'حدثت مشكلة أثناء التحقق من صحة طلب إعادة التعيين -٪ s.';
            $this->addFlash('reset_password_error', sprintf(
                $message,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request', ['locale' => $locale]);
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('login', ['locale' => $locale]);
        }
        $path = ($locale == "en") ? 'authentication/reset.html.twig' : 'authentication/resetAr.html.twig';
        return $this->render($path, [
            'resetForm' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page'=> 'reset'
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, Mailer $mailer, string $locale): RedirectResponse
    {
        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$customer) {
            return $this->redirectToRoute('app_check_email', ['locale' => $locale]);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($customer);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            return $this->redirectToRoute('app_check_email', ['locale' => $locale]);
        }

        $mailer->sendResetPasswordEmail($customer,$resetToken,$this->resetPasswordHelper->getTokenLifetime());

        return $this->redirectToRoute('app_check_email', ['locale' => $locale]);
    }
}
