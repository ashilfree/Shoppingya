<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\Transaction;
use App\Classes\WishList;
use App\Entity\Order;
use App\Form\OrderType;
use App\Form\PaymentMethodType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MyFatoorahController extends AbstractController
{
    //Test
    private $apiURL = 'https://apitest.myfatoorah.com';
    private $apiKey = 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var SessionInterface
     */
    private $session;
    //Live
    //private $apiURL = 'https://api.myfatoorah.com';
    //private $apiKey = ''; //Live token value to be placed here: https://myfatoorah.readme.io/docs/live-token
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, CategoryRepository $categoryRepository, Transaction $transaction, Cart $cart, WishList $wishlist, Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->transaction = $transaction;
        $this->cart = $cart;
        $this->mailer = $mailer;
        $this->wishlist = $wishlist;
        $this->session = $session;
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @Route("/{locale}/order/create-session/{id}", name="my.fatoorah.create.session", defaults={"locale"="en"})
     * @param $locale
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function index($locale, $id, Request $request): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);
        $form = $this->createForm(PaymentMethodType::class, $order);
        $form->handleRequest($request);
        $payment = $order->getPaymentMethod();
        if ($form->isSubmitted()) {
        if($payment){
            if (!$order || !$this->transaction->check($order, 'proceed_checkout'))
                return new JsonResponse(["error" => 'order']);
            $this->transaction->applyWorkFlow($order, 'proceed_checkout');
        $YOUR_DOMAIN = 'https://shoppingya.com';
//                $YOUR_DOMAIN = 'https://127.0.0.1:8000';
                //Fill POST fields array
                $ipPostFields = ['InvoiceAmount' => ($order->getTotal() + $order->getDeliveryPrice()) / 100, 'CurrencyIso' => 'KWD'];

                //Call endpoint
                $paymentMethods = $this->initiatePayment($this->apiURL, $this->apiKey, $ipPostFields);
                $p = $order->getPaymentMethod();
                //You can save $paymentMethods information in database to be used later
                foreach ($paymentMethods as $pm) {
                    if ($pm->PaymentMethodEn == $p->getName()) {
                        $paymentMethodId = $pm->PaymentMethodId;
                        break;
                    }
                }

                /* ------------------------ Call ExecutePayment Endpoint ---------------------*/
                //Fill customer address array
                $customerAddress = array(
                    'Block' => '', //optional
                    'Street' => '', //optional
                    'HouseBuildingNo' => '', //optional
                    'Address' => $order->getShippingAddress(), //optional
                    'AddressInstructions' => $order->getShippingCity() . '-' . $order->getShippingProvince(), //optional
                );

                //Fill invoice item array

                $invoiceItems = [];
                foreach ($order->getOrderDetails()->getValues() as $item) {
                    $invoiceItems[] = [
                        'ItemName' => $item->getProduct(), //ISBAN, or SKU
                        'Quantity' => $item->getQuantity(), //Item's quantity
                        'UnitPrice' => $item->getPrice() / 100, //Price per item
                    ];
                }
                $invoiceItems[] = [
                    'ItemName' => 'Delivery', //ISBAN, or SKU
                    'Quantity' => 1, //Item's quantity
                    'UnitPrice' => $order->getDeliveryPrice() / 100, //Price per item
                ];
                //Fill POST fields array
                $postFields = [
                    //Fill required data
                    'paymentMethodId' => $paymentMethodId,
                    'InvoiceValue' => (($order->getTotal() + $order->getDeliveryPrice()) / 100),
                    'CallBackUrl' => $YOUR_DOMAIN . '/' . $locale . '/order/thank/' . $order->getReference(),
                    'ErrorUrl' => $YOUR_DOMAIN . '/' . $locale . '/order/error/' . $order->getReference(),
                    'CustomerName' => $order->getShippingFullName(),
                    'DisplayCurrencyIso' => 'KWD',
                    'MobileCountryCode' => '+965',
                    'CustomerMobile' => $order->getShippingPhone(),
                    'CustomerEmail' => $order->getShippingEmail(),
                    'Language' => 'en', //or 'ar'
                    'CustomerReference' => $order->getReference(),
                    'CustomerCivilId' => 'CivilId',
                    'UserDefinedField' => '',
                    'ExpiryDate' => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                    'SourceInfo' => 'Pure PHP', //For example: (Laravel/Yii API Ver2.0 integration)
                    'CustomerAddress' => $customerAddress,
                    'InvoiceItems' => $invoiceItems,
                ];

                //Call endpoint
                $data = $this->executePayment($this->apiURL, $this->apiKey, $postFields);
                //You can save payment data in database as per your needs
                $paymentLink = $data->PaymentURL;
                $invoiceId = $data->InvoiceId;
                $invoiceKey = $this->get_string_between($data->PaymentURL, '=', '&');
                $order->setInvoiceId($invoiceId);
                $order->setInvoiceKey($invoiceKey);
            $this->entityManager->flush();
//        return $this->redirect($paymentLink, 301);
                return $this->redirect($paymentLink, 308);
                //Display the payment link to your customer
            }else{
            if (!$order || !$this->transaction->check($order, 'pay_en_delivery'))
                return new JsonResponse(["error" => 'order']);
            $this->session->clear();
            $this->transaction->applyWorkFlow($order, 'pay_en_delivery');
            $order->setOrderedAt(new \DateTime());
            $order->setPaymentMethod("PAY EN DELIVERY");
            $this->mailer->sendSuccessOrderEmail($order);
            $this->entityManager->flush();
            $path = ($locale == "en") ? 'order/order-complete.html.twig' : 'order/order-completeAr.html.twig';
            return $this->render($path , [
                'order' => $order,
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'order-complete',
                'categories' => $this->categoryRepository->findAll(),
            ]);
        }
        }
            return $this->redirectToRoute('order');

    }

    /* ------------------------ Functions --------------------------------------- */
    /*
     * Initiate Payment Endpoint Function
     */

    function initiatePayment($apiURL, $apiKey, $postFields)
    {

        $json = $this->callAPI("$apiURL/v2/InitiatePayment", $apiKey, $postFields);
        return $json->Data->PaymentMethods;
    }

