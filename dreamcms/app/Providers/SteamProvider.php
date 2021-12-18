<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use RuntimeException;
use SocialiteProviders\Steam\Provider;

class SteamProvider extends Provider
{
    protected function getUserByToken($token)
    {
        if (is_null($token)) {
            return null;
        }
        if (empty($this->clientSecret)) {
            throw new RuntimeException('The Steam API key has not been specified.');
        }
        $response = $this->getHttpClient()->request(
            'GET',
            sprintf(self::STEAM_INFO_URL, $this->clientSecret, $token)
        );

        $contents = json_decode($response->getBody()->getContents(), true);

        return Arr::get($contents, 'response.players.0');
    }

    protected function getAuthUrl($state)
    {
        $params = [
            'openid.ns'         => self::OPENID_NS,
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $this->redirectUrl,
            'openid.realm'      => config('app.url'),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        return self::OPENID_URL.'?'.http_build_query($params, '', '&');
    }
}