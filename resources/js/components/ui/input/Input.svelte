<script lang="ts">
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { cn } from '@/lib/utils';

    let {
        class: className = '',
        type = 'text',
        value = $bindable(),
        ...rest
    }: HTMLInputAttributes = $props();
</script>

<input
    {type}
    bind:value
    class={cn(
        'flex h-10 w-full rounded-xl border border-input bg-background/70 px-3 py-2 text-sm shadow-xs backdrop-blur-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:border-primary/40 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50',
        className,
    )}
    {...rest}
/>
