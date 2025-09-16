<?php

namespace Documentation;


use App\Http\Controllers\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Tourism API Documentation",
 *     version="1.0.0",
 *     description="Comprehensive API documentation for the Tourism Management System. This API provides endpoints for managing tours, hotels, bookings, destinations, and more.",
 *     @OA\Contact(
 *         email="support@perfectsolutions4u.com",
 *         name="Perfect Solutions 4U"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class Controller extends BaseController
{
}
