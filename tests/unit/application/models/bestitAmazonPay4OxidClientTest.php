<?php

require_once dirname(__FILE__).'/../../bestitAmazon4OxidUnitTestCase.php';

use PHPUnit_Extensions_Constraint_StringMatchIgnoreWhitespace as MatchIgnoreWhitespace;
use AmazonPay\Client;
use AmazonPay\ResponseParser;

/**
 * Class bestitAmazonPay4OxidClientTest
 * @coversDefaultClass bestitAmazonPay4OxidClient
 */
class bestitAmazonPay4OxidClientTest extends bestitAmazon4OxidUnitTestCase
{
    /**
     * @param                                   $oClient
     * @param oxConfig                          $oConfig
     * @param DatabaseInterface                 $oDatabase
     * @param oxLang                            $oLanguage
     * @param oxSession                         $oSession
     * @param oxUtilsDate                       $oUtilsDate
     * @param bestitAmazonPay4OxidObjectFactory $objectFactory
     *
     * @return bestitAmazonPay4OxidClient
     */
    private function _getObject(
        $oClient,
        oxConfig $oConfig,
        DatabaseInterface $oDatabase,
        oxLang $oLanguage,
        oxSession $oSession,
        oxUtilsDate $oUtilsDate,
        bestitAmazonPay4OxidObjectFactory $objectFactory
    ) {
        $oBestitAmazonPay4OxidClient = new bestitAmazonPay4OxidClient();
        self::setValue($oBestitAmazonPay4OxidClient, '_oAmazonClient', $oClient);
        self::setValue($oBestitAmazonPay4OxidClient, '_oConfigObject', $oConfig);
        self::setValue($oBestitAmazonPay4OxidClient, '_oDatabaseObject', $oDatabase);
        self::setValue($oBestitAmazonPay4OxidClient, '_oLanguageObject', $oLanguage);
        self::setValue($oBestitAmazonPay4OxidClient, '_oSessionObject', $oSession);
        self::setValue($oBestitAmazonPay4OxidClient, '_oUtilsDateObject', $oUtilsDate);
        self::setValue($oBestitAmazonPay4OxidClient, '_oObjectFactory', $objectFactory);

        return $oBestitAmazonPay4OxidClient;
    }

    /**
     * @param array $aResponse
     *
     * @return ResponseParser|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAmazonResponseParserMock(array $aResponse = array())
    {
        $oResponseParser = parent::_getAmazonResponseParserMock();
        $oResponseParser->expects($this->any())
            ->method('toJson')
            ->will($this->returnValue(json_encode($aResponse)));

        return $oResponseParser;
    }

    /**
     * @return oxConfig|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getConfigMock()
    {
        $oShop = $this->_getShopMock();
        $oShop->expects($this->any())
            ->method('getFieldData')
            ->with('oxname')
            ->will($this->returnValue('shopName'));

        $oConfig =  parent::_getConfigMock();
        $oConfig->expects($this->any())
            ->method('getActiveShop')
            ->will($this->returnValue($oShop));

        return $oConfig;
    }


    /**
     * @return PHPUnit_Framework_MockObject_MockObject|oxUtilsDate
     */
    protected function _getUtilsDateMock()
    {
        $oUtilsDate = parent::_getUtilsDateMock();
        $oUtilsDate->expects($this->any())
            ->method('getTime')
            ->willReturn('1234');
        
        return $oUtilsDate;
    }

    /**
     * @group unit
     * @covers ::getInstance()
     */
    public function testCreateInstance()
    {
        $oBestitAmazonPay4OxidClient = new bestitAmazonPay4OxidClient();
        self::assertInstanceOf('bestitAmazonPay4OxidClient', $oBestitAmazonPay4OxidClient);
        self::assertInstanceOf('bestitAmazonPay4OxidClient', bestitAmazonPay4OxidClient::getInstance());
    }

