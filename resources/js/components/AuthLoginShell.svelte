<script lang="ts">
    import type { Snippet } from 'svelte';
    import Activity from 'lucide-svelte/icons/activity';
    import GitBranch from 'lucide-svelte/icons/git-branch';
    import ShieldCheck from 'lucide-svelte/icons/shield-check';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const highlights = [
        {
            icon: GitBranch,
            title: 'Decision Tree C4.5',
            description: 'Prediksi Promil Selanjutnya berdasarkan aturan penelitian.',
        },
        {
            icon: Activity,
            title: 'Klasifikasi Kunjungan',
            description: 'Input data pasien dan atribut klasifikasi dalam satu alur.',
        },
        {
            icon: ShieldCheck,
            title: 'Akses Internal',
            description: 'Hanya untuk pengguna terdaftar dan terverifikasi.',
        },
    ];
</script>

<div class="grid min-h-svh lg:grid-cols-2">
    <section
        class="relative hidden overflow-hidden bg-linear-to-br from-teal-700 via-teal-600 to-cyan-700 px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between"
    >
        <div
            class="pointer-events-none absolute -top-24 -right-24 size-72 rounded-full bg-white/10 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-16 -left-16 size-64 rounded-full bg-cyan-300/20 blur-3xl"
        ></div>

        <div class="relative space-y-6">
            <div
                class="inline-flex size-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 backdrop-blur-sm"
            >
                <Activity class="size-7" />
            </div>
            <div class="space-y-3">
                <p class="text-sm font-medium tracking-wide text-teal-100 uppercase">
                    Program Fertilitas
                </p>
                <h1 class="max-w-md text-3xl leading-tight font-semibold">
                    Sistem Klasifikasi Program Fertilitas Selanjutnya
                </h1>
                <p class="max-w-lg text-base text-teal-50/90">
                    Aplikasi internal untuk mendukung proses klasifikasi program fertilitas
                    berikutnya menggunakan decision rule hasil penelitian.
                </p>
            </div>
        </div>

        <div class="relative space-y-4">
            {#each highlights as item (item.title)}
                <div
                    class="flex items-start gap-4 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm"
                >
                    <div
                        class="mt-0.5 flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/15"
                    >
                        <item.icon class="size-5" />
                    </div>
                    <div>
                        <p class="font-medium">{item.title}</p>
                        <p class="text-sm text-teal-50/85">{item.description}</p>
                    </div>
                </div>
            {/each}
        </div>
    </section>

    <section
        class="flex items-center justify-center bg-linear-to-b from-teal-50/60 to-background px-6 py-10 dark:from-teal-950/20 dark:to-background"
    >
        <div class="w-full max-w-md">
            <div class="mb-8 space-y-2 text-center lg:hidden">
                <div
                    class="mx-auto inline-flex size-12 items-center justify-center rounded-2xl bg-teal-600 text-white"
                >
                    <Activity class="size-6" />
                </div>
                <h1 class="text-xl font-semibold">Klasifikasi Fertilitas</h1>
                <p class="text-sm text-muted-foreground">
                    Masuk untuk melanjutkan ke sistem klasifikasi.
                </p>
            </div>

            <div
                class="rounded-2xl border border-teal-100/80 bg-card/95 p-6 shadow-xl shadow-teal-900/5 backdrop-blur-sm dark:border-teal-900/40"
            >
                {@render children?.()}
            </div>
        </div>
    </section>
</div>
