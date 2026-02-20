<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 space-y-6">

            @php
                $message = session('success') ?? session('warning');
                $bgColor = session('success') ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600';
            @endphp

            @if($message)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="fixed top-5 inset-x-0 flex justify-center z-50">
                    <div class="{{ $bgColor }} text-white px-6 py-4 rounded shadow-lg flex items-center space-x-3">
                        <span>{{ $message }}</span>
                        <button type="button" @click="show = false" class="ml-auto text-white font-bold px-2 py-1 rounded">&times;</button>
                    </div>
                </div>
            @endif

            <!-- Add Leave Type -->
            <form method="POST" action="{{ route('supervisor.leaveType.store') }}" class="bg-white p-6 rounded shadow flex gap-4 items-end">
                @csrf
                <input type="text" name="code" placeholder="Code" class="border px-2 py-1 rounded w-32" required>
                <input type="text" name="name" placeholder="Name" class="border px-2 py-1 rounded flex-1" required>
                <button type="submit" onclick="disableSubmit(this)" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add
                </button>
        </form>
        
        <!-- Bulk Update Table -->
        <form method="POST" action="{{ route('supervisor.leaveType.bulkUpdate') }}">
            @csrf
            @method('PUT')

            <div class="bg-white shadow rounded overflow-hidden">
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Code</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2 text-center">Allow Apply</th>
                            <th class="px-4 py-2 text-center">Deduct AL</th>
                            <th class="px-4 py-2 text-center">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $type)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <input type="text" name="leaveTypes[{{ $type->id }}][code]" value="{{ $type->code }}" class="border px-2 py-1 rounded w-24">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="leaveTypes[{{ $type->id }}][name]" value="{{ $type->name }}" class="border px-2 py-1 rounded w-full">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="hidden" name="leaveTypes[{{ $type->id }}][intern_allowed_apply]" value="0">
                                <input type="checkbox" name="leaveTypes[{{ $type->id }}][intern_allowed_apply]" value="1" {{ $type->intern_allowed_apply ? 'checked' : '' }}>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="hidden" name="leaveTypes[{{ $type->id }}][affects_al_balance]" value="0">
                                <input type="checkbox" name="leaveTypes[{{ $type->id }}][affects_al_balance]" value="1" {{ $type->affects_al_balance ? 'checked' : '' }}>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <!-- DELETE as GET link -->
                                <a href="{{ route('supervisor.leaveType.destroy', $type->id) }}"
                                onclick="return confirm('Are you sure?')"
                                class="text-red-600 hover:underline">Remove</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No leave types found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4 text-right border-t">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                        Save All Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function disableSubmit(button) {
        const form = button.form;

        if (!form.checkValidity()) {
            form.reportValidity(); // show validation errors
            return;
        }

        button.disabled = true;
        button.innerText = "Submitting...";
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-gray-400', 'cursor-not-allowed');
        button.form.submit();
    }
</script>
