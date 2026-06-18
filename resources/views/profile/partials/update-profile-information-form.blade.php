<section>
    <header>
        <h2 class="text-lg font-black text-slate-900 uppercase tracking-widest">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-slate-600">
            {{ __("Update your account's profile information and company details.") }}
        </p>
    </header>
    @if (session('status') === 'profile-updated')
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 2500)"
             class="mt-4 text-sm text-emerald-600 font-black uppercase tracking-widest bg-emerald-50 px-4 py-3 rounded border border-emerald-200">
            {{ __('Saved successfully.') }}
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Personal Details --}}
        <div class="space-y-4">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('name', $user->name)" required />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('email', $user->email)" required />
            </div>
        </div>

        <hr class="border-slate-100">

        {{-- Company Details --}}
        <div class="space-y-4">
            <h3 class="text-xs font-black text-red-600 uppercase tracking-widest">Company Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-input-label for="company_name" :value="__('Company Name')" />
                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('company_name', $user->company_name)" />
                </div>

                <div class="col-span-2">
                    <x-input-label for="company_address" :value="__('Company Address')" />
                    <textarea id="company_address" name="company_address" rows="2" class="mt-1 block w-full border-slate-200 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">{{ old('company_address', $user->company_address) }}</textarea>
                </div>

                <div>
                    <x-input-label for="pic_name" :value="__('Person In Charge (PIC)')" />
                    <x-text-input id="pic_name" name="pic_name" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('pic_name', $user->pic_name)" />
                </div>
                <div>
                    <x-input-label for="pic_phone" :value="__('PIC Phone Number')" />
                    <x-text-input id="pic_phone" name="pic_phone" type="tel" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('pic_phone', $user->pic_phone)" />
                </div>
                <div>
                    <x-input-label for="office_phone" :value="__('Office Phone Number')" />
                    <x-text-input id="office_phone" name="office_phone" type="tel" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('office_phone', $user->office_phone)" />
                </div>
                <div>
                    <x-input-label for="company_email" :value="__('Company Official Email')" />
                    <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('company_email', $user->company_email)" />
                </div>
                <div>
                    <x-input-label for="cidb_reg_number" :value="__('CIDB Reg. Number')" />
                    <x-text-input id="cidb_reg_number" name="cidb_reg_number" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('cidb_reg_number', $user->cidb_reg_number)" />
                </div>
                <div>
                    <x-input-label for="ssm_number" :value="__('SSM Number')" />
                    <x-text-input id="ssm_number" name="ssm_number" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('ssm_number', $user->ssm_number)" />
                </div>
                <div>
                    <x-input-label for="company_level" :value="__('Company Level')" />
                    <x-text-input id="company_level" name="company_level" type="text" class="mt-1 block w-full border-slate-200 focus:border-red-500 focus:ring-red-500" :value="old('company_level', $user->company_level)" />
                </div>
                <div>
                    <x-input-label for="year_established" :value="__('Year Established')" />
                    <select id="year_established" name="year_established" class="mt-1 block w-full border-slate-200 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">Year</option>
                        @for ($year = date('Y'); $year >= 1950; $year--)
                            <option value="{{ $year }}" {{ old('year_established', $user->year_established) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <x-input-label :value="__('CIDB Grades (Select all)')" />
                <div class="mt-2 grid grid-cols-7 gap-2">
                    @php $selectedGrades = is_array($user->cidb_grades) ? $user->cidb_grades : json_decode($user->cidb_grades, true) ?? []; @endphp
                    @foreach(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7'] as $g)
                        <label class="relative flex items-center justify-center p-2 rounded border border-slate-200 cursor-pointer hover:bg-red-50 transition-all has-[:checked]:bg-red-50 has-[:checked]:border-red-400">
                            <input type="checkbox" name="cidb_grades[]" value="{{ $g }}" class="hidden" {{ in_array($g, $selectedGrades) ? 'checked' : '' }}>
                            <span class="text-xs font-black text-slate-700">{{ $g }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <x-input-label for="services_provided" :value="__('Services Provided')" />
                @php
                    $availableServices = [
                        'General Contracting',
                        'Civil Works',
                        'Electrical',
                        'Plumbing',
                        'Mechanical',
                        'Painting',
                        'Carpentry',
                        'Landscaping',
                        'Specialist Works'
                    ];
                    $selectedServices = is_array($user->services_provided) ? $user->services_provided : (json_decode($user->services_provided, true) ?? []);
                @endphp

                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($availableServices as $svc)
                        <label class="flex items-center gap-2 p-2 rounded border border-slate-200 cursor-pointer hover:bg-red-50">
                            <input type="checkbox" name="services_provided[]" value="{{ $svc }}" class="h-4 w-4" {{ in_array($svc, $selectedServices) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-slate-700">{{ $svc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-red-600 hover:bg-red-700 transition uppercase tracking-widest font-black">
                {{ ('Save Changes') }}
            </x-primary-button>
        </div>
    </form>
</section>
