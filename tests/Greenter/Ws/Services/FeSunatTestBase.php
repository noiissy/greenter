<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 19/07/2017
 * Time: 21:16
 */

namespace Tests\Greenter\Ws\Services;

use Greenter\Model\Response\BillResult;
use Greenter\Model\Response\CdrResponse;
use Greenter\Model\Response\SummaryResult;
use Greenter\Ws\Services\ExtService;
use Greenter\Ws\Services\SenderInterface;
use Greenter\Ws\Services\WsClientInterface;

/**
 * Class FeSunatTestBase
 * @package Tests\Greenter\Ws\Services
 */
abstract class FeSunatTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ExtService
     */
    protected function getStatusSender()
    {
        $stub = $this->getMockBuilder(WsClientInterface::class)
                    ->getMock();

        $stub->method('call')
                ->will($this->returnCallback(function ($func, $params) {
                    if ($func !== 'getStatus') {
                        throw new \Exception('Method Not Found');
                    }

                    $obj = new \stdClass();
                    $obj->status = new \stdClass();
                    $obj->status->statusCode = '0';
                    $obj->status->content = file_get_contents(__DIR__.'/../../Resources/cdrBaja.zip');

                    return $obj;
                }));

        /**@var $stub WsClientInterface */
        $sunat = new ExtService();
        $sunat->setClient($stub);

        return $sunat;
    }

    /**
     * @return SenderInterface
     */
    protected function getBillSender()
    {
        $stub = $this->getMockBuilder(SenderInterface::class)
            ->getMock();
        $stub->method('send')->will($this->returnValue((new BillResult())
            ->setCdrResponse((new CdrResponse())
                ->setCode('0')
                ->setDescription('La Factura numero F001-00000001, ha sido aceptada')
                ->setId('F001-00000001'))
            ->setSuccess(true)
        ));

        /**@var $stub SenderInterface*/
        return $stub;
    }

    /**
     * @return SenderInterface
     */
    protected function getSummarySender()
    {
        $stub = $this->getMockBuilder(SenderInterface::class)
            ->getMock();
        $stub->method('send')->will($this->returnValue((new SummaryResult())
            ->setTicket('1500523236696')
            ->setSuccess(true)
        ));

        /**@var $stub SenderInterface*/
        return $stub;
    }
}