<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Connected Accounts
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Manage your connected social accounts and authentication methods.
        </p>
    </header>

    @if (session('status') === 'google-disconnected')
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">Google account disconnected successfully.</p>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="mt-6 space-y-4">
        <!-- Google Account -->
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                    <svg class="w-6 h-6" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                </div>
                
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900">Google Account</h3>
                        @if($user->google_id)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                Connected
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                Not Connected
                            </span>
                        @endif
                    </div>
                    
                    @if($user->google_id)
                        <p class="text-sm text-gray-600 mt-1">
                            You can sign in with your Google account
                        </p>
                    @else
                        <p class="text-sm text-gray-600 mt-1">
                            Connect your Google account for easier sign in
                        </p>
                    @endif
                </div>
            </div>
            
            <div>
                @if($user->google_id)
                    <form method="POST" action="{{ route('profile.disconnect-google') }}" onsubmit="return confirm('Are you sure you want to disconnect your Google account?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-300 rounded-lg hover:bg-red-50 transition">
                            Disconnect
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.google') }}" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-50 transition inline-block">
                        Connect Google
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Provider Info -->
        @if($user->provider)
            <div class="text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        <strong>Primary Sign-in Method:</strong> 
                        {{ ucfirst($user->provider) }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</section>
