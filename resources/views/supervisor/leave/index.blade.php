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

        <!-- Annual Leave Balance -->
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

                <!-- Header -->
                <div class="flex items-center gap-2 text-sm mb-4">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Annual Leave Balance
                    </h2>
                    <span class="text-xs text-green-500">
                        (Active interns)
                    </span>
                </div>

                <!-- AL Balance List -->
                <div class="space-y-2">
                    @forelse($internALBalances as $intern)
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-800">
                                {{ optional($intern->user)->name ?? 'Unknown' }}
                            </span>

                            <span class="text-gray-600 font-semibold">
                                {{ $intern->al_balance }} / {{ $intern->intern_duration }}
                            </span>
                        </div>
                        <hr class="border-gray-200">
                    @empty
                        <p class="text-gray-500 text-sm text-center">
                            No intern data available
                        </p>
                    @endforelse
                </div>

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
                                    {{ $leave->leaveType->name }}
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
                            <td class="px-6 py-4 text-center">
                                @if($leave->status === 'approved' )
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
