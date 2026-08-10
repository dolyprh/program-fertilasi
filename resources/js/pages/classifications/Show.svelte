<script module lang="ts">
    import { index as classificationsIndex } from '@/routes/classifications';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Riwayat Klasifikasi',
                href: classificationsIndex(),
            },
            {
                title: 'Detail Hasil',
                href: '#',
            },
        ],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import PageHeader from '@/components/PageHeader.svelte';
    import PageShell from '@/components/PageShell.svelte';
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { create, index } from '@/routes/classifications';
    import { formatVisitDate } from '@/lib/utils';
    import type { Classification, TrainingAgeRange } from '@/types';

    let {
        classification,
        trainingAgeRange,
        trainingCount,
    }: {
        classification: Classification;
        trainingAgeRange: TrainingAgeRange;
        trainingCount: number;
    } = $props();

    const formatPercent = (value: string | number | null): string => {
        if (value === null || value === undefined) {
            return '—';
        }

        return Number(value).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    const isClassified = $derived(classification.status === 'classified');
    const ageOutsideTraining = $derived(
        classification.age < trainingAgeRange.min ||
            classification.age > trainingAgeRange.max,
    );
</script>

<AppHead title={`Hasil ${classification.case_code}`} />

<PageShell>
    <PageHeader title="Hasil Klasifikasi" description={`Case code ${classification.case_code}`}>
        {#snippet actions()}
            <Button variant="outline" asChild>
                {#snippet children(props)}
                    <Link href={index()} {...props}>Riwayat</Link>
                {/snippet}
            </Button>
            <Button asChild>
                {#snippet children(props)}
                    <Link href={create()} {...props}>Klasifikasi Baru</Link>
                {/snippet}
            </Button>
        {/snippet}
    </PageHeader>

    <div class="grid gap-6 lg:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Informasi Kunjungan</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Case Code</span>
                    <span class="font-medium">{classification.case_code}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Nama Pasien</span>
                    <span class="font-medium">{classification.patient_name}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Tanggal Kunjungan</span>
                    <span class="font-medium">{formatVisitDate(classification.visit_date)}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Tanggal Lahir</span>
                    <span class="font-medium">{formatVisitDate(classification.date_of_birth)}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Umur</span>
                    <span class="font-medium">{classification.age} tahun</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Dokter</span>
                    <span class="font-medium">{classification.doctor?.name ?? '—'}</span>
                </div>
                {#if ageOutsideTraining}
                    <p class="text-amber-600 dark:text-amber-400">
                        Umur di luar rentang data latih ({trainingAgeRange.min}–
                        {trainingAgeRange.max} tahun).
                    </p>
                {/if}
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Data Klasifikasi</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Promil Saat Ini</span>
                    <span class="font-medium">{classification.current_promil?.name ?? '—'}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Tindakan Hari Ini</span>
                    <span class="font-medium">{classification.current_action?.name ?? '—'}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">PoA1</span>
                    <span class="font-medium">{classification.poa1_option?.name ?? '—'}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Status</span>
                    <Badge variant={isClassified ? 'default' : 'secondary'}>
                        {classification.status === 'classified' ? 'Classified' : 'No Rule'}
                    </Badge>
                </div>
            </CardContent>
        </Card>
    </div>

    <Card>
        <CardHeader>
            <CardTitle>Hasil Prediksi</CardTitle>
            <CardDescription>
                Prediksi dihasilkan oleh DecisionTreeService berdasarkan decision rule C4.5.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            {#if isClassified}
                <div class="space-y-2 rounded-2xl border border-primary/20 bg-primary/5 p-5">
                    <p class="text-sm font-medium text-muted-foreground">Hasil Klasifikasi</p>
                    <p class="text-3xl font-bold tracking-tight text-primary">
                        {classification.prediction_result}
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-border/60 bg-background/50 p-3.5">
                        <p class="text-xs text-muted-foreground">Rule</p>
                        <p class="font-medium">{classification.rule_code}</p>
                    </div>
                    <div class="rounded-xl border border-border/60 bg-background/50 p-3.5">
                        <p class="text-xs text-muted-foreground">Confidence</p>
                        <p class="font-medium">{formatPercent(classification.confidence)}%</p>
                    </div>
                    <div class="rounded-xl border border-border/60 bg-background/50 p-3.5">
                        <p class="text-xs text-muted-foreground">Dukungan</p>
                        <p class="font-medium">
                            {classification.support_count} dari {trainingCount} data latih
                        </p>
                    </div>
                    <div class="rounded-xl border border-border/60 bg-background/50 p-3.5">
                        <p class="text-xs text-muted-foreground">Support</p>
                        <p class="font-medium">
                            {formatPercent(classification.support_percentage)}%
                        </p>
                    </div>
                </div>
            {:else}
                <Alert>
                    <AlertTitle>Tidak Ada Rule</AlertTitle>
                    <AlertDescription>
                        Tidak ditemukan decision rule yang sesuai dengan kombinasi data yang
                        dimasukkan.
                    </AlertDescription>
                </Alert>
            {/if}

            <div>
                <p class="mb-2 text-sm font-medium">Jalur Keputusan</p>
                <ol class="list-decimal space-y-1 pl-5 text-sm text-muted-foreground">
                    {#each classification.decision_path ?? [] as step (step)}
                        <li>{step}</li>
                    {/each}
                </ol>
            </div>
        </CardContent>
    </Card>

    <Alert class="rounded-2xl border-amber-200/60 bg-amber-50/80 dark:border-amber-900/40 dark:bg-amber-950/20">
        <AlertTitle class="text-amber-900 dark:text-amber-100">Disclaimer</AlertTitle>
        <AlertDescription class="text-amber-800/90 dark:text-amber-100/80">
            Hasil klasifikasi merupakan pola berdasarkan data historis penelitian dan tidak
            menggantikan keputusan dokter.
        </AlertDescription>
    </Alert>
</PageShell>
