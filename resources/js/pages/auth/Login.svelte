<script module lang="ts">
    export const layout = null;
</script>

<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import AuthLoginShell from '@/components/AuthLoginShell.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store } from '@/routes/login';
    import { request } from '@/routes/password';

    let {
        status = '',
        canResetPassword,
    }: {
        status?: string;
        canResetPassword: boolean;
    } = $props();
</script>

<AppHead title="Masuk" />

<AuthLoginShell>
    <div class="space-y-6">
        <div class="space-y-2 text-center lg:text-left">
            <h2 class="text-2xl font-semibold tracking-tight">Masuk ke Sistem</h2>
            <p class="text-sm text-muted-foreground">
                Gunakan akun internal untuk mengakses dashboard dan fitur klasifikasi.
            </p>
        </div>

        {#if status}
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300"
            >
                {status}
            </div>
        {/if}

        <Form {...store.form()} resetOnSuccess={['password']} class="space-y-5">
            {#snippet children({ errors, processing })}
                <div class="grid gap-2">
                    <Label for="email">Username</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autocomplete="username"
                        placeholder="admin@admin.com"
                    />
                    <InputError message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        {#if canResetPassword}
                            <TextLink href={request()} class="text-sm">
                                Lupa password?
                            </TextLink>
                        {/if}
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    />
                    <InputError message={errors.password} />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center gap-3">
                        <Checkbox id="remember" name="remember" />
                        <span>Ingat saya</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="h-11 w-full"
                    disabled={processing}
                    data-test="login-button"
                >
                    {#if processing}
                        <Spinner />
                    {/if}
                    Masuk ke Dashboard
                </Button>
            {/snippet}
        </Form>

        <div
            class="rounded-xl border border-dashed border-primary/25 bg-primary/5 px-4 py-3 text-sm text-foreground"
        >
            <p class="font-medium">Akun default pengembangan</p>
            <p class="mt-1 text-muted-foreground">
                Username: <span class="font-semibold text-foreground">admin@admin.com</span> · Password:
                <span class="font-semibold text-foreground">admin</span>
            </p>
        </div>
    </div>
</AuthLoginShell>
