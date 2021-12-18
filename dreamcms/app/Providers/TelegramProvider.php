<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use SocialiteProviders\Telegram\Provider;

class TelegramProvider extends Provider
{
    public function user()
    {
        $validator = Validator::make($this->request->all(), [
            'id'        => 'required|numeric',
            'auth_date' => 'required|date_format:U|before:1 day',
            'hash'      => 'required|size:64',
        ]);

        throw_if($validator->fails(), InvalidArgumentException::class);

        $dataToHash = collect($this->request->except('hash'))
            ->transform(function ($val, $key) {
                return "$key=$val";
            })
            ->sort()
            ->join("\n");

        $hash_key = hash('sha256', $this->clientSecret, true);
        $hash_hmac = hash_hmac('sha256', $dataToHash, $hash_key);

        throw_if(
            $this->request->hash !== $hash_hmac,
            InvalidArgumentException::class
        );

        return $this->mapUserToObject($this->request->except(['auth_date', 'hash']));
    }
}