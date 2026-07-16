<script lang="ts">
    import Sun from 'lucide-svelte/icons/sun';
    import { themeState } from '@/lib/theme.svelte';

    const { appearance } = themeState();
</script>

<div
    class="inline-flex items-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-4 py-2 text-sm text-sky-900"
>
    <Sun class="h-4 w-4" />
    <span>Mode tampilan: <strong>{appearance.value === 'light' ? 'Light' : 'Light'}</strong></span>
</div>

<p class="text-sm text-muted-foreground">
    Aplikasi menggunakan tema terang dengan kombinasi sky blue dan aksen merah lembut.
</p>
