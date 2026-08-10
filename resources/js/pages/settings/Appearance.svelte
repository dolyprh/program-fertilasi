<script module lang="ts">
    import { edit as editAppearance } from '@/routes/appearance';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: editAppearance(),
            },
        ],
    };
</script>

<script lang="ts">
    import AppearanceTabs from '@/components/AppearanceTabs.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import PageHeader from '@/components/PageHeader.svelte';
    import PageShell from '@/components/PageShell.svelte';
</script>

<AppHead title="Appearance settings" />

<h1 class="sr-only">Appearance settings</h1>

<PageShell class="max-w-3xl">
    <PageHeader
        title="Appearance settings"
        description="Update the appearance settings for your account"
    />
    <AppearanceTabs />
</PageShell>
