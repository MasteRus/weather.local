<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Weather test task API"
 * )
 * @OA\OpenApi(
 *     @OA\Server(
 *         url="/api",
 *         description="Base URL for all API endpoints"
 *     ),
 *     @OA\Server(
 *          url="http://127.0.0.1:8000/api",
 *          description="Base URL DOCKER all API endpoints"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
