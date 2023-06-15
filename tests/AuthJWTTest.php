<?php

namespace Bx\Router\Tests;

use Bx\JWT\Strategy\RS256TokenStrategy;
use Bx\JWT\UserDataPacker;
use Bx\JWT\UserTokenService;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use Bx\Model\Models\User;
use BX\Router\Exceptions\ForbiddenException;
use BX\Router\Exceptions\UnauthorizedException;
use BX\Router\Middlewares\AuthJWT;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use GuzzleHttp\Psr7\Response;

class AuthJWTTest extends TestCase
{
    /**
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function testProcess()
    {
        $mockedUserService = $this->createMock(UserServiceInterface::class);
        $mockedUserService
            ->method('getById')
            ->willReturnCallback(function (int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel {
                if ($id > 0) {
                    return new User(['ID' => $id]);
                }
                return null;
            });

        $privateKey = <<<TXT
-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDKNRn3vVfIQllu
ya7ilgXDNXdg7rSYeK4Bw+xwpO0Dq8ACTjTV+P8pWn9S+RLNwTn1ylwJsQgNXlUP
KIRtMN+/g0gp2nyoSxbC9vNxNFy9h3Eknjk+S9+0PClKIRf22ziYcrf7bxHIuiJ5
t0Kx8Jd5R8kpoOVIAjmvJskd+xFAHtIB2kv1OYd3mMio3e6O3DlAup6AZBv5UwG8
jxP1ccdl5r4CY5z0Oa0Z4yzsMNgiRZrmr8DiviMI/BUGQnyEVsr+usU5Agp4HmAZ
z3SCChNambZE5JbiIT9s14IrZyfId0Y/k0N8jr3EjzFxSB6O0XGio1ZqCaDucXb0
QKMOuRHvAgMBAAECggEBALIEl+z8W+jpSNT5aCcBjucyDfxfiszlCcRyGZl+CU//
B9a/xVJPmUxt6MRClaRhJcbXNbouiflDAD2NHTEIIyP2zzRRrwd2u/4+7UnPoIoX
UKu9RIYsxDBn+q3RfsiMbUIBVSpduVlvTWS8y1Hej2xtn5MvnEuN8fzad5sWazR3
nKNIizgortzhFszUikT9sYoMfdrvrEJTEpwS+LA/3CafXl//I1AfehP6RYEPXJpE
ncJ4bP1CYFT+Y1pGgfzP084TvWsZy1zCyoUguHHggXpcBiaOx1ZFDxz+DH5RxjpH
ZfEwWVe+M+vIRC5kULnO5OICAy1fNYezIov+or3WyiECgYEA76N/HUU7PXecPpaz
Bze1M59k4Ti7p0/noTR2GQiga1CJuDup6xsMpbfJryvsTQ3edHE9x9sKcQfyK/nC
ZOzLRVo3tm6YZqCGToP/CMp3UDsK3lDxc+/KFqpGEoKurwOhSjru+/lg12K//KQ2
7Agbj1EFrQX4wtX907XfiSceIfECgYEA2ANdyyQQoIFOg4pEHBNwoQcGl9R4zUAa
HBqfH8y9kavKzDa8BdNlPErF2iZHXd9+2RT/dz28+rKhOQ7MoPayWyr8O9XlBFAT
K9bRtHesF1+DxEEvN9BCIXYDCcsdZIgYaxKssIIu2LP0ZamZqjDZDqvVUps/Ll18
8N9TQk5Akd8CgYEA0bLruCxuA4LYifiLn0RDX3Ia7+8aXvXQRsPGmG9xGZd3fG1R
arfX4GgsqAoylNcPFCxIGMx3naOLaqn7Tx/bXMvutsieuie8f5aIJSQvNlkEULja
IU7zM9Q6z1XmEKaHjJJ3sK1v7eqvACCfIvqRS19mLRttrOlfXdToUHeXqbECgYEA
iS56HkwlEwmLZxYj8wCVgm4Hzdxta0vOSRLPA07vBNfozo/kEH8Tx5pk1AmDQSZy
VEJ1irB2l29h2+5+HasN0cAWt5k6++YuhqTCQK3PaHiMIdKTvgpQNRfRDiMj43ha
qYUOjnnebli6WOXAZMjoz9xoeTGGildsxTvOkElJ0FkCgYBSs3gJMYAFBB1beI2b
/5MJW0VyBz8kDqZfKePwd16n5KWk5P/AmyWxZE5fnjdpkFhkRIE91uvi5YiyDrH9
4HkGDZpZyrRDX6lK27bMqAVag2NpIkrxO/MojM372xaLkt4gQTeeP55kSEsFEsSU
HU32MFOtskpGSLwvpMQ21noyrw==
-----END PRIVATE KEY-----
TXT;


        $publicKey = <<<TXT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyjUZ971XyEJZbsmu4pYF
wzV3YO60mHiuAcPscKTtA6vAAk401fj/KVp/UvkSzcE59cpcCbEIDV5VDyiEbTDf
v4NIKdp8qEsWwvbzcTRcvYdxJJ45PkvftDwpSiEX9ts4mHK3+28RyLoiebdCsfCX
eUfJKaDlSAI5rybJHfsRQB7SAdpL9TmHd5jIqN3ujtw5QLqegGQb+VMBvI8T9XHH
Zea+AmOc9DmtGeMs7DDYIkWa5q/A4r4jCPwVBkJ8hFbK/rrFOQIKeB5gGc90ggoT
Wpm2ROSW4iE/bNeCK2cnyHdGP5NDfI69xI8xcUgejtFxoqNWagmg7nF29ECjDrkR
7wIDAQAB
-----END PUBLIC KEY-----
TXT;

        $tokenService = new UserTokenService(
            new RS256TokenStrategy($privateKey, $publicKey),
            new UserDataPacker(86400, $mockedUserService),
            $mockedUserService
        );

        $authJWT = new AuthJWT('Authorization', $tokenService);
        $jwtKey = (string) $tokenService->createToken(2);
        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Authorization' => "Bearer $jwtKey"
            ],
            null,
            '1.1'
        );

        $mockedHandler = $this->createMock(RequestHandlerInterface::class);
        $mockedHandler->expects($this->any())->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request): ResponseInterface {
                $userAttribute = $request->getAttribute('user');
                $response = new Response(200, [], 'test');
                $response->getBody()->write(json_encode([
                    'hasUserContext' => !empty($userAttribute)
                ]));
                return $response;
            });

        $resultResponse = $authJWT->process($request, $mockedHandler);
        $bodyData = $this->getDataFromResponse($resultResponse);
        $this->assertTrue($bodyData['hasUserContext'] ?? false);

        $request = new ServerRequest('GET', '/test', [], null, '1.1');
        $resultResponse = $authJWT->process($request, $mockedHandler);
        $bodyData = $this->getDataFromResponse($resultResponse);
        $this->assertFalse($bodyData['hasUserContext'] ?? false);

        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Authorization' => "Bearer {$jwtKey}_fake"
            ],
            null,
            '1.1'
        );

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Signature verification failed');
        $authJWT->process($request, $mockedHandler);

    }

    private function getDataFromResponse(ResponseInterface $response): array
    {
        $rawBody = (string) $response->getBody();
        return json_decode($rawBody, true);
    }
}
