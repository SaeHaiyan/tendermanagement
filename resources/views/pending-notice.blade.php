<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white p-10 shadow-sm sm:rounded-lg border border-yellow-200">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Registration Under Review ⏳</h2>
                <p class="text-lg text-gray-600">
                    Hi {{ auth()->user()->name }}, your sub-contractor profile is currently being verified by our Admin team.
                </p>
                <p class="mt-4 text-sm text-gray-400 italic">
                    You will gain full access to the dashboard once your status is set to "Active".
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
