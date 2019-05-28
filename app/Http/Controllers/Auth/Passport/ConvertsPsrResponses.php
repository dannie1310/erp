<?php

namespace App\Http\Controllers\Auth\Passport;

use App\Facades\Context;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

trait ConvertsPsrResponses
{
    /**
     * Convert a PSR7 response to a Illuminate Response.
     *
     * @param  ResponseInterface  $psrResponse
     * @return Response
     */
    public function convertResponse($psrResponse)
    {
        $headers = $psrResponse->getHeaders();
        if (Context::isEstablished() && isset($headers['Location'][0]))
            $headers['Location'][0] .= '&db=' . Context::getDatabase() . '&id=' . Context::getIdObra();

        return new Response(
            $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            $headers
        );
    }
}
