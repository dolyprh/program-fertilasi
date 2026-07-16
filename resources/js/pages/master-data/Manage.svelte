<script module lang="ts">
    import { manage } from '@/routes/master-data';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Data Master',
                href: manage('doctors'),
            },
        ],
    };
</script>

<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import MasterDataController from '@/actions/App/Http/Controllers/MasterDataController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { dashboard } from '@/routes';
    import type { MasterDataType, MasterItem } from '@/types';

    let {
        type,
        label,
        description,
        items,
    }: {
        type: MasterDataType;
        label: string;
        description: string;
        items: MasterItem[];
    } = $props();

    let editingId = $state<number | null>(null);

    function startEdit(item: MasterItem): void {
        editingId = item.id;
    }

    function cancelEdit(): void {
        editingId = null;
    }
</script>

<AppHead title={`Kelola ${label}`} />

<div class="flex flex-col gap-6 p-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <Heading title={label} {description} />
        <Button variant="outline" asChild>
            {#snippet children(props)}
                <Link href={dashboard()} {...props}>Dashboard</Link>
            {/snippet}
        </Button>
    </div>

    <Card class="border-sky-200/80 bg-white/95">
        <CardHeader>
            <CardTitle>Tambah Data Baru</CardTitle>
            <CardDescription>Isi kode, nama, urutan, dan status aktif.</CardDescription>
        </CardHeader>
        <CardContent>
            <Form {...MasterDataController.store.form({ type })} class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                {#snippet children({ errors, processing })}
                    <div class="grid gap-2">
                        <Label for="create-code">Kode</Label>
                        <Input id="create-code" name="code" required placeholder="D001 / IVF" />
                        <InputError message={errors.code} />
                    </div>
                    <div class="grid gap-2">
                        <Label for="create-name">Nama</Label>
                        <Input id="create-name" name="name" required placeholder="Nama tampilan" />
                        <InputError message={errors.name} />
                    </div>
                    <div class="grid gap-2">
                        <Label for="create-sort">Urutan</Label>
                        <Input
                            id="create-sort"
                            name="sort_order"
                            type="number"
                            min="0"
                            value="0"
                            required
                        />
                        <InputError message={errors.sort_order} />
                    </div>
                    <div class="grid gap-2">
                        <Label for="create-active">Status</Label>
                        <select
                            id="create-active"
                            name="is_active"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                        <InputError message={errors.is_active} />
                    </div>
                    <div class="flex items-end">
                        <Button type="submit" disabled={processing} class="w-full bg-sky-600 hover:bg-sky-700">
                            {#if processing}<Spinner />{/if}
                            Tambah
                        </Button>
                    </div>
                {/snippet}
            </Form>
        </CardContent>
    </Card>

    <div class="overflow-x-auto rounded-xl border border-sky-200/80 bg-white/95">
        <table class="w-full min-w-[880px] text-left text-sm">
            <thead class="border-b bg-sky-50/80">
                <tr>
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Urutan</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#if items.length === 0}
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                            Belum ada data master.
                        </td>
                    </tr>
                {:else}
                    {#each items as item (item.id)}
                        <tr class="border-b last:border-0">
                            {#if editingId === item.id}
                                <td colspan="5" class="px-4 py-4">
                                    <Form
                                        {...MasterDataController.update.form({ type, id: item.id })}
                                        class="grid gap-4 md:grid-cols-2 xl:grid-cols-6"
                                        options={{ preserveScroll: true }}
                                        onSuccess={cancelEdit}
                                    >
                                        {#snippet children({ errors, processing })}
                                            <div class="grid gap-2">
                                                <Label>Kode</Label>
                                                <Input name="code" value={item.code} required />
                                                <InputError message={errors.code} />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label>Nama</Label>
                                                <Input name="name" value={item.name} required />
                                                <InputError message={errors.name} />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label>Urutan</Label>
                                                <Input
                                                    name="sort_order"
                                                    type="number"
                                                    min="0"
                                                    value={item.sort_order}
                                                    required
                                                />
                                                <InputError message={errors.sort_order} />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label>Status</Label>
                                                <select
                                                    name="is_active"
                                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                                                >
                                                    <option value="1" selected={item.is_active}>Aktif</option>
                                                    <option value="0" selected={!item.is_active}>Nonaktif</option>
                                                </select>
                                                <InputError message={errors.is_active} />
                                            </div>
                                            <div class="flex items-end gap-2 xl:col-span-2">
                                                <Button type="submit" disabled={processing} class="bg-sky-600 hover:bg-sky-700">
                                                    {#if processing}<Spinner />{/if}
                                                    Simpan
                                                </Button>
                                                <Button type="button" variant="outline" onclick={cancelEdit}>
                                                    Batal
                                                </Button>
                                            </div>
                                        {/snippet}
                                    </Form>
                                </td>
                            {:else}
                                <td class="px-4 py-3 font-medium">{item.code}</td>
                                <td class="px-4 py-3">{item.name}</td>
                                <td class="px-4 py-3">{item.sort_order}</td>
                                <td class="px-4 py-3">
                                    <Badge variant={item.is_active ? 'default' : 'secondary'}>
                                        {item.is_active ? 'Aktif' : 'Nonaktif'}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <Button size="sm" variant="outline" onclick={() => startEdit(item)}>
                                            Edit
                                        </Button>
                                        <Form
                                            {...MasterDataController.destroy.form({ type, id: item.id })}
                                            options={{ preserveScroll: true }}
                                        >
                                            {#snippet children({ processing })}
                                                <Button
                                                    size="sm"
                                                    variant="destructive"
                                                    type="submit"
                                                    disabled={processing}
                                                >
                                                    Hapus
                                                </Button>
                                            {/snippet}
                                        </Form>
                                    </div>
                                </td>
                            {/if}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>
</div>
