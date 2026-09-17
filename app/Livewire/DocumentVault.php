<?php

namespace App\Livewire;

use App\Models\CompanyDocument;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentVault extends Component
{
    use WithFileUploads;

    public string $category = 'compliance';

    public string $name = '';

    public string $expiresAt = '';

    public $file = null;

    public function upload(): void
    {
        $data = $this->validate(['category' => ['required', 'in:compliance,financial,experience'], 'name' => ['required', 'string', 'max:255'], 'expiresAt' => ['nullable', 'date'], 'file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:20480']]);
        $path = $this->file->store('company-vault', 'public');
        CompanyDocument::query()->create([...$data, 'file_path' => $path, 'uploaded_by' => Auth::id(), 'expires_at' => $data['expiresAt'] ?: null]);
        $this->reset(['name', 'expiresAt', 'file']);
        Flux::toast(variant: 'success', text: 'Document uploaded to the vault.');
    }

    public function delete(int $documentId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);
        $document = CompanyDocument::query()->findOrFail($documentId);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
    }

    public function render()
    {
        $documents = CompanyDocument::query()->latest()->get()->groupBy('category');

        return view('livewire.document-vault', compact('documents'));
    }
}
