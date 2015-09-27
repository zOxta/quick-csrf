<?php

namespace Zoxta\Csrf;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

/**
 * Class JwtCsrfToken
 */
class JwtCsrfToken
{
    /**
     * @var string
     */
    private $jwtSecret;

    /**
     * @var string
     */
    public $receivedToken;

    /**
     * @var \Lcobucci\JWT\Token
     */
    private $generatedToken;

    /**
     * @var Sha256
     */
    private $signer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var string
     */
    private $audience;

    /**
     * @var ValidationData
     */
    private $validator;

    /**
     * JwtCsrfToken constructor.
     *
     * @param string $receivedToken
     */
    function __construct(
        $receivedToken = ''
    )
    {

        $this->signer         = new Sha256();

        $this->parser         = new Parser() and $this->set($receivedToken);

        $this->issuer         = $this->audience = 'http://' . $_SERVER['HTTP_HOST'];

        $this->validator      = new ValidationData();

        $this->jwtSecret      = defined('JWT_SECRET') ? JWT_SECRET : uniqid('UH(&*G^(F&%d86udtV#HoIHVKIYURtfgi652857');

        $this->generatedToken =
            (new Builder())->setIssuer($this->issuer) // Configures the issuer (iss claim)
                ->setAudience($this->audience) // Configures the audience (aud claim)
                ->setId('vVv', true) // Configures the id (jti claim), replicating as a header item
                ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                ->setNotBefore(time() + 1) // Configures the time that the token can be used (nbf claim)
                ->setExpiration(time() + 360000) // Configures the expiration time of the token (exp claim)
                ->sign($this->signer, JWT_SECRET) // creates a signature using JWT_SECRET as key
                ->getToken(); // Retrieves the generated token
    }

    /**
     * Return the JWT CSRF token
     *
     * @return string
     */
    function __toString()
    {
        return (string) $this->generatedToken;
    }

    /**
     * Echo a hidden form field containing the JWT CSRF token
     *
     * @return string
     */
    public function field()
    {
        return '<input type="hidden" name="_token" value="' . $this . '" />';
    }

    /**
     * Check if a JWT in POST _token is valid
     * @return bool
     */
    public function isValid()
    {
        try {

            return $this->validate() and $this->receivedToken->verify($this->signer, JWT_SECRET);

        } catch (Exception $e) {

            return false;

        }
    }

    /**
     * Check if a JWT in POST _token is invalid
     * @return bool
     */
    public function isInvalid()
    {
        return ! $this->isValid();
    }

    /**
     * Validate the CSRF token
     *
     * @return bool
     */
    function validate()
    {

        if ($this->receivedToken == '') {
            return false;
        }

        try {
            $this->validator->setIssuer($this->issuer);
            $this->validator->setAudience($this->audience);
            $this->validator->setId('vVv');

            return $this->receivedToken->validate($this->validator);

        } catch (Exception $e) {

            return false;

        }
    }

    /**
     * Set the CSRF token received from request or override by passing one.
     *
     * @param bool|false $token
     *
     * @return bool|\Lcobucci\JWT\Token|string
     */
    public function set($token = false)
    {
        try {

            $this->receivedToken = ! empty($token) ? $this->parser->parse((string) $token) :
                (! empty($_POST['_token']) ? $this->parser->parse((string) $_POST['_token']) : false);

        } catch (Exception $e) {

            $this->receivedToken = false;

        }
    }
}