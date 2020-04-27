<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Request\RegisterRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Hash;

class AuthorizationController extends Controller {

    /**
     * @OA\POST(
     *      path="/api/registration",
     *      operationId="register_user",
     *      tags={"Authorization"},
     *      summary="Register new user",
     *      description="If user input new valid credentials, will create new user.
     *                   And log in with his credentials, and get token in response. ",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      description="Email address of the new user.",
     *                      type="string",
     *                      example="admin@gmail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      description="Name of the new user.",
     *                      type="string",
     *                      example="Admin"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password of the new user.",
     *                      type="integer",
     *                      example=123456
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=200
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="ok"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9yZWdpc3RyYXRpb24iLCJpYXQiOjE1ODc2MzUyNzEsImV4cCI6MTU4NzYzODg3MSwibmJmIjoxNTg3NjM1MjcxLCJqdGkiOiJwZ1JuNnk1aXhqZENkblJiIiwic3ViIjoxOCwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.gMKj35EISH3cF6rSa8xPN4nz2FTeuUE3iBqXwr8VgIw"
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid request data",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=401
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="failed"
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="The email has already been taken."
     *              ),
     *          )
     *      )
     *  )
     */
    public function registration(RegisterRequest $request) {
        try {
            $user           = new User();
            $user->name     = request('name');
            $user->email    = request('email');
            $user->password = Hash::make(request('password'));
            $user->save();

            return $this->login($request);
        } catch (\Throwable $exception) {
            return ResponseHelper::sendErrorResponse(ErrorHelper::AUTH_ERROR, $exception->getMessage());
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/login",
     *      operationId="login_user",
     *      tags={"Authorization"},
     *      summary="Check user credential and return token",
     *      description="If user credentials are valid, return token to user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      description="Email address of the new user.",
     *                      type="string",
     *                      example="admin@gmail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password of the new user.",
     *                      type="integer",
     *                      example=123456
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=200
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="ok"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9yZWdpc3RyYXRpb24iLCJpYXQiOjE1ODc2MzUyNzEsImV4cCI6MTU4NzYzODg3MSwibmJmIjoxNTg3NjM1MjcxLCJqdGkiOiJwZ1JuNnk1aXhqZENkblJiIiwic3ViIjoxOCwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.gMKj35EISH3cF6rSa8xPN4nz2FTeuUE3iBqXwr8VgIw"
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid request data",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=401
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="failed"
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Invalid email or password."
     *              ),
     *          )
     *      )
     *  )
     */
    public function login(Request $request) {
        try {
            $credentials = $request->only(['email', 'password']);

            if (! $token = auth()->attempt($credentials)) {
                return ResponseHelper::sendErrorResponse(ErrorHelper::AUTH_ERROR);
            }

            return ResponseHelper::sendSuccessResponse($token);
        } catch (\Throwable $exception) {
            return ResponseHelper::sendErrorResponse(ErrorHelper::AUTH_ERROR, $exception->getMessage());
        }
    }

}
