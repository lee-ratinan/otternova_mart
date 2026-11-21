<?php

namespace App\Exceptions;

use CodeIgniter\HTTP\ResponsableInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

/**
 * A shop page asked for while the business is closed on the mart (v1/mart/site business.is_open false): carries the
 * "not open yet" page (404, so it is not indexed), which CodeIgniter sends instead of the page asked for
 * (see Business::site())
 */
final class ShopClosedException extends RuntimeException implements ResponsableInterface
{
    public function __construct(private readonly ResponseInterface $response)
    {
        parent::__construct('The shop is not open on the mart.');
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
