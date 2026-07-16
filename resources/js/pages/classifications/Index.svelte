<script module lang="ts">
    import { index as classificationsIndex } from '@/routes/classifications';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Riwayat Klasifikasi',
                href: classificationsIndex(),
            },
        ],
    };
</script>

<script lang="ts">
    import { Form, Link, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { create, index, show } from '@/routes/classifications';
    import { formatVisitDate } from '@/lib/utils';
    import type {
        Classification,
        ClassificationFilters,
        CurrentPromil,
        PaginatedData,
    } from '@/types';

    let {
        classifications,
        filters,
        currentPromils,
        predictionResults,
        statuses,
    }: {
        classifications: PaginatedData<Classification>;
        filters: ClassificationFilters;
        currentPromils: CurrentPromil[];
        predictionResults: string[];
        statuses: Array<{ value: string; label: string }>;
    } = $props();

    function clearFilters(): void {
        router.get(index.url());
    }
</script>

<AppHead title="Riwayat Klasifikasi" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <Heading
            title="Riwayat Klasifikasi"
            description="Daftar klasifikasi yang telah Anda proses."
        />
        <Button asChild>
            {#snippet children(props)}
                <Link href={create()} {...props}>Klasifikasi Baru</Link>
            {/snippet}
        </Button>
    </div>

    <Form
        {...index.form()}
        class="grid gap-4 rounded-xl border p-4 md:grid-cols-2 xl:grid-cols-4"
    >
        {#snippet children({ processing })}
            <div class="grid gap-2">
                <Label for="current_promil_id">Promil Saat Ini</Label>
                <select
                    id="current_promil_id"
                    name="current_promil_id"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                >
                    <option value="">Semua</option>
                    {#each currentPromils as promil (promil.id)}
                        <option
                            value={promil.id}
                            selected={String(filters.current_promil_id) === String(promil.id)}
                        >
                            {promil.name}
                        </option>
                    {/each}
                </select>
            </div>
            <div class="grid gap-2">
                <Label for="prediction_result">Hasil</Label>
                <select
                    id="prediction_result"
                    name="prediction_result"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                >
                    <option value="">Semua</option>
                    {#each predictionResults as result (result)}
                        <option
                            value={result}
                            selected={filters.prediction_result === result}
                        >
                            {result}
                        </option>
                    {/each}
                </select>
            </div>
            <div class="grid gap-2">
                <Label for="status">Status</Label>
                <select
                    id="status"
                    name="status"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                >
                    <option value="">Semua</option>
                    {#each statuses as status (status.value)}
                        <option
                            value={status.value}
                            selected={filters.status === status.value}
                        >
                            {status.label}
                        </option>
                    {/each}
                </select>
            </div>
            <div class="flex items-end gap-2">
                <Button type="submit" disabled={processing}>Filter</Button>
                <Button type="button" variant="outline" onclick={clearFilters}>Reset</Button>
            </div>
        {/snippet}
    </Form>

    <div class="overflow-x-auto rounded-xl border">
        <table class="w-full min-w-[880px] text-left text-sm">
            <thead class="border-b bg-muted/40">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama Pasien</th>
                    <th class="px-4 py-3 font-medium">Tanggal Kunjungan</th>
                    <th class="px-4 py-3 font-medium">Umur</th>
                    <th class="px-4 py-3 font-medium">Dokter</th>
                    <th class="px-4 py-3 font-medium">Promil Saat Ini</th>
                    <th class="px-4 py-3 font-medium">Hasil</th>
                    <th class="px-4 py-3 font-medium">Rule</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#if classifications.data.length === 0}
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-muted-foreground">
                            Belum ada data klasifikasi.
                        </td>
                    </tr>
                {:else}
                    {#each classifications.data as item (item.id)}
                        <tr class="border-b last:border-0">
                            <td class="px-4 py-3 font-medium">{item.patient_name}</td>
                            <td class="px-4 py-3">{formatVisitDate(item.visit_date)}</td>
                            <td class="px-4 py-3">{item.age}</td>
                            <td class="px-4 py-3">{item.doctor?.name ?? '—'}</td>
                            <td class="px-4 py-3">{item.current_promil?.name ?? '—'}</td>
                            <td class="px-4 py-3">{item.prediction_result ?? '—'}</td>
                            <td class="px-4 py-3">{item.rule_code ?? '—'}</td>
                            <td class="px-4 py-3">
                                <Badge
                                    variant={item.status === 'classified'
                                        ? 'default'
                                        : 'secondary'}
                                >
                                    {item.status === 'classified' ? 'Classified' : 'No Rule'}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Button variant="outline" size="sm" asChild>
                                    {#snippet children(props)}
                                        <Link href={show(item.id)} {...props}>Lihat Detail</Link>
                                    {/snippet}
                                </Button>
                            </td>
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>

    {#if classifications.last_page > 1}
        <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
            <p class="text-muted-foreground">
                Menampilkan {classifications.from ?? 0}–{classifications.to ?? 0} dari
                {classifications.total} data
            </p>
            <div class="flex flex-wrap gap-1">
                {#each classifications.links as link, i (i)}
                    {@const pageUrl = link.url}
                    {#if pageUrl}
                        <Button
                            size="sm"
                            variant={link.active ? 'default' : 'outline'}
                            asChild
                        >
                            {#snippet children(props)}
                                <Link href={pageUrl} {...props} preserveScroll>
                                    {@html link.label}
                                </Link>
                            {/snippet}
                        </Button>
                    {:else}
                        <Button size="sm" variant="outline" disabled>
                            {@html link.label}
                        </Button>
                    {/if}
                {/each}
            </div>
        </div>
    {/if}
</div>
