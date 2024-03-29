<?php

namespace App\Http\Requests\Web\Login;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Web: запрос инструкции верификации учетной записи.
 */
class NotificationRequest extends FormRequest
{
    /**
     * Проверка доступа.
     * @return bool
     */
    public function authorize(): bool
    {
        if (!$this->user()) {
            return false;
        }

        if ($this->user()->hasVerifiedEmail()) {
            return false;
        }

        return true;
    }

    /**
     * Правила валидации запроса.
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,dns', 'min:5', 'max:255', 'exists:users'],
        ];
    }

    /**
     * Получить модель пользователя.
     * @param null $guard Guard.
     * @return Builder|Model|object|null
     */
    public function user($guard = null)
    {
        return User::query()->whereNull('email_verified_at')
            ->where('email', '=', $this->get('email'))
            ->first();
    }

    /**
     * Отправить инструкцию по активации аккаунта пользователю.
     * @return void
     */
    public function fulfill()
    {
        $this->user()->sendEmailVerificationNotification();
    }

}
