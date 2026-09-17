<x-layouts::app :title="__('Special page')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 lg:p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">Operations</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">Special page</h1>
            </div>

            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                Back to dashboard
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Today</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Priority tasks</h2>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                            <path d="M6 12h12"/>
                            <path d="M12 6v12"/>
                        </svg>
                    </div>
                </div>

                <ul class="mt-5 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                    <li class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">Receive supplier delivery at 10:00 AM</li>
                    <li class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">Review stock count before lunch</li>
                    <li class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">Close pending customer orders</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Live status</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">System health</h2>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                            <path d="M12 3v18"/>
                            <path d="M3 12h18"/>
                            <circle cx="12" cy="12" r="7"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">NFC checkout</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">98%</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full w-[98%] rounded-full bg-emerald-500"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Inventory sync</span>
                            <span class="font-semibold text-sky-600 dark:text-sky-400">91%</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full w-[91%] rounded-full bg-sky-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Action</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Quick note</h2>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                            <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11A2.5 2.5 0 0 1 17.5 20h-11A2.5 2.5 0 0 1 4 17.5v-11Z"/>
                            <path d="M8 8h8"/>
                            <path d="M8 12h8"/>
                            <path d="M8 16h5"/>
                        </svg>
                    </div>
                </div>

                <p class="mt-5 text-sm leading-6 text-slate-700 dark:text-slate-200">
                    This page is now connected to the real app navigation, so staff can reach it from the sidebar and the top navigation without any fake or design-only links.
                </p>
            </div>
        </div>
    </div>
</x-layouts::app>
