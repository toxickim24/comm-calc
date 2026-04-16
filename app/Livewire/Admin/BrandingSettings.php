<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\BrandingSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrandingSettings extends Component
{
    use WithFileUploads;

    public string $company_name = '';
    public $logo = null;
    public ?string $currentLogoUrl = null;

    public function mount(): void
    {
        $branding = BrandingSetting::current();
        $this->company_name = $branding->company_name;
        $this->currentLogoUrl = $branding->logoUrl();
    }

    public function save(): void
    {
        $this->validate([
            'company_name' => 'required|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $branding = BrandingSetting::current();
        $oldValues = $branding->only(['company_name', 'logo_path']);

        $data = ['company_name' => $this->company_name];

        if ($this->logo) {
            // Delete old logo
            if ($branding->logo_path) {
                Storage::disk('public')->delete($branding->logo_path);
            }

            $data['logo_path'] = $this->logo->store('branding', 'public');
        }

        $branding->update($data);

        AuditLog::record('branding_updated', $branding, $oldValues, $data);

        $this->currentLogoUrl = $branding->logoUrl();
        $this->logo = null;

        $this->dispatch('toast', type: 'success', message: 'Branding updated successfully.');
    }

    public function removeLogo(): void
    {
        $branding = BrandingSetting::current();

        if ($branding->logo_path) {
            Storage::disk('public')->delete($branding->logo_path);
            $branding->update(['logo_path' => null]);
            $this->currentLogoUrl = null;

            AuditLog::record('logo_removed', $branding);

            $this->dispatch('toast', type: 'success', message: 'Logo removed.');
        }
    }

    public function render()
    {
        return view('livewire.admin.branding-settings')
            ->layout('layouts.app')
            ->title('Branding Settings');
    }
}
