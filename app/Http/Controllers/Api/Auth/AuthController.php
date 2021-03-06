<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\TitlesManager\Auth\Services\AuthService;
use App\TitlesManager\User\Models\UserEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api\Auth
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * AuthController constructor.
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function signIn(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials)) {
            return new JsonResponse(['message' => 'Credentials not found']);
        }

        /** @var UserEloquent $user */
        $user = Auth::user();
        $token = $user->createToken(UserEloquent::USER_TOKEN_NAME);

        return new JsonResponse(['user' => $user, 'token' => $token->accessToken]);
    }

    /**
     * @param UserRegistrationRequest $request
     * @return JsonResponse
     */
    public function signUp(UserRegistrationRequest $request)
    {
        $registrationRequest = $request->validated();

        /** @var UserEloquent $user */
        $user = UserEloquent::create($registrationRequest);
        $token = $user->createToken(UserEloquent::USER_TOKEN_NAME);

        return new JsonResponse(['user' => $user, 'token' => $token->accessToken]);
    }
}
