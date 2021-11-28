<?php

namespace App\Http\Requests\Web\Login;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Web: запрос аутентификации пользователя.
 */
class AuthenticationRequest extends FormRequest
{
    /**
     * Проверка доступа.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации запроса.
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string', 'alpha_dash', 'max:255', 'min:5', 'exists:users'],
            'password' => ['required', Password::defaults()],
        ];
    }

    /**
     * Обработчик ошибок валидации.
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'errors' => $validator->errors()->toArray(),
        ], 422);

        throw new ValidationException($validator, $response);
    }

    /**
     * Авторизация пользователя.
     * @return void
     * @throws ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();
        $this->checkIsVerify();

        if (!Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['username' => __('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Проверка лимита неудачных попыток.
     * @return void
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Идентификатор неудачных попыток.
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('username')) . '|' . $this->ip();
    }

    /**
     * Получить модель пользователя.
     * @param string|null $guard
     * @return Builder|Model|object
     * @throws ValidationException
     */
    public function user($guard = null)
    {
        $user = User::query()->where('username', '=', $this->only('username'))
            ->whereNotNull('email_verified_at')->first();

        if (empty($user)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['username' => __('auth.failed')]);
        }

        return $user;
    }

    /**
     * Проверить верифицирован ли пользователь.
     * @throws ValidationException
     */
    public function checkIsVerify()
    {
        if (empty($this->user()->email_verified_at)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['username' => __('auth.verify')]);
        }
    }
}