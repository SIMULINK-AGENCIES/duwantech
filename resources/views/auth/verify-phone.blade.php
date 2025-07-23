<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please verify your phone number. Enter the OTP sent to your phone.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('phone.verify.send') }}">
        @csrf
        <x-primary-button>
            {{ __('Send OTP') }}
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('phone.verify') }}" class="mt-4">
        @csrf
        <div>
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify Phone') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> 