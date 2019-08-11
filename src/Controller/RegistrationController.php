<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractFOSRestController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function index(Request $request)
    {
      $email = $request->get('email');
      $password = $request->get('password');

      $user = $this->userRepository->findOneBy([
          'email' => $email,
      ]);

      if ($user) {
          return $this->view([
              'message' => 'User already exists',
          ], Response::HTTP_CONFLICT);
      }

      /** @var $user User */
      $user = new User();

      $user->setEmail($email);
      //TODO: Add some validation on the password, e.g. min length etc
      $user->setPassword(
          $this->userPasswordEncoder->encodePassword($user, $password)
      );

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      return $this->view($user, Response::HTTP_CREATED)->setContext((new Context())->setGroups(['public']));
    }
}
