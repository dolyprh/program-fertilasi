<script module lang="ts">
    import { create } from '@/routes/classifications';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Klasifikasi Baru',
                href: create(),
            },
        ],
    };
</script>

<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import ClassificationController from '@/actions/App/Http/Controllers/ClassificationController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
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
    import type {
        CurrentAction,
        CurrentPromil,
        Doctor,
        Poa1Option,
        TrainingAgeRange,
    } from '@/types';

    let {
        doctors,
        currentPromils,
        currentActions,
        poa1Options,
        trainingAgeRange,
    }: {
        doctors: Doctor[];
        currentPromils: CurrentPromil[];
        currentActions: CurrentAction[];
        poa1Options: Poa1Option[];
        trainingAgeRange: TrainingAgeRange;
    } = $props();

    let visitDate = $state(new Date().toISOString().slice(0, 10));
    let dateOfBirth = $state('');

    const previewAge = $derived.by(() => {
        if (!visitDate || !dateOfBirth) {
            return null;
        }

        const birth = new Date(`${dateOfBirth}T00:00:00`);
        const visit = new Date(`${visitDate}T00:00:00`);

        if (Number.isNaN(birth.getTime()) || Number.isNaN(visit.getTime()) || birth > visit) {
            return null;
        }

        let age = visit.getFullYear() - birth.getFullYear();
        const monthDiff = visit.getMonth() - birth.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && visit.getDate() < birth.getDate())) {
            age -= 1;
        }

        return age;
    });

    const ageOutsideTraining = $derived(
        previewAge !== null &&
            (previewAge < trainingAgeRange.min || previewAge > trainingAgeRange.max),
    );

    const canSubmit = $derived(poa1Options.length > 0);
</script>

<AppHead title="Klasifikasi Baru" />

<div class="flex flex-col gap-6 p-4">
    <Heading
        title="Klasifikasi Baru"
        description="Masukkan informasi kunjungan dan data klasifikasi untuk menghasilkan prediksi Promil Selanjutnya."
    />

    {#if !canSubmit}
        <Alert variant="destructive">
            <AlertTitle>Master PoA1 belum tersedia</AlertTitle>
            <AlertDescription>
                Tidak ada opsi PoA1 aktif. Lengkapi seeder PoA1 sebelum menjalankan klasifikasi.
            </AlertDescription>
        </Alert>
    {/if}

    <Form {...ClassificationController.store.form()} class="space-y-6">
        {#snippet children({ errors, processing })}
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Kunjungan</CardTitle>
                        <CardDescription>
                            Data dasar kunjungan pasien. Umur dihitung otomatis dari tanggal lahir
                            terhadap tanggal kunjungan.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="patient_name">Nama Lengkap</Label>
                            <Input
                                id="patient_name"
                                name="patient_name"
                                required
                                autocomplete="name"
                                placeholder="Nama lengkap pasien"
                            />
                            <InputError message={errors.patient_name} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="visit_date">Tanggal Kunjungan</Label>
                            <Input
                                id="visit_date"
                                name="visit_date"
                                type="date"
                                required
                                bind:value={visitDate}
                            />
                            <InputError message={errors.visit_date} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="date_of_birth">Tanggal Lahir</Label>
                            <Input
                                id="date_of_birth"
                                name="date_of_birth"
                                type="date"
                                required
                                bind:value={dateOfBirth}
                            />
                            <InputError message={errors.date_of_birth} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="age_preview">Umur</Label>
                            <Input
                                id="age_preview"
                                type="text"
                                readonly
                                tabindex={-1}
                                value={previewAge === null ? '—' : `${previewAge} tahun`}
                                class="bg-muted"
                            />
                            {#if ageOutsideTraining}
                                <p class="text-sm text-amber-600 dark:text-amber-400">
                                    Umur di luar rentang data latih ({trainingAgeRange.min}–
                                    {trainingAgeRange.max} tahun). Klasifikasi tetap dapat diproses.
                                </p>
                            {/if}
                        </div>

                        <div class="grid gap-2">
                            <Label for="doctor_id">Dokter Pemeriksa</Label>
                            <select
                                id="doctor_id"
                                name="doctor_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="">Pilih dokter</option>
                                {#each doctors as doctor (doctor.id)}
                                    <option value={doctor.id}>{doctor.name}</option>
                                {/each}
                            </select>
                            <InputError message={errors.doctor_id} />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Data Klasifikasi</CardTitle>
                        <CardDescription>
                            Atribut yang digunakan untuk menerapkan decision rule C4.5. PoA1
                            disimpan sebagai atribut penelitian.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="current_promil_id">Promil Saat Ini</Label>
                            <select
                                id="current_promil_id"
                                name="current_promil_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="">Pilih promil saat ini</option>
                                {#each currentPromils as promil (promil.id)}
                                    <option value={promil.id}>{promil.name}</option>
                                {/each}
                            </select>
                            <InputError message={errors.current_promil_id} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="current_action_id">Tindakan Hari Ini</Label>
                            <select
                                id="current_action_id"
                                name="current_action_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="">Pilih tindakan</option>
                                {#each currentActions as action (action.id)}
                                    <option value={action.id}>{action.name}</option>
                                {/each}
                            </select>
                            <InputError message={errors.current_action_id} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="poa1_option_id">PoA1</Label>
                            <select
                                id="poa1_option_id"
                                name="poa1_option_id"
                                required
                                disabled={!canSubmit}
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:opacity-50"
                            >
                                <option value="">Pilih PoA1</option>
                                {#each poa1Options as option (option.id)}
                                    <option value={option.id}>{option.name}</option>
                                {/each}
                            </select>
                            <InputError message={errors.poa1_option_id} />
                            <p class="text-xs text-muted-foreground">
                                PoA1 tidak digunakan pada pohon keputusan final.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <Button type="submit" disabled={processing || !canSubmit}>
                    {#if processing}
                        <Spinner />
                    {/if}
                    Proses Klasifikasi
                </Button>
                <Button variant="outline" asChild>
                    {#snippet children(props)}
                        <Link href={dashboard()} {...props}>Batal</Link>
                    {/snippet}
                </Button>
            </div>
        {/snippet}
    </Form>
</div>
