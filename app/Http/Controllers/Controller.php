<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Foxy P.A. APIs",
 *     version="2.0.0",
 *     description="Foxy P.A. helps users organize and manage appointments by allowing them to search for clinics, follow doctors, set appointments, and receive notifications. It provides various time management modes, lets users add titles and descriptions to appointment requests, and enables anyone signed in to set or receive appointments."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token in the format **Bearer &lt;token&gt;**"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}