@extends('admin.layout')

@section('title', 'M-PESA Settings')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">M-PESA Settings</h1>
        <div class="flex space-x-2">
            @if($isConfigured ?? false)
                <button type="button" onclick="testConnection()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    Test Connection
                </button>
            @endif
            <button type="button" onclick="resetSettings()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                Reset to Default
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($isConfigured ?? false)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">M-PESA is configured and enabled</h3>
                    <p class="text-sm text-green-700">M-PESA payments are available for customers.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">M-PESA configuration incomplete</h3>
                    <p class="text-sm text-yellow-700">Please configure all required fields and enable M-PESA to start accepting payments.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.mpesa.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Configuration Tab -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button type="button" class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="config">
                        Configuration
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="limits">
                        Limits & URLs
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="callbacks">
                        Callback URLs
                    </button>
                </nav>
            </div>

            <!-- Configuration Tab Content -->
            <div id="config-tab" class="tab-content p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mpesa_consumer_key" class="block text-sm font-medium text-gray-700">Consumer Key *</label>
                        <input type="text" name="mpesa_consumer_key" id="mpesa_consumer_key" 
                               value="{{ old('mpesa_consumer_key', $settings['mpesa_consumer_key'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter your M-PESA consumer key">
                        @error('mpesa_consumer_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_consumer_secret" class="block text-sm font-medium text-gray-700">Consumer Secret *</label>
                        <div class="mt-1 relative">
                            <input type="password" name="mpesa_consumer_secret" id="mpesa_consumer_secret" 
                                   value="{{ old('mpesa_consumer_secret', $settings['mpesa_consumer_secret'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   placeholder="Enter your M-PESA consumer secret">
                            <button type="button" onclick="togglePassword('mpesa_consumer_secret')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('mpesa_consumer_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_passkey" class="block text-sm font-medium text-gray-700">Passkey *</label>
                        <div class="mt-1 relative">
                            <input type="password" name="mpesa_passkey" id="mpesa_passkey" 
                                   value="{{ old('mpesa_passkey', $settings['mpesa_passkey'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   placeholder="Enter your M-PESA passkey">
                            <button type="button" onclick="togglePassword('mpesa_passkey')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('mpesa_passkey')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_shortcode" class="block text-sm font-medium text-gray-700">Business Shortcode *</label>
                        <input type="text" name="mpesa_shortcode" id="mpesa_shortcode" 
                               value="{{ old('mpesa_shortcode', $settings['mpesa_shortcode'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 174379">
                        @error('mpesa_shortcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_environment" class="block text-sm font-medium text-gray-700">Environment *</label>
                        <select name="mpesa_environment" id="mpesa_environment" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="sandbox" {{ old('mpesa_environment', $settings['mpesa_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="live" {{ old('mpesa_environment', $settings['mpesa_environment'] ?? '') === 'live' ? 'selected' : '' }}>Live (Production)</option>
                        </select>
                        @error('mpesa_environment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Enable M-PESA Payments</h4>
                            <p class="text-sm text-gray-500">Allow customers to pay using M-PESA</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="mpesa_enabled" id="mpesa_enabled" value="1" 
                                   {{ old('mpesa_enabled', $settings['mpesa_enabled'] ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Limits & URLs Tab Content -->
            <div id="limits-tab" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mpesa_min_amount" class="block text-sm font-medium text-gray-700">Minimum Amount (KES) *</label>
                        <input type="number" name="mpesa_min_amount" id="mpesa_min_amount" 
                               value="{{ old('mpesa_min_amount', $settings['mpesa_min_amount'] ?? 1) }}"
                               min="1" max="70000" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('mpesa_min_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_max_amount" class="block text-sm font-medium text-gray-700">Maximum Amount (KES) *</label>
                        <input type="number" name="mpesa_max_amount" id="mpesa_max_amount" 
                               value="{{ old('mpesa_max_amount', $settings['mpesa_max_amount'] ?? 70000) }}"
                               min="1" max="70000" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('mpesa_max_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_account_reference" class="block text-sm font-medium text-gray-700">Account Reference</label>
                        <input type="text" name="mpesa_account_reference" id="mpesa_account_reference" 
                               value="{{ old('mpesa_account_reference', $settings['mpesa_account_reference'] ?? config('app.name')) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Your store name">
                        @error('mpesa_account_reference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mpesa_transaction_desc" class="block text-sm font-medium text-gray-700">Transaction Description</label>
                        <input type="text" name="mpesa_transaction_desc" id="mpesa_transaction_desc" 
                               value="{{ old('mpesa_transaction_desc', $settings['mpesa_transaction_desc'] ?? 'Payment for order') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Payment description">
                        @error('mpesa_transaction_desc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Callback URLs Tab Content -->
            <div id="callbacks-tab" class="tab-content p-6 hidden">
                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-blue-900 mb-2">Default Callback URLs</h3>
                        <p class="text-sm text-blue-700 mb-4">Use these URLs in your M-PESA Daraja API configuration:</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white p-3 rounded border">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">STK Push Callback:</span>
                                    <code class="block text-sm text-blue-600 mt-1">{{ url('/api/mpesa/stk-callback') }}</code>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ url('/api/mpesa/stk-callback') }}')" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between bg-white p-3 rounded border">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">C2B Confirmation:</span>
                                    <code class="block text-sm text-blue-600 mt-1">{{ url('/api/mpesa/c2b-confirmation') }}</code>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ url('/api/mpesa/c2b-confirmation') }}')" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between bg-white p-3 rounded border">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">C2B Validation:</span>
                                    <code class="block text-sm text-blue-600 mt-1">{{ url('/api/mpesa/c2b-validation') }}</code>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ url('/api/mpesa/c2b-validation') }}')" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="mpesa_callback_url" class="block text-sm font-medium text-gray-700">Custom STK Callback URL</label>
                            <input type="url" name="mpesa_callback_url" id="mpesa_callback_url" 
                                   value="{{ old('mpesa_callback_url', $settings['mpesa_callback_url'] ?? '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Leave empty to use default">
                            <p class="mt-1 text-sm text-gray-500">Optional: Override the default STK callback URL</p>
                            @error('mpesa_callback_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mpesa_confirmation_url" class="block text-sm font-medium text-gray-700">Custom Confirmation URL</label>
                            <input type="url" name="mpesa_confirmation_url" id="mpesa_confirmation_url" 
                                   value="{{ old('mpesa_confirmation_url', $settings['mpesa_confirmation_url'] ?? '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Leave empty to use default">
                            <p class="mt-1 text-sm text-gray-500">Optional: Override the default confirmation URL</p>
                            @error('mpesa_confirmation_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mpesa_validation_url" class="block text-sm font-medium text-gray-700">Custom Validation URL</label>
                            <input type="url" name="mpesa_validation_url" id="mpesa_validation_url" 
                                   value="{{ old('mpesa_validation_url', $settings['mpesa_validation_url'] ?? '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Leave empty to use default">
                            <p class="mt-1 text-sm text-gray-500">Optional: Override the default validation URL</p>
                            @error('mpesa_validation_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <div class="flex space-x-2">
                    <button type="button" onclick="exportConfig()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        Export Config
                    </button>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    Save M-PESA Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            // Update content visibility
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-tab').classList.remove('hidden');
        });
    });
});

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary success message
        const originalText = event.target.innerHTML;
        event.target.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        setTimeout(() => {
            event.target.innerHTML = originalText;
        }, 2000);
    });
}

function testConnection() {
    if (!confirm('Test M-PESA connection? This will validate your configuration.')) return;
    
    fetch('{{ route("admin.mpesa.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Connection test failed: ' + error.message);
    });
}

function resetSettings() {
    if (!confirm('Reset all M-PESA settings to default values? This cannot be undone.')) return;
    
    fetch('{{ route("admin.mpesa.reset") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to reset settings');
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function exportConfig() {
    fetch('{{ route("admin.mpesa.export") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = data.filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    })
    .catch(error => {
        alert('Export failed: ' + error.message);
    });
}
</script>
@endsection 