//------------------------------------------------------------------------------
    /*
     * Execute Payment Endpoint Function
     */

    function executePayment($apiURL, $apiKey, $postFields)
    {

        $json = $this->callAPI("$apiURL/v2/ExecutePayment", $apiKey, $postFields);
        return $json->Data;
    }

    /*
     * Call API Endpoint Function
     */

    function callAPI($endpointURL, $apiKey, $postFields)
    {

        $curl = curl_init($endpointURL);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);
        $curlErr = curl_error($curl);

        curl_close($curl);

        if ($curlErr) {
            //Curl is not working in your server
            die("Curl Error: $curlErr");
        }

        $error = $this->handleError($response);
        if ($error) {
            die("Error: $error");
        }

        return json_decode($response);
    }

//------------------------------------------------------------------------------
    /*
     * Handle Endpoint Errors Function
     */

    function handleError($response): ?string
    {

        $json = json_decode($response);
        if (isset($json->IsSuccess) && $json->IsSuccess == true) {
            return null;
        }

        //Check for the errors
        if (isset($json->ValidationErrors) || isset($json->FieldsErrors)) {
            $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
            $blogDatas = array_column($errorsObj, 'Error', 'Name');

            $error = implode(', ', array_map(function ($k, $v) {
                return "$k: $v";
            }, array_keys($blogDatas), array_values($blogDatas)));
        } else if (isset($json->Data->ErrorMessage)) {
            $error = $json->Data->ErrorMessage;
        }

        if (empty($error)) {
            $error = (isset($json->Message)) ? $json->Message : (!empty($response) ? $response : 'API key or API URL is not correct');
        }

        return $error;
    }

    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
