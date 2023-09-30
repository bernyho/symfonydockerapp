<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ItemprotoType;
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

    public const HEAD = 'ITEM_VNUM	ITEM_NAME	ITEM_TYPE	SUB_TYPE	SIZE	ANTI_FLAG	FLAG	ITEM_WEAR	IMMUNE	GOLD	SHOP_BUY_PRICE	REFINE	REFINESET	MAGIC_PCT	LIMIT_TYPE0	LIMIT_VALUE0	LIMIT_TYPE1	LIMIT_VALUE1	ADDON_TYPE0	ADDON_VALUE0	ADDON_TYPE1	ADDON_VALUE1	ADDON_TYPE2	ADDON_VALUE2	VALUE0	VALUE1	VALUE2	VALUE3	VALUE4	VALUE5	Specular	SOCKET	ATTU_ADDON';

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

    /**
     * @Route("/itemproto", name="itemproto")
     */
    public function itemProto(Request $request): Response
    {
        $content = '';

        $form = $this->createForm(ItemprotoType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $operator = 1 + $data['bonusPercent'] / 100;
            $diff = $data['minattack'] * $operator - $data['minattack'];

            $plusBonus = [
                7 => 1.10,
                8 => 1.20,
                9 => 1.30,
            ];

            for ($i = $data['from']; $i <= $data['to']; $i++)
            {
                $plus = substr($i, -1);
                $bonusValue = ($plus < 7) ? $diff * $plus : ($diff * $plus) * $plusBonus[$plus];
                $content .= sprintf("%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s	%s\n",
                    $i, // ITEM_VNUM
                    $data['name'] . '+' . $plus, // ITEM_NAME(K)
                    $data['type'], // ITEM_TYPE
                    $data['subtype'],  // SUB_TYPE
                    $data['size'], // SIZE
                    $data['antiflag'], // ANTI_FLAG
                    $data['flag'], // FLAG
                    'WEAR_WEAPON', // ITEM_WEAR
                    'NONE', // IMMUNE,
                    $data['gold'], // GOLD,
                    $data['shopBuy'], // SHOP_BUY_PRICE,
                    ($plus == 9) ? '0' : $i + 1, // REFINE
                    $data['refineset'], // REFINESET
                    15, // MAGIC_PCT
                    'LEVEL', // LIMIT_TYPE0
                    ($i >=1) ? $data['level'] + ((int)$plus * $data['levelplus']) : $data['level'], // LIMIT_VALUE0
                    'LIMIT_NONE', // LIMIT_TYPE1
                    '0', // LIMIT_VALUE1
                    $data['applytype0'], // ADDON_TYPE0
                    ($i >=1) ? $data['applyvalue0'] + ((int)$plus * $data['plusvalue0']) : $data['applyvalue0'], // ADDON_VALUE0
                    $data['applytype1'], // ADDON_TYPE1
                    ($i >=1) ? $data['applyvalue1'] + ((int)$plus * $data['plusvalue1']) : $data['applyvalue1'], // ADDON_VALUE1
                    $data['applytype2'], // ADDON_TYPE2
                    ($i >=1) ? $data['applyvalue2'] + ((int)$plus * $data['plusvalue2']) : $data['applyvalue2'], // ADDON_VALUE2
                    '0', // VALUE0
                    $data['minmagicattack'], // VALUE1
                    $data['maxmagicattack'], // VALUE2
                    $data['minattack'], // VALUE3
                    $data['maxattack'], // VALUE4
                    (int)$bonusValue, // VALUE5
                    (string)$this->getSpecularByPlus($plus), // Specular
                    '3', // SOCKET
                    $data['skillAndAverage'] === 'ano' ? '-1' : '0' // ATTU_ADDON
                );
            }

            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/myText.txt","wb");
            fwrite($fp, $content);
            fclose($fp);

            return $this->redirectToRoute('itemproto');
        }

        $handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/myText.txt","r");
        $lines = [];
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $lines[] = preg_split('/\s+/', $line);
            }

            fclose($handle);
        }

        return $this->render('user/protos.html.twig', [
            'form' => $form->createView(),
            'content' => file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/myText.txt', true),
            'contentInLines' => $lines,
            'headInArray' => preg_split('/\s+/', self::HEAD)
        ]);

    }

    private function getSpecularByPlus(int $plus): int
    {
        $specular = 0;
        if ($plus === 4) {
            $specular = 30;
        } elseif ($plus === 5) {
            $specular = 40;
        } elseif ($plus === 6) {
            $specular = 50;
        } elseif ($plus === 7) {
            $specular = 65;
        } elseif ($plus === 8) {
            $specular = 80;
        } elseif ($plus === 9) {
            $specular = 100;
        }

        return $specular;
    }
}
