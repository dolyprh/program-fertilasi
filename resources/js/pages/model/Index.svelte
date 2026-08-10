<script module lang="ts">
    import { modelInformation } from '@/routes';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Informasi Model',
                href: modelInformation(),
            },
        ],
    };
</script>

<script lang="ts">
    import Brain from 'lucide-svelte/icons/brain';
    import Database from 'lucide-svelte/icons/database';
    import GitBranch from 'lucide-svelte/icons/git-branch';
    import Target from 'lucide-svelte/icons/target';
    import AppHead from '@/components/AppHead.svelte';
    import PageHeader from '@/components/PageHeader.svelte';
    import PageShell from '@/components/PageShell.svelte';
    import StatCard from '@/components/StatCard.svelte';
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import type { DecisionRule, ModelInformation } from '@/types';

    let {
        model,
        rules,
    }: {
        model: ModelInformation;
        rules: DecisionRule[];
    } = $props();

    const formatPercent = (value: number): string =>
        value.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
</script>

<AppHead title="Informasi Model" />

<PageShell>
    <PageHeader
        title="Informasi Model"
        description="Metadata model Decision Tree C4.5 hasil penelitian RapidMiner."
    />

    <Alert class="rounded-2xl border-teal-200/60 bg-teal-50/80 dark:border-teal-900/40 dark:bg-teal-950/30">
        <AlertTitle class="text-teal-800 dark:text-teal-200">Catatan Penggunaan Data Latih</AlertTitle>
        <AlertDescription class="text-teal-700/90 dark:text-teal-100/80">
            Data latih sebanyak {model.training_count} data digunakan untuk membentuk pohon keputusan
            di RapidMiner (evaluasi pada {model.testing_count} data uji). Aplikasi tidak membaca ulang
            data latih ketika proses klasifikasi dijalankan. Aplikasi hanya menerapkan decision rule
            yang dihasilkan oleh model.
        </AlertDescription>
    </Alert>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Algoritma" value={model.algorithm} icon={Brain} tone="teal" />
        <StatCard label="Criterion" value={model.criterion} icon={GitBranch} tone="lilac" />
        <StatCard label="Data Latih" value={model.training_count} icon={Database} tone="coral" />
        <StatCard label="Data Uji" value={model.testing_count} icon={Database} tone="amber" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Accuracy" value="{formatPercent(model.accuracy)}%" icon={Target} tone="teal" />
        <StatCard label="Macro Precision" value="{formatPercent(model.macro_precision)}%" icon={Target} tone="coral" />
        <StatCard label="Macro Recall" value="{formatPercent(model.macro_recall)}%" icon={Target} tone="lilac" />
        <StatCard label="Macro F1-score" value="{formatPercent(model.macro_f1)}%" icon={Target} tone="amber" />
    </div>

    <Card>
        <CardHeader>
            <CardTitle>Parameter Model</CardTitle>
            <CardDescription>Konfigurasi pembentukan pohon keputusan.</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
            {#each [
                ['Root node', model.root_node],
                ['Maximal depth', model.maximal_depth],
                ['Minimal gain', model.minimal_gain],
                ['Minimal leaf size', model.minimal_leaf_size],
                ['Pruning', model.pruning ? 'Ya' : 'Tidak'],
                ['Prepruning', model.prepruning ? 'Ya' : 'Tidak'],
            ] as [label, value] (label)}
                <div class="flex justify-between gap-3 rounded-xl border border-border/60 bg-background/50 p-3.5">
                    <span class="text-muted-foreground">{label}</span>
                    <span class="font-semibold text-foreground">{value}</span>
                </div>
            {/each}
        </CardContent>
    </Card>

    <Card>
        <CardHeader>
            <CardTitle>{rules.length} Decision Rule</CardTitle>
            <CardDescription>
                Rule yang diterapkan aplikasi saat proses klasifikasi.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            {#each rules as rule (rule.code)}
                <div class="list-item-soft flex-col items-stretch sm:flex-row sm:items-start">
                    <div class="mb-2 flex w-full flex-wrap items-center justify-between gap-2">
                        <span class="rounded-lg bg-primary/10 px-2.5 py-1 text-sm font-bold text-primary">
                            {rule.code}
                        </span>
                        <span class="text-sm font-semibold text-foreground">{rule.prediction}</span>
                    </div>
                    <ul class="mb-3 w-full list-disc space-y-1 pl-5 text-sm text-muted-foreground">
                        {#each rule.conditions as condition (condition)}
                            <li>{condition}</li>
                        {/each}
                    </ul>
                    <div class="grid w-full gap-2 text-sm sm:grid-cols-3">
                        <p>
                            <span class="text-muted-foreground">Confidence:</span>
                            <span class="font-medium"> {formatPercent(rule.confidence)}%</span>
                        </p>
                        <p>
                            <span class="text-muted-foreground">Support count:</span>
                            <span class="font-medium"> {rule.support_count}</span>
                        </p>
                        <p>
                            <span class="text-muted-foreground">Support:</span>
                            <span class="font-medium"> {formatPercent(rule.support_percentage)}%</span>
                        </p>
                    </div>
                </div>
            {/each}
        </CardContent>
    </Card>
</PageShell>
