<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import Activity from 'lucide-svelte/icons/activity';
    import GitBranch from 'lucide-svelte/icons/git-branch';
    import Sparkles from 'lucide-svelte/icons/sparkles';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login } from '@/routes';

    const auth = $derived(page.props.auth);

    const features = [
        {
            icon: GitBranch,
            title: 'Decision Tree C4.5',
            description: 'Prediksi program fertilitas selanjutnya berdasarkan aturan penelitian.',
            tone: 'stat-tone-teal',
        },
        {
            icon: Activity,
            title: 'Klasifikasi Kunjungan',
            description: 'Input data pasien dan atribut klasifikasi dalam satu alur kerja.',
            tone: 'stat-tone-coral',
        },
        {
            icon: Sparkles,
            title: 'Dashboard Analitik',
            description: 'Ringkasan statistik dan distribusi hasil klasifikasi secara visual.',
            tone: 'stat-tone-lilac',
        },
    ];
</script>

<AppHead title="Selamat Datang" />

<div class="flex min-h-svh flex-col">
    <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
        <div class="flex items-center gap-3">
            <div
                class="flex size-11 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-lg shadow-primary/25"
            >
                <Activity class="size-5" />
            </div>
            <div>
                <p class="text-sm font-semibold text-foreground">Program Fertilitas</p>
                <p class="text-xs text-muted-foreground">Sistem Klasifikasi</p>
            </div>
        </div>

        <nav class="flex items-center gap-2">
            {#if auth.user}
                <Button asChild>
                    {#snippet children(props)}
                        <Link href={toUrl(dashboard())} {...props}>Dashboard</Link>
                    {/snippet}
                </Button>
            {:else}
                <Button variant="outline" asChild>
                    {#snippet children(props)}
                        <Link href={toUrl(login())} {...props}>Masuk</Link>
                    {/snippet}
                </Button>
            {/if}
        </nav>
    </header>

    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-12 px-6 pb-16 pt-4">
        <section class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="space-y-6">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-4 py-1.5 text-sm font-medium text-primary"
                >
                    <Sparkles class="size-4" />
                    Decision Tree C4.5 · 597 data
                </div>

                <h1 class="text-4xl font-bold leading-tight tracking-tight text-foreground md:text-5xl">
                    Klasifikasi Program Fertilitas
                    <span class="text-primary">Selanjutnya</span>
                </h1>

                <p class="max-w-xl text-base leading-relaxed text-muted-foreground md:text-lg">
                    Aplikasi internal untuk mendukung proses klasifikasi program fertilitas berikutnya
                    menggunakan decision rule hasil penelitian RapidMiner.
                </p>

                <div class="flex flex-wrap gap-3">
                    {#if auth.user}
                        <Button size="lg" asChild>
                            {#snippet children(props)}
                                <Link href={toUrl(dashboard())} {...props}>Buka Dashboard</Link>
                            {/snippet}
                        </Button>
                    {:else}
                        <Button size="lg" asChild>
                            {#snippet children(props)}
                                <Link href={toUrl(login())} {...props}>Masuk ke Sistem</Link>
                            {/snippet}
                        </Button>
                    {/if}
                </div>
            </div>

            <div class="glass-panel relative overflow-hidden p-8">
                <div
                    class="pointer-events-none absolute -right-8 -top-8 size-40 rounded-full bg-brand-coral/20 blur-3xl"
                ></div>
                <div
                    class="pointer-events-none absolute -bottom-8 -left-8 size-40 rounded-full bg-brand-teal/20 blur-3xl"
                ></div>

                <div class="relative space-y-6">
                    <p class="text-sm font-medium uppercase tracking-wide text-muted-foreground">
                        Fitur Utama
                    </p>
                    <div class="space-y-4">
                        {#each features as feature (feature.title)}
                            <div class="list-item-soft {feature.tone}">
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center rounded-xl"
                                    style="background: var(--stat-bg); color: var(--stat-icon);"
                                >
                                    <feature.icon class="size-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-foreground">{feature.title}</p>
                                    <p class="text-sm text-muted-foreground">{feature.description}</p>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        </section>
        </main>

    <footer class="border-t border-border/60 py-6 text-center text-sm text-muted-foreground">
        Sistem Klasifikasi Program Fertilitas Selanjutnya
    </footer>
</div>
