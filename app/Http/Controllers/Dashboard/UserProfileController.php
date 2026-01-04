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
}
