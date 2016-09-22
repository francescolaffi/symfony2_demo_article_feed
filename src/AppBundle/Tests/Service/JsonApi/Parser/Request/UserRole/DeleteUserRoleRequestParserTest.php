<?php

namespace AppBundle\Tests\Service\JsonApi\Parser\Request\UserRole;


use AppBundle\Service\JsonApi\Deserializer\JsonRequestDeserializer;
use AppBundle\Service\JsonApi\Parser\Request\UserRole\DeleteUserRoleRequestParser;
use Symfony\Component\HttpFoundation\Request;

class DeleteUserRoleRequestParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $payLoad = new \stdClass();
        $payLoad->data = new \stdClass();

        /** @var Request $request */
        $request = self::prophesize(Request::class);
        $request->getContent()->willReturn(json_encode($payLoad));

        $parser = new DeleteUserRoleRequestParser(new JsonRequestDeserializer());

        $parsedRequest = $parser->parse($request->reveal());

        self::markTestIncomplete('PayLoad is not complete, response is not complete');
    }
}
