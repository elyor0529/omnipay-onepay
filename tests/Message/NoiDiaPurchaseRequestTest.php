<?php

namespace Omnipay\OnePay\Message;

use Omnipay\Tests\TestCase;

class NoiDiaPurchaseRequestTest extends TestCase
{

    /**
     * @var \Omnipay\OnePay\Message\NoiDiaPurchaseRequest
     */
    private $request;


    public function setUp()
    {
        $this->request = new NoiDiaPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->options = [
            'vpcMerchant' => 'ONEPAY',
            'vpcAccessCode' => 'D67342C2',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'returnUrl' => 'http://www.google.com/app_dev.php/backend/process_transaction.html/1431785?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'amount' => 1000.00,
            'currency' => 'VND',
            'transactionId' => '1431785'
        ];

        $this->request->initialize($this->options);
    }


    public function testGetData()
    {
        $expected = [
            'vpc_Merchant' => 'ONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'vpc_order_id' => '1431785',
            'Title' => 'VPC 3-Party',
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'virtualPaymentClientURL' => $this->testGetEndpoint(),
            'vpc_Amount' => 100000,
            'vpc_Locale' => $this->getHttpRequest()->getLocale(),
            'vpc_ReturnURL' => 'http://www.google.com/app_dev.php/backend/process_transaction.html/1431785?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'vpc_TicketNo' => $this->getHttpRequest()->getClientIp(),
            'vpc_Currency' => 'VND'

        ];

        $requetData = $this->request->getData();

        $this->assertSame('1431785', $requetData['vpc_MerchTxnRef']);
        $this->assertSame('1431785', $requetData['vpc_OrderInfo']);
        $this->assertNotNull($requetData['vpc_OrderInfo']);

        // exclude by random property
        unset($requetData['vpc_OrderInfo']);
        unset($requetData['vpc_MerchTxnRef']);

        $this->assertEquals($expected, $requetData);
    }


    public function testSendData()
    {
        $this->testGetData();

        $data = $this->request->generateDataWithChecksum($this->request->getData());

        $this->assertArrayHasKey('vpc_SecureHash', $data);
    }


    public function testGetEndpoint()
    {
        $reflectionOfUser = new \ReflectionClass('\Omnipay\OnePay\Message\NoiDiaPurchaseRequest');
        $method = $reflectionOfUser->getMethod('getEndpoint');
        $method->setAccessible(true);

        $this->assertEquals('https://mtf.onepay.vn/onecomm-pay/vpc.op', $method->invokeArgs($this->request, []));

        return 'https://mtf.onepay.vn/onecomm-pay/vpc.op';
    }
}
