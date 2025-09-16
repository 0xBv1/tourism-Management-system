<?php

namespace Documentation;

use App\Http\Controllers\Controller;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  schema="TourOption",
 *  title="Tour Option Schema",
 *                     @OA\Property(
 *                         property="id",
 *                         type="number",
 *                         example="1"
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="Lorem Ipsum"
 *                     ),
 *                     @OA\Property(
 *                         property="description",
 *                         type="string",
 *                         example="Lorem Ipsum"
 *                     ),
 *                     @OA\Property(
 *                         property="adult_price",
 *                         type="number",
 *                         example="100.0"
 *                     ),
*                      @OA\Property(
 *                         property="child_price",
 *                         type="number",
 *                         example="100.0"
 *                      ),
 * )
 */
class TourOptionController extends Controller
{
    use HasApiResponse;
}
