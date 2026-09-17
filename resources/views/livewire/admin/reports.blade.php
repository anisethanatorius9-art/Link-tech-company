<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div><flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Reporting centre</flux:text><flux:heading size="xl">Reports &amp; exports</flux:heading><flux:text class="mt-2">Link-Tech quotation reports and internal margin controls.</flux:text></div>
            <div class="flex flex-wrap gap-3"><flux:button href="{{ route('admin.exports.requests.pdf') }}" icon="document-arrow-down" variant="ghost">Download PDF</flux:button><flux:button href="{{ route('admin.exports.requests.xlsx') }}" icon="arrow-down-tray" variant="primary">Download Excel</flux:button></div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900"><flux:text>Total requests</flux:text><flux:heading size="xl" class="mt-2">{{ $total }}</flux:heading></flux:card>
            <flux:card class="border-[#e9d7b7] bg-[#fff8eb] p-5 dark:border-zinc-800 dark:bg-amber-950/20"><flux:text>Pending</flux:text><flux:heading size="xl" class="mt-2 text-amber-700">{{ $pending }}</flux:heading></flux:card>
            <flux:card class="border-[#c6dfcd] bg-[#edf7ef] p-5 dark:border-zinc-800 dark:bg-emerald-950/20"><flux:text>Completed</flux:text><flux:heading size="xl" class="mt-2 text-[#2d7a57]">{{ $completed }}</flux:heading></flux:card>
            <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900"><flux:text>Quantity requested</flux:text><flux:heading size="xl" class="mt-2">{{ $quantity }}</flux:heading></flux:card>
        </div>
        <flux:card class="border-[#dce3d8] bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900"><flux:heading size="lg">Quotation export centre</flux:heading><flux:text class="mt-2 max-w-2xl">The PDF contains customer-facing prices, VAT and totals. Excel also includes a protected operational sheet for costs, markup and profit.</flux:text><div class="mt-5 flex flex-wrap gap-3"><flux:button href="{{ route('admin.exports.requests.pdf') }}" icon="document-arrow-down" variant="ghost">Customer quotation PDF</flux:button><flux:button href="{{ route('admin.exports.requests.xlsx') }}" icon="table-cells" variant="primary">Internal Excel workbook</flux:button></div></flux:card>
    </div>
</div>
