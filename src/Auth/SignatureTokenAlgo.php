<?php

namespace Codeages\Weblib\Auth;

use Codeages\Weblib\Error\ErrorCode;

class SignatureTokenAlgo implements TokenAlgo
{
    public function parse($tokenValue)
    {
        $tokenValue = explode(':', $tokenValue);
        if (count($tokenValue) !== 2 || empty($tokenValue[0]) || empty($tokenValue[1])) {
            throw new AuthException('Auth token format is invalid.', ErrorCode::INVALID_CREDENTIAL);
        }

        return new Token($tokenValue[0], '', $tokenValue[1]);
    }

    public function check(Token $token, AccessKey $key, $signingText = '')
    {
        $signature = hash_hmac('sha1', $signingText, $key->secret, true);
        $signature = str_replace(array('+', '/'), array('-', '_'), base64_encode($signature));

        if (empty($token->signature) || $token->signature != $signature) {
            throw new AuthException("Signature is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        return true;
    }
}