<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-5xl space-y-8">
        <div class="border-b border-[#dce3d8] pb-6 dark:border-zinc-800">
            <flux:text class="mb-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#2d7a57] dark:text-emerald-400">Company communication</flux:text>
            <flux:heading size="xl" level="1">Company feedback &amp; reports</flux:heading>
            <flux:text class="mt-2 max-w-2xl">Write a professional email, send it to the company, copy the customer, and attach the PDF report.</flux:text>
        </div>

        @if (session('feedback-sent'))
        <flux:callout variant="success" icon="check-circle">{{ session('feedback-sent') }}</flux:callout>
        @endif

        <flux:card class="max-w-3xl border-[#dce3d8] bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <form wire:submit="sendFeedback" class="space-y-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input wire:model="company" label="Company name" placeholder="Example Company Ltd" required />
                    <flux:input wire:model="companyEmail" label="Company email (To)" type="email" placeholder="contact@company.com" required />
                </div>
                <flux:input wire:model="customerEmail" label="Customer email (CC)" type="email" placeholder="customer@example.com" required />
                <flux:input wire:model="subject" label="Subject" placeholder="Feedback report subject" required />
                <flux:textarea wire:model="message" label="Main email body" rows="14" placeholder="Write the complete email body here..." required />
                <div>
                    <flux:field>
                        <flux:label>Attach PDF report</flux:label>
                        <input wire:model="reportFile" type="file" accept="application/pdf,.pdf" class="mt-2 block w-full rounded-lg border border-[#dce3d8] bg-white px-3 py-2 text-sm text-[#17221b] file:mr-3 file:rounded-md file:border-0 file:bg-[#eaf5ed] file:px-3 file:py-2 file:font-semibold file:text-[#2d7a57] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" required>
                        <flux:error name="reportFile" />
                        <flux:description>PDF only, maximum 10 MB. The company and customer will receive this attachment.</flux:description>
                    </flux:field>
                    <div wire:loading wire:target="reportFile" class="mt-2 text-sm text-[#637168]">Uploading PDF...</div>
                    @if ($reportFile)
                    <div class="mt-2 text-sm text-[#2d7a57]">Ready to attach: {{ $reportFile->getClientOriginalName() }}</div>
                    @endif
                </div>
                <div class="flex justify-end border-t border-[#dce3d8] pt-5 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary" icon="paper-airplane">Send report</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>