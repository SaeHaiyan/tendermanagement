<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-widest">
            {{ __('Publish New Tender') }}
        </h2>
    </x-slot>
    
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-t-red-600">
            <div class="p-8">
                <form action="{{ route('admin.tenders.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="title" :value="__('Project Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                        </div>
                        <div>
                            <x-input-label for="tender_ref_number" :value="__('Tender Ref No.')" />
                            <x-text-input id="tender_ref_number" name="tender_ref_number" type="text" class="mt-1 block w-full" :value="old('tender_ref_number')" required placeholder="e.g. T-2026-001" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label :value="__('Required CIDB Grades')" />
                            <div class="mt-2 grid grid-cols-4 gap-2">
                                @foreach(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7'] as $g)
                                    <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-red-50 has-[:checked]:bg-red-100">
                                        <input type="checkbox" name="required_grade[]" value="{{ $g }}" class="rounded text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-black">{{ $g }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <x-input-label for="deadline" :value="__('Submission Deadline')" />
                            <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="estimated_budget" :value="__('Budget (RM)')" />
                            <x-text-input id="estimated_budget" name="estimated_budget" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="priority_level" :value="__('Priority')" />
                            <select id="priority_level" name="priority_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="years_experience_required" :value="__('Min. Experience (Yrs)')" />
                            <x-text-input id="years_experience_required" name="years_experience_required" type="number" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="site_location" :value="__('Site Location')" />
                            <x-text-input id="site_location" name="site_location" type="text" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="site_visit_date" :value="__('Site Visit Date/Time')" />
                            <x-text-input id="site_visit_date" name="site_visit_date" type="datetime-local" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="required_services" :value="__('Services Required')" />
                        <x-text-input id="required_services" name="required_services" type="text" class="mt-1 block w-full" placeholder="e.g. Electrical, Plumbing" required />
                    </div>
                    <div>
                        <x-input-label for="description" :value="__('Project Description')" />
                        <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" required></textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4">
                        <x-primary-button class="bg-red-600 hover:bg-red-700 uppercase tracking-widest font-black">
                            {{ __('Publish Tender') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
