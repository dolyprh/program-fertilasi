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
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
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
</script>

<AppHead title="Dashboard" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <Heading
            title="Dashboard"
            description="Ringkasan klasifikasi program fertilitas selanjutnya."
        />
        <Button asChild>
            {#snippet children(props)}
                <Link href={create()} {...props}>Klasifikasi Baru</Link>
            {/snippet}
        </Button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Total Klasifikasi</CardDescription>
                <CardTitle class="text-3xl">{totalClassifications}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Klasifikasi Hari Ini</CardDescription>
                <CardTitle class="text-3xl">{todayClassifications}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Berhasil Diklasifikasikan</CardDescription>
                <CardTitle class="text-3xl">{classifiedCount}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Tidak Ada Rule</CardDescription>
                <CardTitle class="text-3xl">{noRuleCount}</CardTitle>
            </CardHeader>
        </Card>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Distribusi Hasil</CardTitle>
                <CardDescription>
                    Sebaran prediksi Promil Selanjutnya dari klasifikasi Anda.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-3">
                {#if resultDistribution.length === 0}
                    <p class="text-sm text-muted-foreground">Belum ada hasil klasifikasi.</p>
                {:else}
                    {#each resultDistribution as item (item.label)}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span>{item.label}</span>
                                <span class="text-muted-foreground">{item.total}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-primary"
                                    style={`width: ${maxDistribution === 0 ? 0 : (item.total / maxDistribution) * 100}%`}
                                ></div>
                            </div>
                        </div>
                    {/each}
                {/if}
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between gap-3">
                <div>
                    <CardTitle>Lima Klasifikasi Terbaru</CardTitle>
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
                    <p class="text-sm text-muted-foreground">Belum ada klasifikasi.</p>
                {:else}
                    {#each recentClassifications as item (item.id)}
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-3"
                        >
                            <div class="space-y-1">
                                <p class="font-medium">{item.case_code}</p>
                                <p class="text-sm text-muted-foreground">
                                    {item.patient_name} · {item.current_promil?.name ?? '—'}
                                </p>
                                <p class="text-sm">
                                    {item.prediction_result ?? 'Tidak ada rule'}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    variant={item.status === 'classified'
                                        ? 'default'
                                        : 'secondary'}
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
</div>
