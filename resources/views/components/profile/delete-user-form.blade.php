{{-- Delete User Form Component --}}
<div class="space-y-6">
    <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
        @csrf
        @method('DELETE')
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Confirm Password to Delete Account</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Delete Account</button>
            @if (session('status') === 'account-deleted')
                <p class="text-sm text-red-600">Account deleted.</p>
            @endif
        </div>
    </form>
</div> 