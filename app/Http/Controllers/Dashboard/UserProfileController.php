<?php

namespace App\Http\Controllers\Dashboard;

use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Rules\ValidCpf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Update user's CPF
     */
    public function updateCpf(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cpf' => ['required', 'string', new ValidCpf, 'unique:users,cpf,'.$request->user()->id],
        ]);

        // Remove formatting (keep only numbers)
        $cpf = preg_replace('/[^0-9]/', '', $validated['cpf']);

        $request->user()->update([
            'cpf' => $cpf,
        ]);

        Toast::success('CPF atualizado com sucesso!');

        return redirect()->back();
    }

    /**
     * Check if user has CPF
     */
    public function hasCpf(Request $request)
    {
        return response()->json([
            'has_cpf' => ! empty($request->user()->cpf),
            'cpf_formatted' => $request->user()->cpf
                ? $this->formatCpf($request->user()->cpf)
                : null,
        ]);
    }

    /**
     * Format CPF as XXX.XXX.XXX-XX
     */
    protected function formatCpf(string $cpf): string
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return sprintf(
            '%s.%s.%s-%s',
            substr($cpf, 0, 3),
            substr($cpf, 3, 3),
            substr($cpf, 6, 3),
            substr($cpf, 9, 2)
        );
    }
    /**
     * Show the user profile edit page.
     */
    public function edit(Request $request): \Inertia\Response
    {
        return \Inertia\Inertia::render('dashboard/profile/edit', [
            'mustVerifyEmail' => $request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'avatar' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ]);

        $user = $request->user();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_url) {
                // Assuming 'public' disk for now as per plan, but use Storage facade for abstraction
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_url'] = $path;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Toast::success('Perfil atualizado com sucesso!');

        return redirect()->back();
    }
}
