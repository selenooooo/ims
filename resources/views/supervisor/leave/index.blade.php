<x-app-layout>
    <div class="max-w-8xl mx-auto px-4 space-y-6">

       <!-- Page Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-2xl font-semibold text-gray-900 uppercase">Intern Leave Record</h1>

            <div class="flex gap-2">
                <a href="{{ route('supervisor.leave.leaveType') }}" 
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition">
                    <i class="fas fa-pencil-alt"></i> Leave Type
                </a>
                <a href="{{ route('supervisor.leave.create') }}" 
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i> Add Leave
                </a>
            </div>

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

        </div>

        <div class="flex flex-col md:flex-row gap-6 mb-3">
        <!-- Annual Leave Balance -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 text-sm mb-4">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Annual Leave Balance
                    </h2>
                    <span class="text-xs text-green-500">(Active interns)</span>
                </div>

                <div class="space-y-2 flex-1 overflow-y-auto">
                    @forelse($internALBalances as $intern)
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-800">{{ optional($intern->user)->name ?? 'Unknown' }}</span>
                            <span class="text-gray-600 font-semibold">{{ $intern->al_balance }} / {{ $intern->intern_duration }}</span>
                        </div>
                        <hr class="border-gray-200">
                    @empty
                        <p class="text-gray-500 text-sm text-center">No intern data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="w-full md:w-1/3">
            <form method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-3">
                <!-- Intern -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Intern</label>
                    <select name="intern_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-400">
                        <option value="all">All Interns</option>
                        @foreach($interns as $intern)
                            <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>
                                {{ $intern->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Type -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Leave Type</label>
                    <select name="leave_type_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-400">
                        <option value="all">All Types</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Date + Buttons -->
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Leave Date</label>
                        <input type="date" name="leave_date" value="{{ request('leave_date') }}" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-400">
                    </div>

                    <div class="flex gap-2 self-end">
                        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 text-xs font-semibold transition">Filter</button>
                        <a href="{{ route('supervisor.leave.index') }}" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs font-semibold transition">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Rows per page selector -->
        <div class="w-full md:w-1/3 flex justify-end items-end">
            <form method="GET" id="rowsForm" class="flex gap-2 items-center">
                <!-- Keep current filters in hidden inputs -->
                <input type="hidden" name="intern_id" value="{{ request('intern_id') }}">
                <input type="hidden" name="leave_type_id" value="{{ request('leave_type_id') }}">
                <input type="hidden" name="leave_date" value="{{ request('leave_date') }}">
                
                <select name="perPage" id="rowsPerPage" 
                        class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-400"
                        onchange="document.getElementById('rowsForm').submit()">
                    <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </form>
        </div>
    </div>


        <!-- Leave Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3">Intern</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Leave</th>
                        <!-- <th class="px-6 py-3">Half Day</th> -->
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3 text-center">Action</th>
                        <th class="px-6 py-3 text-center"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50">
                            <!-- Intern -->
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $leave->user->name }}
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') }}
                            </td>

                            <!-- Leave Type -->
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $leave->leaveType->name }} ( {{ $leave->half_day }} )
                                </span>
                            </td>

                            <!-- Half Day -->
                            <!-- <td class="px-6 py-4">
                                @if($leave->half_day)
                                    <span class="text-orange-600 font-semibold">YES</span>
                                @else
                                    <span class="text-gray-500">NO</span>
                                @endif
                            </td> -->

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($leave->status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700 font-semibold">
                                        Pending
                                    </span>
                                @elseif($leave->status === 'approved')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700 font-semibold">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 font-semibold">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{  $leave->reason ?? '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                @if($leave->status === 'pending')
                                    <div class="flex justify-center gap-2">
                                        <form method="POST" action="{{ route('supervisor.leave.approve', $leave) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 text-xs font-semibold rounded bg-green-600 hover:bg-green-700 text-white transition">
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('supervisor.leave.reject', $leave) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 text-xs font-semibold rounded bg-red-600 hover:bg-red-700 text-white transition">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Completed</span>
                                @endif
                            </td>
                            @php
                                $canDelete = $leave->status === 'approved' && (\Carbon\Carbon::parse($leave->leave_date)->isToday() || \Carbon\Carbon::parse($leave->leave_date)->isFuture());
                            @endphp
                            <td class="px-6 py-4 text-center">
                               @if($canDelete)
                                    <form action="{{ route('supervisor.leave.destroy', $leave) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this leave record?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete leave">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 italic text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No Record found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
