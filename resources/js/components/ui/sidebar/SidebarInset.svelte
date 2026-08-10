<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let { class: className = '', children }: { class?: string; children?: Snippet } = $props();
</script>

<main
    data-slot="sidebar-inset"
    class={cn(
        'relative flex w-full flex-1 flex-col bg-transparent',
        'md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-2xl md:peer-data-[variant=inset]:bg-background/40 md:peer-data-[variant=inset]:shadow-[0_12px_40px_-12px_hsl(168_40%_30%_/_0.15)] md:peer-data-[variant=inset]:backdrop-blur-sm md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-2',
        className,
    )}
>
    {@render children?.()}
</main>
