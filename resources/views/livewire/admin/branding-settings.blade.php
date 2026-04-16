<div class="mx-auto max-w-lg">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Branding Settings</h2>
        <p class="text-sm text-gray-500">Customize company name and logo</p>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <form wire:submit="save" class="space-y-6">
            <!-- Company Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                <input wire:model="company_name"
                       type="text"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Current Logo -->
            @if($currentLogoUrl)
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Logo</label>
                <div class="mt-2 flex items-center gap-4">
                    <img src="{{ $currentLogoUrl }}" alt="Current Logo" class="h-16 w-auto rounded-lg border border-gray-200 p-1">
                    <button type="button"
                            wire:click="removeLogo"
                            wire:confirm="Remove the current logo?"
                            class="text-sm text-red-600 hover:text-red-700">
                        Remove
                    </button>
                </div>
            </div>
            @endif

            <!-- Upload Logo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ $currentLogoUrl ? 'Replace Logo' : 'Upload Logo' }}</label>
                <div class="mt-2">
                    <input wire:model="logo"
                           type="file"
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                    @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, SVG. Max 2MB.</p>
                </div>

                @if($logo)
                <div class="mt-3">
                    <p class="text-xs text-gray-500">Preview:</p>
                    <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="mt-1 h-16 w-auto rounded-lg border border-gray-200 p-1">
                </div>
                @endif
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50">
                Save Changes
            </button>
        </form>
    </div>
</div>
