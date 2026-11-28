<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\User;
use App\Policies\UserPolicy;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $target */
        $target = $this->route('user');

        return $this->user()?->can('update', $target) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::enum(Role::class),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! in_array($value, $this->assignableRoles(), true)) {
                        $fail('Anda tidak memiliki wewenang untuk menetapkan hak akses tersebut.');
                    }
                },
            ],
        ];
    }

    /**
     * Validasi tambahan yang bergantung pada data database.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var User $target */
            $target = $this->route('user');
            $actor = $this->user();
            $newRole = $this->input('role');

            if (! $target instanceof User || ! $actor instanceof User || ! is_string($newRole)) {
                return;
            }

            $isLastAdmin = User::query()
                ->where('role', Role::Admin->value)
                ->count() <= 1;

            if ($target->role === Role::Admin
                && $newRole !== Role::Admin->value
                && $isLastAdmin) {
                $validator->errors()->add('role', 'Tidak dapat menurunkan role karena ini adalah satu-satunya Admin.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Hak akses wajib dipilih.',
            'role.enum' => 'Hak akses tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'password' => 'password',
            'role' => 'hak akses',
        ];
    }

    /**
     * Role yang boleh ditetapkan oleh pengguna yang sedang login.
     *
     * @return array<int, string>
     */
    private function assignableRoles(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        return app(UserPolicy::class)->assignableRoles($this->user(), $target);
    }
}
