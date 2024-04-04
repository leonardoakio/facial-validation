<?php

namespace App\Repositories\FacialValidator;

use App\Enums\FacialValidator\FacialValidatorClientsEnum;
use GuzzleHttp\Client;

class FacialValidatorRepository implements FacialValidatorRepositoryInterface
{
    public function __construct(
        protected Client $client
    ) {
    }

    public function verifyPhoto($data) {
        return $this->client->post(
            FacialValidatorClientsEnum::SERVICE_FACIAL_URL,
            $data
        );
    }
}