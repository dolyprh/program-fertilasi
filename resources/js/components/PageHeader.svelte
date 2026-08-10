<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let {
        title,
        description = '',
        actions,
        class: className = '',
    }: {
        title: string;
        description?: string;
        actions?: Snippet;
        class?: string;
    } = $props();
</script>

<div class={cn('flex flex-wrap items-start justify-between gap-4', className)}>
    <div class="space-y-1.5">
        <h1 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
            {title}
        </h1>
        {#if description}
            <p class="max-w-2xl text-sm leading-relaxed text-muted-foreground md:text-base">
                {description}
            </p>
        {/if}
    </div>
    {#if actions}
        <div class="flex flex-wrap items-center gap-2">
            {@render actions()}
        </div>
    {/if}
</div>
