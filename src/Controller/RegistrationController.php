<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserChangePasswordTokenType;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailChangePassword;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EmailChangePassword $emailChangePassword;

    public function __construct(EmailVerifier $emailVerifier, EmailChangePassword $emailChangePassword)
    {
        $this->emailVerifier = $emailVerifier;
        $this->emailChangePassword = $emailChangePassword;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('cous.gabriel@gmail.com', 'gcousin'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('alert-success', 'Un mail vous a été envoyé !');
            return $this->redirectToRoute('app_login');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('alert-success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/password-forgot', name: 'app_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $formEmail = $this->createFormBuilder()
            ->add('email', EmailType::class, array(
                'required' => true
            ))->getForm();

        $formEmail->handleRequest($request);
        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $user = $userRepository->findOneBy(["email" => $formEmail->get('email')->getData()]);

            if ($user === null) {
                $this->addFlash("alert-danger", "Ce compte n'existe pas");
                return $this->redirectToRoute('app_change_password');
            }

            $this->emailChangePassword->sendEmailForgotPassword('app_change_password_confirm', $user,
                (new TemplatedEmail())
                    ->from(new Address('cous.gabriel@gmail.com', 'gcousin'))
                    ->to($user->getEmail())
                    ->subject('Change password please')
                    ->htmlTemplate('change_password/change_password_email.html.twig')
            );

            $this->addFlash("alert-success", "Un mail vous a été envoyé");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('change_password/change_password.html.twig', [
            'formEmail' => $formEmail->createView()
        ]);
    }

    #[Route('/change_password/email', name: 'app_change_password_confirm')]
    public function changePasswordUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }


        $formPasswordChange = $this->createForm(UserChangePasswordTokenType::class, $user);
        $formPasswordChange->handleRequest($request);

        if ($formPasswordChange->isSubmitted() && $formPasswordChange->isValid()) {
            $plainPassword = $formPasswordChange->get('plainPassword')->getData();
            try {
                $this->emailChangePassword->handleEmailChangePassword($request, $user, $plainPassword);
                $this->addFlash('alert-success', 'Votre mot de passe a été modifié.');
                return $this->redirectToRoute('app_home');
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('alert-danger', $exception->getReason());

                return $this->redirectToRoute('app_register');
            }
        }

        return $this->render('change_password/change_password_token.html.twig', [
            'formPasswordChange' => $formPasswordChange->createView()
        ]);
    }
}
