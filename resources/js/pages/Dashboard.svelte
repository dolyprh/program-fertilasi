<script module lang="ts">
    import { dashboard } from '@/routes';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Activity from 'lucide-svelte/icons/activity';
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import CheckCircle2 from 'lucide-svelte/icons/check-circle-2';
    import Layers from 'lucide-svelte/icons/layers';
    import AppHead from '@/components/AppHead.svelte';
    import PageHeader from '@/components/PageHeader.svelte';
    import PageShell from '@/components/PageShell.svelte';
    import StatCard from '@/components/StatCard.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { create, index, show } from '@/routes/classifications';
    import type { Classification, ResultDistributionItem } from '@/types';

    let {
        totalClassifications,
        todayClassifications,
        classifiedCount,
        noRuleCount,
        recentClassifications,
        resultDistribution,
    }: {
        totalClassifications: number;
        todayClassifications: number;
        classifiedCount: number;
        noRuleCount: number;
        recentClassifications: Classification[];
        resultDistribution: ResultDistributionItem[];
    } = $props();

    const maxDistribution = $derived(
        resultDistribution.reduce((max, item) => Math.max(max, item.total), 0),
    );

    const chartColors = [
        'bg-teal-500',
        'bg-orange-400',
        'bg-violet-500',
        'bg-amber-400',
        'bg-rose-400',
        'bg-cyan-500',
        'bg-emerald-500',
        'bg-indigo-400',
    ];
</script>

<AppHead title="Dashboard" />

<PageShell>
    <PageHeader
        title="Dashboard"
        description="Ringkasan klasifikasi program fertilitas selanjutnya — pantau aktivitas dan distribusi hasil prediksi."
    >
        {#snippet actions()}
            <Button asChild>
                {#snippet children(props)}
                    <Link href={create()} {...props}>+ Klasifikasi Baru</Link>
                {/snippet}
            </Button>
        {/snippet}
    </PageHeader>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Total Klasifikasi" value={totalClassifications} icon={Layers} tone="teal" />
        <StatCard label="Klasifikasi Hari Ini" value={todayClassifications} icon={CalendarDays} tone="coral" />
        <StatCard label="Berhasil Diklasifikasikan" value={classifiedCount} icon={CheckCircle2} tone="lilac" />
        <StatCard label="Tidak Ada Rule" value={noRuleCount} icon={Activity} tone="amber" />
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-lg">
                    <span class="inline-block size-2 rounded-full bg-teal-500"></span>
                    Distribusi Hasil
                </CardTitle>
                <CardDescription>
                    Sebaran prediksi Promil Selanjutnya dari klasifikasi Anda.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                {#if resultDistribution.length === 0}
                    <div class="rounded-xl border border-dashed border-border/80 bg-muted/30 px-4 py-8 text-center">
                        <p class="text-sm text-muted-foreground">Belum ada hasil klasifikasi.</p>
                    </div>
                {:else}
                    {#each resultDistribution as item, index (item.label)}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium">{item.label}</span>
                                <span class="rounded-full bg-muted px-2.5 py-0.5 text-xs font-semibold text-muted-foreground">
                                    {item.total}
                                </span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-muted/80">
                                <div
                                    class="h-full rounded-full transition-all duration-500 {chartColors[index % chartColors.length]}"
                                    style={`width: ${maxDistribution === 0 ? 0 : (item.total / maxDistribution) * 100}%`}
                                ></div>
                            </div>
                        </div>
                    {/each}
                {/if}
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-start justify-between gap-3">
                <div>
                    <CardTitle class="flex items-center gap-2 text-lg">
                        <span class="inline-block size-2 rounded-full bg-orange-400"></span>
                        Klasifikasi Terbaru
                    </CardTitle>
                    <CardDescription>Riwayat proses klasifikasi terakhir.</CardDescription>
                </div>
                <Button variant="outline" size="sm" asChild>
                    {#snippet children(props)}
                        <Link href={index()} {...props}>Lihat Semua</Link>
                    {/snippet}
                </Button>
            </CardHeader>
            <CardContent class="space-y-3">
                {#if recentClassifications.length === 0}
                    <div class="rounded-xl border border-dashed border-border/80 bg-muted/30 px-4 py-8 text-center">
                        <p class="text-sm text-muted-foreground">Belum ada klasifikasi.</p>
                    </div>
                {:else}
                    {#each recentClassifications as item (item.id)}
                        <div class="list-item-soft">
                            <div class="space-y-1">
                                <p class="font-semibold text-foreground">{item.case_code}</p>
                                <p class="text-sm text-muted-foreground">
                                    {item.patient_name} · {item.current_promil?.name ?? '—'}
                                </p>
                                <p class="text-sm font-medium text-primary">
                                    {item.prediction_result ?? 'Tidak ada rule'}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    variant={item.status === 'classified' ? 'default' : 'secondary'}
                                    class="rounded-lg"
                                >
                                    {item.status === 'classified' ? 'Classified' : 'No Rule'}
                                </Badge>
                                <Button variant="outline" size="sm" asChild>
                                    {#snippet children(props)}
                                        <Link href={show(item.id)} {...props}>Detail</Link>
                                    {/snippet}
                                </Button>
                            </div>
                        </div>
                    {/each}
                {/if}
            </CardContent>
        </Card>
    </div>
</PageShell>
