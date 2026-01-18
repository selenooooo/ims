<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <!-- Add Leave Type Form -->
        <form method="POST" action="{{ route('supervisor.leaveType.store') }}"
              class="bg-white p-6 rounded-lg shadow-md flex flex-wrap gap-4 items-end">
            @csrf

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" required
                       class="border border-gray-300 rounded px-3 py-2 text-sm w-32 focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            <div class="flex-1 flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required
                       class="border border-gray-300 rounded px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Add
            </button>
        </form>

        <!-- Leave Type Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-gray-600 uppercase text-left tracking-wider">
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3 text-center"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($leaveTypes as $type)
                        <tr class="hover:bg-gray-50">
                            <!-- Code -->
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('supervisor.leaveType.update', $type) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="code" value="{{ $type->code }}"
                                           class="border border-gray-300 rounded px-2 py-1 text-sm w-24 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-3">
                                    <input type="text" name="name" value="{{ $type->name }}"
                                           class="border border-gray-300 rounded px-2 py-1 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-3 text-center flex justify-center gap-2">
                                    <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition text-sm">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </form>

                                <!-- <form method="POST" action="{{ route('supervisor.leaveType.destroy', $type) }}"
                                        onsubmit="return confirm('Delete this leave type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-sm">
                                       <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form> -->

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500">
                                No leave types found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
