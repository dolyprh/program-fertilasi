<script lang="ts">
    import type { Component } from 'svelte';
    import { cn } from '@/lib/utils';
    import { Card, CardHeader } from '@/components/ui/card';

    type Tone = 'teal' | 'coral' | 'lilac' | 'amber';

    let {
        label,
        value,
        icon: Icon,
        tone = 'teal',
        class: className = '',
    }: {
        label: string;
        value: string | number;
        icon?: Component<{ class?: string }>;
        tone?: Tone;
        class?: string;
    } = $props();
</script>

<Card class={cn('stat-tone-' + tone, 'overflow-hidden border-0 shadow-none', className)}>
    <CardHeader class="relative pb-4">
        <div
            class="absolute -top-6 -right-6 size-24 rounded-full bg-[var(--stat-ring)] blur-2xl"
            aria-hidden="true"
        ></div>
        <div class="relative flex items-start justify-between gap-3">
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">{label}</p>
                <p class="text-3xl font-bold tracking-tight text-foreground">{value}</p>
            </div>
            {#if Icon}
                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-[var(--stat-bg)] text-[var(--stat-icon)] ring-1 ring-[var(--stat-ring)]"
                >
                    <Icon class="size-5" />
                </div>
            {/if}
        </div>
    </CardHeader>
</Card>
