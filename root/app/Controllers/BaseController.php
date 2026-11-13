<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * GET a public endpoint of LINK_INTERNAL_API, such as v1/mart/site; no sign-in is needed for those
     * @param string $endpoint
     * @param array  $query
     * @return array{int, array} [http status, decoded body]; 503 when the API could not be reached or answered
     *                           something that is not its JSON
     */
    protected function fetchApi(string $endpoint, array $query): array
    {
        return $this->callApi('GET', $endpoint, ['query' => $query]);
    }

    /**
     * POST JSON to a public endpoint of LINK_INTERNAL_API, such as v1/mart/order
     * @param string $endpoint
     * @param array  $body
     * @return array{int, array} as fetchApi()
     */
    protected function postApi(string $endpoint, array $body): array
    {
        return $this->callApi('POST', $endpoint, ['json' => $body]);
    }

    /** @return array{int, array} as fetchApi() */
    private function callApi(string $method, string $endpoint, array $options): array
    {
        try {
            $response = service('curlrequest')->request($method, getenv('LINK_INTERNAL_API') . $endpoint, $options + [
                'headers'     => [
                    'Accept'          => 'application/json',
                    'Accept-Language' => $this->request->getLocale(),
                ],
                'timeout'     => 15,
                'http_errors' => false,
            ]);
            $code = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);
            // a 404 without the API's own error body means the endpoint is missing, not the record
            if (! is_array($body) || $code >= 500 || ($code === 404 && ($body['status'] ?? '') !== 'error')) {
                log_message('error', 'LINK_INTERNAL_API ' . $endpoint . ' returned HTTP ' . $code);

                return [503, []];
            }

            return [$code, $body];
        } catch (Throwable $e) {
            log_message('error', 'LINK_INTERNAL_API ' . $endpoint . ' failed: ' . $e->getMessage());

            return [503, []];
        }
    }
}
