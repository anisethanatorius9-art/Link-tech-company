<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-[1400px] space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div>
                <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Business intelligence</flux:text>
                <flux:heading size="xl">Reports</flux:heading>
                <flux:subheading class="mt-2">Measure bid activity, outcomes, and pipeline value.</flux:subheading>
            </div>
            @if (auth()->user()->isAdmin())
                <div class="flex gap-2">
                    <flux:button href="{{ route('admin.exports.requests.pdf') }}" variant="ghost" icon="document-arrow-down">PDF</flux:button>
                    <flux:button href="{{ route('admin.exports.requests.xlsx') }}" variant="primary" icon="arrow-down-tray">Excel</flux:button>
                </div>
            @endif
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <flux:card class="p-5"><flux:text>Total tenders</flux:text><flux:heading size="xl" class="mt-2">{{ $total }}</flux:heading></flux:card>
            <flux:card class="p-5"><flux:text>Won vs lost</flux:text><flux:heading size="xl" class="mt-2 text-emerald-600">{{ $won }} / {{ $lost }}</flux:heading></flux:card>
            <flux:card class="p-5"><flux:text>Win rate</flux:text><flux:heading size="xl" class="mt-2">{{ $winRate }}%</flux:heading></flux:card>
            <flux:card class="p-5"><flux:text>Average winning bid</flux:text><flux:heading size="xl" class="mt-2">{{ number_format((float) $averageWin, 0) }} TZS</flux:heading></flux:card>
        </div>
        <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <flux:card class="p-6">
                <flux:heading size="lg">Monthly quotation value</flux:heading>
                <div class="mt-7 flex h-64 items-end gap-4 border-b border-zinc-200 dark:border-zinc-800">
                    @foreach ($months as $month)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <div class="text-xs text-zinc-500">{{ number_format($month['value'] / 1000000, 1) }}M</div>
                            <div class="w-full rounded-t bg-[#2d7a57]" @style(['height' => $month['height'].'%'])></div>
                            <div class="pb-3 text-xs text-zinc-500">{{ $month['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </flux:card>
            <flux:card class="p-6">
                <flux:heading size="lg">Win / loss rate</flux:heading>
                <div class="mx-auto mt-8 flex size-44 items-center justify-center rounded-full" @style(['background' => 'conic-gradient(#2d7a57 '.$winRate.'%, #f4b942 0)'])>
                    <div class="flex size-28 items-center justify-center rounded-full bg-white text-center dark:bg-zinc-900"><div><strong class="block text-2xl">{{ $winRate }}%</strong><span class="text-xs text-zinc-500">won</span></div></div>
                </div>
            </flux:card>
        </div>
        <flux:card class="overflow-hidden p-0">
            <div class="p-6"><flux:heading size="lg">Officer performance</flux:heading></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-[#edf7ef] text-xs uppercase tracking-wider text-zinc-500 dark:bg-zinc-800"><tr><th class="p-4">Officer</th><th class="p-4">Tenders handled</th><th class="p-4">Won</th><th class="p-4">Performance</th></tr></thead><tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($officers as $officer)
                    <tr><td class="p-4 font-semibold">{{ $officer['name'] }}</td><td class="p-4">{{ $officer['count'] }}</td><td class="p-4 text-emerald-600">{{ $officer['won'] }}</td><td class="p-4">{{ $officer['count'] ? round($officer['won'] / $officer['count'] * 100) : 0 }}%</td></tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-zinc-500">No officer activity yet.</td></tr>
                @endforelse
            </tbody></table></div>
        </flux:card>
    </div>
</div>
