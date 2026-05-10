<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Edit tender: <span class="text-indigo-600">{{ $tenders->title }}</span>
            </h2>
            <a href="{{ route('admin.tenders.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                &larr; Back to Board
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">

                <form action="{{ route('admin.tenders.update', $tenders->id) }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Project Title</label>
                            <input type="text" name="title" value="{{ old('title', $tenders->title) }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 font-bold text-slate-700 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">tenders Ref No.</label>
                            <input type="text" name="tender_ref_number" value="{{ old('tender_ref_number', $tenders->tender_ref_number) }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 font-bold text-slate-700 shadow-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Required CIDB Grade(s)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            @php $selectedGrades = explode(',', $tenders->required_grade); @endphp
                            @foreach(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7'] as $required_grade)
                                <label class="relative flex items-center p-3 rounded-xl border border-white bg-white shadow-sm cursor-pointer hover:border-indigo-200 transition group">
                                    <input type="checkbox" name="required_grade[]" value="{{ $required_grade }}" {{ in_array($required_grade, $selectedGrades) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-bold text-slate-600 group-hover:text-indigo-600">{{ $required_grade }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Budget (RM)</label>
                            <input type="number" step="0.01" name="estimated_budget" value="{{ old('estimated_budget', $tenders->estimated_budget) }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Priority</label>
                            <select name="priority_level" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm">
                                <option value="low" {{ $tenders->priority_level == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $tenders->priority_level == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $tenders->priority_level == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Min. Experience (Yrs)</label>
                            <input type="number" name="years_experience_required" value="{{ old('years_experience_required', $tenders->years_experience_required) }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Site Location</label>
                            <input type="text" name="site_location" value="{{ old('site_location', $tenders->site_location) }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Site Visit Date</label>
                            <input type="datetime-local" name="site_visit_date" value="{{ old('site_visit_date', $tenders->site_visit_date ? \Carbon\Carbon::parse($tenders->site_visit_date)->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $tenders->deadline ? \Carbon\Carbon::parse($tenders->deadline)->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Services & Scope</label>
                        <textarea name="required_services" rows="2" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm" required>{{ old('required_services', $tenders->required_services) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-600 shadow-sm" required>{{ old('description', $tenders->description) }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                        <select name="status" class="rounded-xl border-slate-200 focus:ring-indigo-500 font-bold text-slate-700 shadow-sm">
                            <option value="open" {{ $tenders->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="assigned" {{ $tenders->status == 'assigned' ? 'selected' : '' }}>Assigned (Closed)</option>
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-black text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
