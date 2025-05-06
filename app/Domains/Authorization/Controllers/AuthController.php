<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Controllers;

use App\Support\BusInterface;
use App\Domains\Authorization\Commands\{ForgetPasswordCommand,
    LoginUserCommand,
    LogoutUserCommand,
    RegisterUserCommand,
    ResetPasswordCommand,
    UpdatePasswordCommand,
    VerifyEmailCommand};
use App\Domains\Authorization\DTOs\{ForgetPasswordDTO,
    LoginUserDTO,
    RegisterUserDTO,
    ResetPasswordDTO,
    UpdatePasswordDTO,
    VerifyEmailDTO};
use App\Domains\Authorization\Requests\{ForgetPasswordRequest,
    LoginUserRequest,
    RegisterUserRequest,
    ResetPasswordRequest,
    UpdatePasswordRequest
};
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected BusInterface $bus)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register new user",
     *     tags={"Authorization"},
     *     description="Registers a new customer user and returns access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer registered successfully"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken."))
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $dto = new RegisterUserDTO($request->validated());
        $token = $this->bus->dispatch(new RegisterUserCommand($dto));

        return response()->json([
            'message' => 'Customer registered successfully',
            'access_token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login user",
     *     tags={"Authorization"},
     *     description="Authenticates user and returns access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="1|sometokenvalue...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authentication failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="credentials", type="array",
     *                     @OA\Items(type="string", example="Invalid email or password")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $dto = new LoginUserDTO($request->validated());
        $data = $this->bus->dispatch(new LoginUserCommand($dto));

        if (!$data) {
            return response()->json([
                'message' => 'Authentication failed',
                'errors' => [
                    'credentials' => ['Invalid email or password'],
                ],
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => $data,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     operationId="forgotPassword",
     *     tags={"Authorization"},
     *     summary="Send a password reset link",
     *     description="Sends a password reset link to the user's email address.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset link sent successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="We couldn’t find a user with that email address.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Please wait before trying again.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Something went wrong while sending the reset link.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function forget(ForgetPasswordRequest $request): JsonResponse
    {
        $dto = new ForgetPasswordDTO($request->validated());
        $status = $this->bus->dispatch(new ForgetPasswordCommand($dto));

        return match ($status) {
            PasswordBroker::RESET_LINK_SENT => response()->json(['message' => 'Reset link sent successfully.']),
            PasswordBroker::INVALID_USER => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['We couldn’t find a user with that email address.'],
                ],
            ], 404),
            PasswordBroker::RESET_THROTTLED => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['Please wait before trying again.'],
                ],
            ], 429),
            default => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['Something went wrong while sending the reset link.'],
                ],
            ], 500),
        };
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     operationId="resetPassword",
     *     tags={"Authorization"},
     *     summary="Reset user password",
     *     description="Resets the user's password using a valid token.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "token", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi..."),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password has been reset.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="token", type="array",
     *                     @OA\Items(type="string", example="The reset token is invalid or has expired.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="We couldn’t find a user with that email address.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Please wait before trying again.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reset failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Something went wrong while resetting the password.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $dto = new ResetPasswordDTO($request->validated());
        $status = $this->bus->dispatch(new ResetPasswordCommand($dto));

        return match ($status) {
            PasswordBroker::PASSWORD_RESET => response()->json(['message' => 'Password has been reset.']),
            PasswordBroker::INVALID_TOKEN => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'token' => ['The reset token is invalid or has expired.'],
                ],
            ], 400),
            PasswordBroker::INVALID_USER => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['We couldn’t find a user with that email address.'],
                ],
            ], 404),
            PasswordBroker::RESET_THROTTLED => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['Please wait before trying again.'],
                ],
            ], 429),
            default => response()->json([
                'message' => 'Reset failed',
                'errors' => [
                    'email' => ['Something went wrong while resetting the password.'],
                ],
            ], 500),
        };
    }

    /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Refresh access token",
     *     tags={"Authorization"},
     *     description="Returns a new access token if the previous token is expired and has been refreshed in middleware.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="1|newlygeneratedsanctumtoken")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No refreshed token found in headers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No refreshed token found in headers")
     *         )
     *     )
     * )
     */

    public function refresh(): JsonResponse
    {
        $authorizationHeader = request()->header('Authorization');

        if (!$authorizationHeader) {
            return response()->json([
                'message' => 'No refreshed token found in headers',
            ], 400);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => str_replace('Bearer ', '', $authorizationHeader),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout user",
     *     tags={"Authorization"},
     *     description="Revokes the current access token for the authenticated user.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No active access token found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No active access token found")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        $result = $this->bus->dispatch(new LogoutUserCommand(request()->user()));

        if ($result) {
            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'message' => 'No active access token found',
        ], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/update-password",
     *     summary="Update user password",
     *     tags={"Authorization"},
     *     description="Allows the authenticated user to update their password by providing the current password and the new one.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", example="currentPassword123"),
     *             @OA\Property(property="new_password", type="string", example="newPassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="newPassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error while updating the password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password update failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="new_password", type="array", @OA\Items(type="string", example="Something went wrong while updating the password."))
     *             )
     *         )
     *     )
     * )
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $dto = new UpdatePasswordDTO($request->validated());
        $status = $this->bus->dispatch(new UpdatePasswordCommand($dto));

        return $status ? response()->json(['message' => 'Password updated successfully.'])
            : response()->json([
                'message' => 'Password update failed',
                'errors' => [
                    'new_password' => ['Something went wrong while updating the password.'],
                ],
            ], 500);
    }

    public function verifyEmail(Request $request, int $id, string $hash): JsonResponse
    {
        try {
            $verifyEmailDTO = VerifyEmailDTO::fromArray(['id' => $id, 'hash' => $hash]);
            $user = $this->bus->dispatch(new VerifyEmailCommand($verifyEmailDTO));

            return response()->json(['message' => 'Email verified successfully']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()['hash'][0] ?? $e->errors()['email'][0] ?? 'Verification failed'], 400);
        }
    }
}
