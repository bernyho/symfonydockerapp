<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api",name="api_")
 */
class ApiController extends AbstractFOSRestController
{
	private $userRepository;
	private $userPasswordEncoder;

	public function __construct(
		UserRepository $userRepository,
		UserPasswordEncoderInterface $userPasswordEncoder
	) {
		$this->userRepository = $userRepository;
		$this->userPasswordEncoder = $userPasswordEncoder;
	}

	/**
	 * @return JsonResponse
	 * @Route("/users", name="posts", methods={"GET"})
	 */
	public function getUsers(): JsonResponse
	{
		$data = $this->userRepository->findAll();
		$results = [];

		foreach ($data as $d) {
			$results[] = $d->jsonSerialize();
		}

		return new JsonResponse($results);
	}

	/**
	 * @param Request $request
	 * @param EntityManagerInterface $entityManager
	 * @return JsonResponse
	 * @throws Exception
	 * @Route("/user", name="add", methods={"POST"})
	 */
	public function postUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		try {
			$request = $this->transformRequest($request);
			if (!$request ||
				!$request->get('name') ||
				!$request->get('email') ||
				!$request->get('password') ||
				!$request->get('roles')
			) {
				throw new Exception();
			}
		} catch (Exception $e) {
			$data = [
				'status' => 422,
				'errors' => "No valid data.",
			];
			return new JsonResponse($data, 422);
		}

		try {
			$user = new User();
			$user->setName($request->get('name'));
			$user->setPassword(
				$this->userPasswordEncoder->encodePassword($user, $request->get('password'))
			);
			$user->setEmail($request->get('email'));
			$user->setRoles($request->get('roles'));

			$entityManager->persist($user);
			$entityManager->flush();

			$data = [
				'status' => 200,
				'success' => "New user added successfully.",
			];

			return new JsonResponse($data);
		} catch (UniqueConstraintViolationException $exception) {
			$data = [
				'status' => 422,
				'errors' => "Duplicate email.",
			];
			return new JsonResponse($data, 422);
		}

	}

	protected function transformRequest(Request $request)
	{
		$data = json_decode($request->getContent(), true);

		if ($data === null) {
			return $request;
		}

		$request->request->replace($data);

		return $request;
	}

}
