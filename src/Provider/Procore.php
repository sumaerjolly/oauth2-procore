<?php

namespace SumaerJolly\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Sumaerjolly\OAuth2\Client\Provider\ProcoreResourceOwner;


class Procore extends AbstractProvider
{
  const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';

  /**
   * Constructs an OAuth 2.0 service provider.
   *
   * @param array $options An array of options to set on this provider.
   *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
   *     Individual providers may introduce more options, as needed.
   * @param array $collaborators An array of collaborators that may be used to
   *     override this provider's default behavior. Collaborators include
   *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
   *     Individual providers may introduce more collaborators, as needed.
   */
  public function __construct(array $options = [], array $collaborators = [])
  {
    parent::__construct($options, $collaborators);
  }

  public function getBaseAuthorizationUrl()
  {
    return 'https://login-sandbox.procore.com/oauth/authorize';
  }

  public function getBaseAccessTokenUrl(array $params)
  {
    return 'https://login-sandbox.procore.com/oauth/token';
  }

  public function getResourceOwnerDetailsUrl(AccessToken $token)
  {
    return 'https://api.procore.com/rest/v1.0/me';
  }

  public function getDefaultScopes()
  {
    return [];
  }

  protected function getScopeSeparator()
  {
    return ' ';
  }

  public function checkResponse(ResponseInterface $response, $data)
  {
    if (!empty($data['errors'])) {
      throw new IdentityProviderException($data['errors'], 0, $data);
    }

    return $data;
  }

  protected function createResourceOwner(array $response, AccessToken $token)
  {
    return new ProcoreResourceOwner($response);
  }

  /**
   * Returns a prepared request for requesting an access token.
   *
   * @param array $params Query string parameters
   * @return Psr\Http\Message\RequestInterface
   */
  protected function getAccessTokenRequest(array $params)
  {
    $request = parent::getAccessTokenRequest($params);
    $uri = $request->getUri()
      ->withUserInfo($this->clientId, $this->clientSecret);

    return $request->withUri($uri);
  }
}
