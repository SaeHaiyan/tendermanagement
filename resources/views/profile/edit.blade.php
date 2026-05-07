<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-900 leading-tight uppercase tracking-widest">
            Account <span class="text-red-600">Settings</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Main Grid Wrapper: 1 column on mobile, 2 columns on desktop --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                {{-- Left Side: Profile Info --}}
                {{-- Added border-t-4 border-red-600 to mirror the Dashboard/Tender Board --}}
                <div class="p-6 sm:p-8 bg-white shadow-xl sm:rounded-lg border border-slate-200 border-t-4 border-t-red-600">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Right Side: Update Password --}}
                <div class="p-6 sm:p-8 bg-white shadow-xl sm:rounded-lg border border-slate-200 border-t-4 border-t-red-600">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Delete Account (Full Width) --}}
            {{-- We keep this distinct with a lighter red border to signify caution --}}
            <div class="p-6 sm:p-8 bg-white shadow-xl sm:rounded-lg border border-red-100 border-t-4 border-t-red-200">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