    /**
     * @group unit
     * @covers ::_getAmazonClient()
     * @covers ::_getLogger()
     */
    public function testGetAmazonClient()
    {
        $oConfig = $this->_getConfigMock();
        $oConfig->expects($this->exactly(7))
            ->method('getConfigParam')
            ->withConsecutive(
                array('sAmazonSellerId'),
                array('sAmazonAWSAccessKeyId'),
                array('sAmazonSignature'),
                array('sAmazonLoginClientId'),
                array('sAmazonLocale'),
                array('blAmazonSandboxActive'),
                array('sShopDir')
            )
            ->will($this->returnValue('value'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            null,
            $oConfig,
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertInstanceOf('\AmazonPay\Client', self::callMethod($oBestitAmazonPay4OxidClient, '_getAmazonClient'));
        self::assertInstanceOf('AmazonPay\Client', self::callMethod($oBestitAmazonPay4OxidClient, '_getAmazonClient'));
    }

    /**
     * @group unit
     * @covers ::getAmazonProperty()
     */
    public function testGetAmazonProperty()
    {
        $oConfig = $this->_getConfigMock();
        $oConfig->expects($this->exactly(8))
            ->method('getConfigParam')
            ->withConsecutive(
                array('blAmazonSandboxActive'),
                array('sAmazonLocale'),
                array('blAmazonSandboxActive'),
                array('sAmazonLocale'),
                array('blAmazonSandboxActive'),
                array('sAmazonLocale'),
                array('blAmazonSandboxActive'),
                array('sAmazonLocale')
            )
            ->will($this->onConsecutiveCalls(null, 'DE', null, 'DE', 1, 'DE', null, 'DE'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $this->_getAmazonClientMock(),
            $oConfig,
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertNull($oBestitAmazonPay4OxidClient->getAmazonProperty('some'));
        self::assertEquals(
            'https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js',
            $oBestitAmazonPay4OxidClient->getAmazonProperty('sAmazonLoginWidgetUrl')
        );
        self::assertEquals(
            'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js',
            $oBestitAmazonPay4OxidClient->getAmazonProperty('sAmazonLoginWidgetUrl')
        );
        self::assertEquals(
            'https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js',
            $oBestitAmazonPay4OxidClient->getAmazonProperty('sAmazonLoginWidgetUrl', false)
        );
    }

    /**
     * @group unit
     * @covers ::getOrderReferenceDetails()
     * @covers ::processOrderReference()
     * @covers ::_convertResponse()
     */
    public function testGetOrderReferenceDetailsWithoutOrder()
    {
        $oClient = $this->_getAmazonClientMock();
        $oClient->expects($this->exactly(3))
            ->method('getOrderReferenceDetails')
            ->withConsecutive(
                array(array()),
                array(array('amazon_order_reference_id' => 'referenceId')),
                array(array('amazon_order_reference_id' => 'referenceId', 'address_consent_token' => 'loginToken'))
            )
            ->will($this->returnValue($this->_getAmazonResponseParserMock()));

        $oSession = $this->_getSessionMock();
        $oSession->expects($this->exactly(5))
            ->method('getVariable')
            ->withConsecutive(
                array('amazonOrderReferenceId'),
                array('amazonOrderReferenceId'),
                array('amazonLoginToken'),
                array('amazonOrderReferenceId'),
                array('amazonLoginToken')
            )
            ->will($this->onConsecutiveCalls(
                '',
                'referenceId',
                '',
                'referenceId',
                'loginToken'
            ));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $oSession,
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertEquals($this->_getResponseObject(array()), $oBestitAmazonPay4OxidClient->getOrderReferenceDetails());
        self::assertEquals($this->_getResponseObject(array()), $oBestitAmazonPay4OxidClient->getOrderReferenceDetails());
        self::assertEquals($this->_getResponseObject(array()), $oBestitAmazonPay4OxidClient->getOrderReferenceDetails());
    }

    /**
     * @group unit
     * @covers ::getOrderReferenceDetails()
     * @covers ::processOrderReference()
     * @covers ::_convertResponse()
     * @covers ::authorize()
     */
    public function testGetOrderReferenceDetailsWithOrder()
    {
        $aDefaultResponse = array(
            'GetOrderReferenceDetailsResult' => array(
                'OrderReferenceDetails' => array(
                    'OrderReferenceStatus' => array(
                        'State' => 'state'
                    )
                )
            )
        );

        $aOpenResponse = array(
            'GetOrderReferenceDetailsResult' => array(
                'OrderReferenceDetails' => array(
                    'OrderReferenceStatus' => array(
                        'State' => 'Open'
                    )
                )
            )
        );

        $oClient = $this->_getAmazonClientMock();
        $oClient->expects($this->exactly(5))
            ->method('getOrderReferenceDetails')
            ->withConsecutive(
                array(array('amazon_order_reference_id' => 'referenceId')),
                array(array('amazon_order_reference_id' => 'referenceId', 'extra' => 'extraValue')),
                array(array('amazon_order_reference_id' => 'referenceId')),
                array(array('amazon_order_reference_id' => 'referenceId')),
                array(array('amazon_order_reference_id' => 'referenceId'))
            )
            ->will($this->onConsecutiveCalls(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock($aDefaultResponse),
                $this->_getAmazonResponseParserMock($aDefaultResponse),
                $this->_getAmazonResponseParserMock($aDefaultResponse),
                $this->_getAmazonResponseParserMock($aOpenResponse)
            ));
        $oClient->expects($this->once())
            ->method('authorize')
            ->with(array(
                'amazon_order_reference_id' => 'referenceId',
                'authorization_amount' => 'orderSum',
                'currency_code' => 'currency',
                'authorization_reference_id' => 'referenceId_1234',
                'seller_authorization_note' => 'Authorization%20Order%20#orderNumber',
                'transaction_timeout' => 1440
            ))
            ->will($this->returnValue($this->_getAmazonResponseParserMock()));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderMock();
        $oOrder->expects($this->exactly(13))
            ->method('getFieldData')
            ->withConsecutive(
                array('bestitamazonorderreferenceid'),
                array('bestitamazonorderreferenceid'),
                array('bestitamazonorderreferenceid'),
                array('oxtransstatus'),
                array('bestitamazonorderreferenceid'),
                array('oxtransstatus'),
                array('bestitamazonorderreferenceid'),
                array('oxtransstatus'),
                //authorize
                array('bestitamazonorderreferenceid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr')
            )
            ->will($this->onConsecutiveCalls(
                'referenceId',
                'referenceId',
                'referenceId',
                'transStatus',
                'referenceId',
                'AMZ-Order-Suspended',
                'referenceId',
                'AMZ-Order-Suspended',
                //authorize
                'referenceId',
                'orderSum',
                'currency',
                'referenceId',
                'orderNumber'
            ));

        $oOrder->expects($this->exactly(2))
            ->method('assign')
            ->with(array('oxtransstatus' => 'AMZ-Order-state'));

        $oOrder->expects($this->exactly(2))
            ->method('save');

        self::assertEquals(
            $this->_getResponseObject(array()),
            $oBestitAmazonPay4OxidClient->getOrderReferenceDetails($oOrder)
        );

        self::assertEquals(
            $this->_getResponseObject($aDefaultResponse),
            $oBestitAmazonPay4OxidClient->getOrderReferenceDetails($oOrder, array('extra' => 'extraValue'), true)
        );

        self::assertEquals(
            $this->_getResponseObject($aDefaultResponse),
            $oBestitAmazonPay4OxidClient->getOrderReferenceDetails($oOrder)
        );

        self::assertEquals(
            $this->_getResponseObject($aDefaultResponse),
            $oBestitAmazonPay4OxidClient->getOrderReferenceDetails($oOrder)
        );

        self::assertEquals(
            $this->_getResponseObject($aOpenResponse),
            $oBestitAmazonPay4OxidClient->getOrderReferenceDetails($oOrder)
        );
    }

    /**
     * @group unit
     * @covers ::setOrderReferenceDetails()
     */
    public function testSetOrderReferenceDetails()
    {
        $oClient = $this->_getAmazonClientMock();
        $oClient->expects($this->exactly(2))
            ->method('setOrderReferenceDetails')
            ->withConsecutive(
                array(array('amazon_order_reference_id' => 'referenceId', 'extra' => 'extraValue')),
                array(array(
                    'amazon_order_reference_id' => 'referenceId',
                    'amount' => 1.1,
                    'currency_code' => 'currency',
                    'platform_id' => 'A26EQAZK19E0U2',
                    'store_name' => 'shopName'
                ))
            )
            ->will($this->onConsecutiveCalls(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock()
            ));

        $oSession = $this->_getSessionMock();
        $oSession->expects($this->exactly(2))
            ->method('getVariable')
            ->with('amazonOrderReferenceId')
            ->will($this->returnValue('referenceId'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $oSession,
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->setOrderReferenceDetails(null, array('extra' => 'extraValue'))
        );

        $oBasket = $this->_getBasketMock();

        $oPrice = $this->_getPriceMock();
        $oPrice->expects($this->once())
            ->method('getBruttoPrice')
            ->will($this->returnValue(1.1));

        $oBasket->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($oPrice));

        $oCurrency = new stdClass();
        $oCurrency->name = 'currency';
        $oBasket->expects($this->once())
            ->method('getBasketCurrency')
            ->will($this->returnValue($oCurrency));

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->setOrderReferenceDetails($oBasket)
        );
    }

    /**
     * @group unit
     * @covers ::confirmOrderReference()
     */
    public function testConfirmOrderReference()
    {
        $oClient = $this->_getAmazonClientMock();
        $oClient->expects($this->once())
            ->method('confirmOrderReference')
            ->withConsecutive(
                array(array('amazon_order_reference_id' => 'referenceId', 'extra' => 'extraValue'))
            )
            ->will($this->returnValue($this->_getAmazonResponseParserMock()));

        $oSession = $this->_getSessionMock();
        $oSession->expects($this->once())
            ->method('getVariable')
            ->with('amazonOrderReferenceId')
            ->will($this->returnValue('referenceId'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $oSession,
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->confirmOrderReference(array('extra' => 'extraValue'))
        );
    }

    /**
     * @param string $sFunctionUnderTest
     * @param array  $aParameters
     * @param array  $aResponses
     *
     * @return Client|PHPUnit_Framework_MockObject_MockObject
     */
    private function _getOrderRequestClientMock($sFunctionUnderTest, array $aParameters, array $aResponses)
    {
        $oClient = $this->_getAmazonClientMock();
        $oMethod = $oClient->expects($this->exactly(count($aResponses)))
            ->method($sFunctionUnderTest)
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $aResponses));
        call_user_func_array(array($oMethod, 'withConsecutive'), $aParameters);

        return $oClient;
    }

    /**
     * @param array $aAssigns
     * @param array $aFieldData
     *
     * @return oxOrder|PHPUnit_Framework_MockObject_MockObject
     */
    private function _getOrderRequestOrderMock(array $aAssigns, array $aFieldData)
    {
        $oOrder = $this->_getOrderMock();
        $oMethod = $oOrder->expects($this->exactly(count($aAssigns)))
            ->method('assign');
        call_user_func_array(array($oMethod, 'withConsecutive'), $aAssigns);

        $oMethod = $oOrder->expects($this->exactly(count($aFieldData)))
            ->method('getFieldData')
            ->will($this->returnCallback(function ($sValue) {
                return $sValue.'Value';
            }));
        call_user_func_array(array($oMethod, 'withConsecutive'), $aFieldData);

        $oOrder->expects($this->exactly(count($aAssigns)))
            ->method('save')
            ->will($this->returnValue(true));

        return $oOrder;
    }

    /**
     * @param string $sFunctionUnderTest
     * @param string $sAmazonStatus
     */
    private function _cancelCloseOrderTestBase($sFunctionUnderTest, $sAmazonStatus)
    {
        $oClient = $this->_getOrderRequestClientMock(
            $sFunctionUnderTest,
            array(
                array(array('extra' => 'extraValue')),
                array(array('amazon_order_reference_id' => 'bestitamazonorderreferenceidValue', 'extra' => 'extraValue')),
                array(array('amazon_order_reference_id' => 'bestitamazonorderreferenceidValue', 'extra' => 'extraValue'))
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock()
            )
        );

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(array('oxtransstatus' => $sAmazonStatus))
            ),
            array(
                array('bestitamazonorderreferenceid'),
                array('bestitamazonorderreferenceid')
            )
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->{$sFunctionUnderTest}(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->{$sFunctionUnderTest}($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->{$sFunctionUnderTest}($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::cancelOrderReference()
     */
    public function testCancelOrderReference()
    {
        $this->_cancelCloseOrderTestBase('cancelOrderReference', 'AMZ-Order-Canceled');
    }

    /**
     * @group unit
     * @covers ::closeOrderReference()
     */
    public function testCloseOrderReference()
    {
        $this->_cancelCloseOrderTestBase('cancelOrderReference', 'AMZ-Order-Canceled');
    }

    /**
     * @group unit
     * @covers ::closeAuthorization()
     */
    public function testCloseAuthorization()
    {
        $aParams = array(
            'amazon_authorization_id' => 'bestitamazonauthorizationidValue',
            'closure_reason' => 'Authorization%20Close%20Order%20#oxordernrValue',
            'extra' => 'extraValue'
        );

        $oClient = $this->_getOrderRequestClientMock(
            'closeAuthorization',
            array(
                array(array('extra' => 'extraValue')),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock()
            )
        );

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(array('oxtransstatus' => 'AMZ-Authorize-Closed'))
            ),
            array(
                array('bestitamazonauthorizationid'),
                array('oxordernr'),
                array('bestitamazonauthorizationid'),
                array('oxordernr')
            )
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->closeAuthorization(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->closeAuthorization($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->closeAuthorization($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::authorize()
     */
    public function testAuthorize()
    {
        $aAuthorizeResult = array(
            'AuthorizeResult' => array(
                'AuthorizationDetails' => array(
                    'AmazonAuthorizationId' => 'AmazonAuthorizationIdValue',
                    'AuthorizationStatus' => array(
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $aParams = array(
            'amazon_order_reference_id' => 'bestitamazonorderreferenceidValue',
            'extra' => 'extraValue',
            'transaction_timeout' => 1440,
            'authorization_amount' => 'oxtotalordersumValue',
            'currency_code' => 'oxcurrencyValue',
            'authorization_reference_id' => 'bestitamazonorderreferenceidValue_1234',
            'seller_authorization_note' => 'Authorization%20Order%20#oxordernrValue'
        );

        $oClient = $this->_getOrderRequestClientMock(
            'authorize',
            array(
                array(array('extra' => 'extraValue', 'transaction_timeout' => 1440)),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock($aAuthorizeResult)
            )
        );

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(
                    array('oxtransstatus' => 'AMZ-Error-errorCode')
                ),
                array(
                    array(
                        'bestitamazonauthorizationid' => 'AmazonAuthorizationIdValue',
                        'oxtransstatus' => 'AMZ-Authorize-StateValue'
                    )
                )
            ),
            array(
                array('bestitamazonorderreferenceid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonorderreferenceid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr')
            )
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->authorize(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->authorize($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aAuthorizeResult),
            $oBestitAmazonPay4OxidClient->authorize($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::getAuthorizationDetails()
     * @covers ::processAuthorization()
     */
    public function testGetAuthorizationDetails()
    {
        $blAmazonCapture = false;
        $blAmazonMode = false;

        $oConfig = $this->_getConfigMock();
        $oConfig->expects($this->any())
            ->method('getConfigParam')
            ->withConsecutive(
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('sAmazonCapture'),
                array('blAmazonSandboxActive'),
                array('sAmazonCapture'),
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('sAmazonMode'),
                array('blAmazonSandboxActive'),
                array('sAmazonMode'),
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('sAmazonMode'),
                array('blAmazonSandboxActive'),
                array('sAmazonMode'),
                array('blAmazonSandboxActive'),
                array('blAmazonSandboxActive'),
                array('sAmazonMode')
            )
            ->will($this->returnCallback(function ($sValue) use (&$blAmazonCapture, &$blAmazonMode) {
                if ($sValue === 'sAmazonCapture') {
                    if ($blAmazonCapture === false) {
                        $blAmazonCapture = true;
                        return 'some';
                    }

                    return 'DIRECT';
                } elseif ($sValue === 'sAmazonMode') {
                    if ($blAmazonMode === false) {
                        $blAmazonMode = true;
                        return 'some';
                    }

                    return 'Async';
                }

                return false;
            }));

        $aGetAuthorizationDetailsResult = array(
            'GetAuthorizationDetailsResult' => array(
                'AuthorizationDetails' => array(
                    'AuthorizationStatus' => array(
                        'ReasonCode' => 'ReasonCodeValue',
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $aGetAuthorizationDetailsResultOpen = array(
            'GetAuthorizationDetailsResult' => array(
                'AuthorizationDetails' => array(
                    'AuthorizationStatus' => array(
                        'ReasonCode' => 'ReasonCodeValue',
                        'State' => 'Open'
                    )
                )
            )
        );

        $aGetAuthorizationDetailsResultDeclinedDefault = array(
            'GetAuthorizationDetailsResult' => array(
                'AuthorizationDetails' => array(
                    'AuthorizationStatus' => array(
                        'ReasonCode' => 'ReasonCodeValue',
                        'State' => 'Declined'
                    )
                )
            )
        );

        $aGetAuthorizationDetailsResultDeclinedInvalidPaymentMethod = array(
            'GetAuthorizationDetailsResult' => array(
                'AuthorizationDetails' => array(
                    'AuthorizationStatus' => array(
                        'ReasonCode' => 'InvalidPaymentMethod',
                        'State' => 'Declined'
                    )
                )
            )
        );

        $aGetAuthorizationDetailsResultDeclinedAmazonRejected = array(
            'GetAuthorizationDetailsResult' => array(
                'AuthorizationDetails' => array(
                    'AuthorizationStatus' => array(
                        'ReasonCode' => 'AmazonRejected',
                        'State' => 'Declined'
                    )
                )
            )
        );

        $aParams = array(
            'extra' => 'extraValue',
            'amazon_authorization_id' => 'bestitamazonauthorizationidValue'
        );

        $oClient = $this->_getOrderRequestClientMock(
            'getAuthorizationDetails',
            array(
                array(array('extra' => 'extraValue')),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResult),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultOpen),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultOpen),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultDeclinedDefault),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultDeclinedDefault),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultDeclinedInvalidPaymentMethod),
                $this->_getAmazonResponseParserMock($aGetAuthorizationDetailsResultDeclinedAmazonRejected)
            )
        );

        $oClient->expects($this->once())
            ->method('capture')
            ->will($this->returnValue($this->_getAmazonResponseParserMock()));
        $oClient->expects($this->exactly(2))
            ->method('closeOrderReference')
            ->will($this->returnValue($this->_getAmazonResponseParserMock()));

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(array('oxtransstatus' => 'AMZ-Error-errorCode')),
                array(array('oxtransstatus' => 'AMZ-Authorize-StateValue')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Open')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Open')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Declined')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Declined')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Declined')),
                array(array('oxtransstatus' => 'AMZ-Authorize-Declined'))
            ),
            array(
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonorderreferenceid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonauthorizationid'),
                array('bestitamazonorderreferenceid')
            )
        );

        $oEmail = $this->_getExtendedEmailMock();
        $oEmail->expects($this->once())
            ->method('sendAmazonInvalidPaymentEmail')
            ->with($oOrder);
        $oEmail->expects($this->once())
            ->method('sendAmazonRejectedPaymentEmail')
            ->with($oOrder);

        $objectFactory = $this->_getObjectFactoryMock();
        $objectFactory->expects($this->exactly(2))
            ->method('createOxidObject')
            ->with('oxEmail')
            ->will($this->returnCallback(function () use(&$oEmail) {
                return $oEmail;
            }));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $oConfig,
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $objectFactory
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResult),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultOpen),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultOpen),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultDeclinedDefault),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultDeclinedDefault),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultDeclinedInvalidPaymentMethod),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aGetAuthorizationDetailsResultDeclinedAmazonRejected),
            $oBestitAmazonPay4OxidClient->getAuthorizationDetails($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::capture()
     * @covers ::setCaptureState()
     */
    public function testCapture()
    {
        $aCaptureResult = array(
            'CaptureResult' => array(
                'CaptureDetails' => array(
                    'AmazonCaptureId' => 'AmazonCaptureIdValue',
                    'CaptureStatus' => array(
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $aCaptureCompletedResult = array(
            'CaptureResult' => array(
                'CaptureDetails' => array(
                    'AmazonCaptureId' => 'AmazonCaptureIdValue',
                    'CaptureStatus' => array(
                        'State' => 'Completed'
                    )
                )
            )
        );

        $aParams = array(
            'amazon_authorization_id' => 'bestitamazonauthorizationidValue',
            'extra' => 'extraValue',
            'currency_code' => 'oxcurrencyValue',
            'capture_amount' => 'oxtotalordersumValue',
            'capture_reference_id' => 'bestitamazonorderreferenceidValue_1234',
            'seller_capture_note' => 'shopName orderNumber: oxordernrValue'
        );

        $oClient = $this->_getOrderRequestClientMock(
            'capture',
            array(
                array(array(
                    'extra' => 'extraValue'
                )),
                array($aParams),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock($aCaptureResult),
                $this->_getAmazonResponseParserMock($aCaptureCompletedResult)
            )
        );

        $oLanguage = $this->_getLanguageMock();
        $oLanguage->expects($this->exactly(3))
            ->method('translateString')
            ->with('BESTITAMAZONPAY_ORDER_NO')
            ->will($this->returnValue('orderNumber'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $oLanguage,
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(
                    array('oxtransstatus' => 'AMZ-Error-errorCode')
                ),
                array(
                    array(
                        'oxtransstatus' => 'AMZ-Capture-StateValue',
                        'bestitamazoncaptureid' => 'AmazonCaptureIdValue'
                    )
                ),
                array(
                    array(
                        'oxtransstatus' => 'AMZ-Capture-Completed',
                        'bestitamazoncaptureid' => 'AmazonCaptureIdValue',
                        'oxpaid' => '1970-01-01 01:20:34'
                    )
                )
            ),
            array(
                array('bestitamazonauthorizationid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonauthorizationid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonauthorizationid'),
                array('oxtotalordersum'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr')
            )
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->capture(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->capture($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aCaptureResult),
            $oBestitAmazonPay4OxidClient->capture($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aCaptureCompletedResult),
            $oBestitAmazonPay4OxidClient->capture($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::getCaptureDetails()
     * @covers ::setCaptureState()
     */
    public function testGetCaptureDetails()
    {
        $aCaptureResult = array(
            'GetCaptureDetailsResult' => array(
                'CaptureDetails' => array(
                    'AmazonCaptureId' => 'AmazonCaptureIdValue',
                    'CaptureStatus' => array(
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $aCaptureCompletedResult = array(
            'GetCaptureDetailsResult' => array(
                'CaptureDetails' => array(
                    'AmazonCaptureId' => 'AmazonCaptureIdValue',
                    'CaptureStatus' => array(
                        'State' => 'Completed'
                    )
                )
            )
        );

        $aParams = array(
            'amazon_capture_id' => 'bestitamazoncaptureidValue',
            'extra' => 'extraValue'
        );

        $oClient = $this->_getOrderRequestClientMock(
            'getCaptureDetails',
            array(
                array(array('extra' => 'extraValue')),
                array($aParams),
                array($aParams),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode'))),
                $this->_getAmazonResponseParserMock($aCaptureResult),
                $this->_getAmazonResponseParserMock($aCaptureCompletedResult),
                $this->_getAmazonResponseParserMock($aCaptureCompletedResult)
            )
        );

        $oLanguage = $this->_getLanguageMock();
        $oLanguage->expects($this->exactly(4))
            ->method('translateString')
            ->with('BESTITAMAZONPAY_ORDER_NO')
            ->will($this->returnValue('orderNumber'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $oLanguage,
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(
                array(
                    array('oxtransstatus' => 'AMZ-Error-errorCode')
                ),
                array(
                    array(
                        'oxtransstatus' => 'AMZ-Capture-StateValue',
                        'bestitamazoncaptureid' => 'AmazonCaptureIdValue'
                    )
                ),
                array(
                    array(
                        'oxtransstatus' => 'AMZ-Capture-Completed',
                        'bestitamazoncaptureid' => 'AmazonCaptureIdValue',
                        'oxpaid' => '1970-01-01 01:20:34'
                    )
                ),
                array(
                    array(
                        'oxtransstatus' => 'AMZ-Capture-Completed',
                        'bestitamazoncaptureid' => 'AmazonCaptureIdValue',
                        'oxpaid' => '1970-01-01 01:20:34'
                    )
                )
            ),
            array(
                array('bestitamazoncaptureid'),
                array('bestitamazoncaptureid'),
                array('bestitamazoncaptureid'),
                array('oxpaid'),
                array('bestitamazoncaptureid'),
                array('oxpaid')
            )
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->getCaptureDetails(null, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode'))),
            $oBestitAmazonPay4OxidClient->getCaptureDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aCaptureResult),
            $oBestitAmazonPay4OxidClient->getCaptureDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aCaptureCompletedResult),
            $oBestitAmazonPay4OxidClient->getCaptureDetails($oOrder, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aCaptureCompletedResult),
            $oBestitAmazonPay4OxidClient->getCaptureDetails($oOrder, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::refund()
     */
    public function testRefund()
    {
        $aRefundResult = array(
            'RefundResult' => array(
                'RefundDetails' => array(
                    'AmazonRefundId' => 'AmazonCaptureIdValue',
                    'RefundStatus' => array(
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $aParams = array(
            'amazon_capture_id' => 'bestitamazoncaptureidValue',
            'extra' => 'extraValue',
            'refund_amount' => 1.0,
            'currency_code' => 'oxcurrencyValue',
            'refund_reference_id' => 'bestitamazonorderreferenceidValue_1234',
            'seller_refund_note' => 'Refund%20Order%20#oxordernrValue',
        );

        $oClient = $this->_getOrderRequestClientMock(
            'refund',
            array(
                array(array('extra' => 'extraValue')),
                array($aParams),
                array($aParams)
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode', 'Message' => 'errorMessage'))),
                $this->_getAmazonResponseParserMock($aRefundResult)
            )
        );

        $oDatabase = $this->_getDatabaseMock();
        $oDatabase->expects($this->exactly(10))
            ->method('quote')
            ->will($this->returnCallback(function ($sValue) {
                return "'{$sValue}'";
            }));

        $oDatabase->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive(
                array(new MatchIgnoreWhitespace("
                    INSERT bestitamazonrefunds SET 
                      ID = 'bestitamazonorderreferenceidValue_1234',
                      OXORDERID = 'orderId',
                      BESTITAMAZONREFUNDID = '',
                      AMOUNT = 1,
                      STATE = 'Error',
                      ERROR = 'errorMessage',
                      TIMESTAMP = NOW()
                ")),
                array(new MatchIgnoreWhitespace("
                    INSERT bestitamazonrefunds SET 
                      ID = 'bestitamazonorderreferenceidValue_1234',
                      OXORDERID = 'orderId',
                      BESTITAMAZONREFUNDID = 'AmazonCaptureIdValue',
                      AMOUNT = 1,
                      STATE = 'StateValue',
                      ERROR = '',
                      TIMESTAMP = NOW()
                "))
            );

        $oLanguage = $this->_getLanguageMock();
        $oLanguage->expects($this->exactly(2))
            ->method('translateString')
            ->with('BESTITAMAZONPAY_ORDER_NO')
            ->will($this->returnValue('orderNumber'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $oDatabase,
            $oLanguage,
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        $oOrder = $this->_getOrderRequestOrderMock(
            array(),
            array(
                array('bestitamazoncaptureid'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonorderreferenceid'),
                array('bestitamazoncaptureid'),
                array('oxcurrency'),
                array('bestitamazonorderreferenceid'),
                array('oxordernr'),
                array('bestitamazonorderreferenceid')
            )
        );
        $oOrder->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue('orderId'));

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->refund(null, 1.0, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode', 'Message' => 'errorMessage'))),
            $oBestitAmazonPay4OxidClient->refund($oOrder, 1.0, array('extra' => 'extraValue'))
        );
        self::assertEquals(
            $this->_getResponseObject($aRefundResult),
            $oBestitAmazonPay4OxidClient->refund($oOrder, 1.0, array('extra' => 'extraValue'))
        );
    }

    /**
     * @group unit
     * @covers ::getRefundDetails()
     * @covers ::updateRefund()
     */
    public function testGetRefundDetails()
    {
        $aRefundResult = array(
            'GetRefundDetailsResult' => array(
                'RefundDetails' => array(
                    'AmazonRefundId' => 'AmazonCaptureIdValue',
                    'RefundStatus' => array(
                        'State' => 'StateValue'
                    )
                )
            )
        );

        $oClient = $this->_getOrderRequestClientMock(
            'getRefundDetails',
            array(
                array(array('amazon_refund_id' => 'refundId')),
                array(array('amazon_refund_id' => 'refundId')),
                array(array('amazon_refund_id' => 'refundId'))
            ),
            array(
                $this->_getAmazonResponseParserMock(),
                $this->_getAmazonResponseParserMock(array('Error' => array('Code' => 'errorCode', 'Message' => 'errorMessage'))),
                $this->_getAmazonResponseParserMock($aRefundResult)
            )
        );

        $oDatabase = $this->_getDatabaseMock();
        $oDatabase->expects($this->exactly(6))
            ->method('quote')
            ->will($this->returnCallback(function ($sValue) {
                return "'{$sValue}'";
            }));

        $oDatabase->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive(
                array(new MatchIgnoreWhitespace("
                    UPDATE bestitamazonrefunds SET
                      `STATE` = 'Error',
                      `ERROR` = 'errorMessage',
                      `TIMESTAMP` = NOW()
                    WHERE `BESTITAMAZONREFUNDID` = 'refundId'
                ")),
                array(new MatchIgnoreWhitespace("
                    UPDATE bestitamazonrefunds SET
                      `STATE` = 'StateValue',
                      `ERROR` = '',
                      `TIMESTAMP` = NOW()
                    WHERE `BESTITAMAZONREFUNDID` = 'refundId'
                "))
            );

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $oDatabase,
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertEquals(
            $this->_getResponseObject(),
            $oBestitAmazonPay4OxidClient->getRefundDetails('refundId')
        );
        self::assertEquals(
            $this->_getResponseObject(array('Error' => array('Code' => 'errorCode', 'Message' => 'errorMessage'))),
            $oBestitAmazonPay4OxidClient->getRefundDetails('refundId')
        );
        self::assertEquals(
            $this->_getResponseObject($aRefundResult),
            $oBestitAmazonPay4OxidClient->getRefundDetails('refundId')
        );
    }

    /**
     * @group unit
     * @covers ::processAmazonLogin()
     */
    public function testProcessAmazonLogin()
    {
        $oClient = $this->_getAmazonClientMock();
        $oClient->expects($this->once())
            ->method('getUserInfo')
            ->with('accessToken')
            ->will($this->returnValue('getUserInfoResult'));

        $oBestitAmazonPay4OxidClient = $this->_getObject(
            $oClient,
            $this->_getConfigMock(),
            $this->_getDatabaseMock(),
            $this->_getLanguageMock(),
            $this->_getSessionMock(),
            $this->_getUtilsDateMock(),
            $this->_getObjectFactoryMock()
        );

        self::assertEquals('getUserInfoResult', $oBestitAmazonPay4OxidClient->processAmazonLogin('accessToken'));
    }
}
