<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Attachments\Services\AttachmentService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckPlanFeature;
use App\Http\Requests\UploadAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function __construct(protected AttachmentService $service) {}

    /**
     * Upload a new attachment
     */
    public function store(UploadAttachmentRequest $request)
    {
        $user = $request->user();

        // Check if user can upload more attachments
        if (!$this->service->canUpload($user)) {
            $limit = $user->getPlanLimits()['max_attachments'] ?? 0;

            if ($limit === 0) {
                Toast::error('Anexos não estão disponíveis no seu plano.')
                    ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                    ->persistent();
            } else {
                Toast::error("Você atingiu o limite de {$limit} anexos do seu plano.")
                    ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                    ->persistent();
            }

            return back();
        }

        // Get the attachable model
        $attachableType = $request->input('attachable_type');
        $attachableId = $request->input('attachable_id');
        $attachable = $attachableType::findOrFail($attachableId);

        // Check ownership
        if ($attachable->user_id !== $user->id) {
            Toast::error('Você não tem permissão para adicionar anexos a este item.');
            return back();
        }

        // Validate file
        $file = $request->file('file');

        if (!$this->service->isValidFileType($file)) {
            Toast::error('Tipo de arquivo não permitido.');
            return back();
        }

        if (!$this->service->isValidFileSize($file)) {
            Toast::error('O arquivo não pode ser maior que 5MB.');
            return back();
        }

        // Store attachment
        try {
            $this->service->store($user, $attachable, $file);
            Toast::success('Anexo adicionado com sucesso!');
        } catch (\Exception $e) {
            Toast::error('Erro ao fazer upload do arquivo. Tente novamente.');
            \Log::error('Attachment upload error: ' . $e->getMessage());
        }

        return back();
    }

    /**
     * Download an attachment
     */
    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            Toast::error('Arquivo não encontrado.');
            return back();
        }

        return Storage::disk('private')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    /**
     * Delete an attachment
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        try {
            $this->service->delete($attachment);
            Toast::success('Anexo excluído com sucesso!');
        } catch (\Exception $e) {
            Toast::error('Erro ao excluir o anexo. Tente novamente.');
            \Log::error('Attachment delete error: ' . $e->getMessage());
        }

        return back();
    }

    /**
     * Get user's attachment stats
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $limit = $user->getPlanLimits()['max_attachments'] ?? 0;
        $count = $this->service->getUserAttachmentCount($user);
        $totalStorage = $this->service->getUserTotalStorage($user);

        return response()->json([
            'count' => $count,
            'limit' => $limit,
            'limit_label' => $limit === -1 ? 'Ilimitado' : $limit,
            'total_storage' => $totalStorage,
            'total_storage_formatted' => (new Attachment())->formatBytes($totalStorage),
            'can_upload' => $this->service->canUpload($user),
        ]);
    }
}
