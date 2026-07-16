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
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
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

<div class="flex flex-col gap-6 p-4">
    <Heading
        title="Informasi Model"
        description="Metadata model Decision Tree C4.5 hasil penelitian RapidMiner."
    />

    <Alert>
        <AlertTitle>Catatan Penggunaan Data Latih</AlertTitle>
        <AlertDescription>
            Data latih sebanyak 101 data digunakan untuk membentuk pohon keputusan di RapidMiner.
            Aplikasi tidak membaca ulang data latih ketika proses klasifikasi dijalankan. Aplikasi
            hanya menerapkan decision rule yang dihasilkan oleh model.
        </AlertDescription>
    </Alert>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Algoritma</CardDescription>
                <CardTitle class="text-lg">{model.algorithm}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Criterion</CardDescription>
                <CardTitle class="text-lg">{model.criterion}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Data Latih</CardDescription>
                <CardTitle class="text-lg">{model.training_count}</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Data Uji</CardDescription>
                <CardTitle class="text-lg">{model.testing_count}</CardTitle>
            </CardHeader>
        </Card>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Accuracy</CardDescription>
                <CardTitle class="text-lg">{formatPercent(model.accuracy)}%</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Macro Precision</CardDescription>
                <CardTitle class="text-lg">{formatPercent(model.macro_precision)}%</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Macro Recall</CardDescription>
                <CardTitle class="text-lg">{formatPercent(model.macro_recall)}%</CardTitle>
            </CardHeader>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardDescription>Macro F1-score</CardDescription>
                <CardTitle class="text-lg">{formatPercent(model.macro_f1)}%</CardTitle>
            </CardHeader>
        </Card>
    </div>

    <Card>
        <CardHeader>
            <CardTitle>Parameter Model</CardTitle>
            <CardDescription>Konfigurasi pembentukan pohon keputusan.</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Root node</span>
                <span class="font-medium">{model.root_node}</span>
            </div>
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Maximal depth</span>
                <span class="font-medium">{model.maximal_depth}</span>
            </div>
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Minimal gain</span>
                <span class="font-medium">{model.minimal_gain}</span>
            </div>
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Minimal leaf size</span>
                <span class="font-medium">{model.minimal_leaf_size}</span>
            </div>
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Pruning</span>
                <span class="font-medium">{model.pruning ? 'Ya' : 'Tidak'}</span>
            </div>
            <div class="flex justify-between gap-3 rounded-lg border p-3">
                <span class="text-muted-foreground">Prepruning</span>
                <span class="font-medium">{model.prepruning ? 'Ya' : 'Tidak'}</span>
            </div>
        </CardContent>
    </Card>

    <Card>
        <CardHeader>
            <CardTitle>14 Decision Rule</CardTitle>
            <CardDescription>
                Rule yang diterapkan aplikasi saat proses klasifikasi.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            {#each rules as rule (rule.code)}
                <div class="rounded-xl border p-4">
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                        <p class="font-semibold">{rule.code}</p>
                        <p class="text-sm font-medium">{rule.prediction}</p>
                    </div>
                    <ul class="mb-3 list-disc space-y-1 pl-5 text-sm text-muted-foreground">
                        {#each rule.conditions as condition (condition)}
                            <li>{condition}</li>
                        {/each}
                    </ul>
                    <div class="grid gap-2 text-sm sm:grid-cols-3">
                        <p>
                            <span class="text-muted-foreground">Confidence:</span>
                            {formatPercent(rule.confidence)}%
                        </p>
                        <p>
                            <span class="text-muted-foreground">Support count:</span>
                            {rule.support_count}
                        </p>
                        <p>
                            <span class="text-muted-foreground">Support:</span>
                            {formatPercent(rule.support_percentage)}%
                        </p>
                    </div>
                </div>
            {/each}
        </CardContent>
    </Card>
</div>
