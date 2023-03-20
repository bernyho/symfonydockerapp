<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{
	private $httpClient;

	public const USER_POST_URL = 'http://app_nginx/api/user';
	public const USER_GET_URL = 'http://app_nginx/api/users';

	public function __construct(HttpClientInterface $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	/**
	 * @Route("/", name="home")
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	public function index(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$user = $form->getData();
			$response = $this->httpClient->request('POST', self::USER_POST_URL, [
					'body' => [
						'name' => $user->getName(),
						'email' => $user->getEmail(),
						'password' => $userPasswordEncoder->encodePassword($user, $user->getPassword()),
						'roles' => $user->getRoles()
					]
				]
			);

			if($response->getStatusCode() === 200) {
				$this->addFlash("success", "Success!");
			}

			return $this->redirectToRoute('home');
		}
		return $this->render('user/index.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/list", name="list")
	 */
	public function list(): Response
	{
		$response = $this->httpClient->request('GET', self::USER_GET_URL);

		return $this->render('user/list.html.twig', [
			'list' => json_decode($response->getContent(), true)
		]);
	}
}
