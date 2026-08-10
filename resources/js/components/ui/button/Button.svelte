<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    type Variant =
        | 'default'
        | 'secondary'
        | 'ghost'
        | 'destructive'
        | 'outline'
        | 'link';
    type Size = 'default' | 'sm' | 'lg' | 'icon';
    type AsChildProps = {
        class?: string;
        onClick?: (event: MouseEvent) => void;
        [key: string]: any;
    };

    const base =
        'inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 active:scale-[0.98]';

    const variants: Record<Variant, string> = {
        default: 'bg-primary text-primary-foreground shadow-md shadow-primary/25 hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/30',
        secondary:
            'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
        ghost: 'hover:bg-accent hover:text-accent-foreground',
        destructive:
            'bg-destructive text-destructive-foreground shadow-md shadow-destructive/20 hover:bg-destructive/90',
        outline: 'border border-input bg-background/60 backdrop-blur-sm hover:border-primary/30 hover:bg-primary/5 hover:text-foreground',
        link: 'text-primary underline-offset-4 hover:underline',
    };

    const sizes: Record<Size, string> = {
        default: 'h-10 px-5 py-2',
        sm: 'h-8 rounded-lg px-3.5 text-xs',
        lg: 'h-11 rounded-xl px-8',
        icon: 'size-10 rounded-xl',
    };

    let {
        children,
        asChild = false,
        variant = 'default',
        size = 'default',
        class: className = '',
        type = 'button',
        ...rest
    }: {
        children?: Snippet<[AsChildProps]>;
        asChild?: boolean;
        variant?: Variant;
        size?: Size;
        class?: string;
        type?: 'button' | 'submit' | 'reset';
        [key: string]: unknown;
    } = $props();

    const classes = () => cn(base, variants[variant], sizes[size], className);
</script>

{#if asChild}
    {@render children?.({ class: classes(), ...rest })}
{:else}
    <button class={classes()} type={type} {...rest}>
        {@render children?.({})}
    </button>
{/if}
