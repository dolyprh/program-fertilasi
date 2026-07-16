<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Activity from 'lucide-svelte/icons/activity';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ClipboardList from 'lucide-svelte/icons/clipboard-list';
    import Database from 'lucide-svelte/icons/database';
    import ListTree from 'lucide-svelte/icons/list-tree';
    import Stethoscope from 'lucide-svelte/icons/stethoscope';
    import {
        SidebarGroup,
        SidebarGroupLabel,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { currentUrlState } from '@/lib/currentUrl.svelte';
    import { cn, toUrl } from '@/lib/utils';
    import { manage } from '@/routes/master-data';
    import type { MasterDataType } from '@/types';

    const url = currentUrlState();
    const masterBasePath = '/master-data';

    let open = $state(url.currentUrl.startsWith(masterBasePath));
    let lastPath = $state(url.currentUrl);

    const subItems: Array<{
        title: string;
        type: MasterDataType;
        icon: typeof Database;
    }> = [
        {
            title: 'Dokter Pemeriksa',
            type: 'doctors',
            icon: Stethoscope,
        },
        {
            title: 'Promil Saat Ini',
            type: 'current-promils',
            icon: Activity,
        },
        {
            title: 'Tindakan Hari Ini',
            type: 'current-actions',
            icon: ClipboardList,
        },
        {
            title: 'PoA1',
            type: 'poa1-options',
            icon: ListTree,
        },
    ];

    $effect(() => {
        const currentPath = url.currentUrl;

        if (currentPath !== lastPath) {
            if (currentPath.startsWith(masterBasePath)) {
                open = true;
            }

            lastPath = currentPath;
        }
    });

    const isMasterSectionActive = $derived(url.currentUrl.startsWith(masterBasePath));

    function toggleOpen(event: MouseEvent): void {
        event.preventDefault();
        event.stopPropagation();
        open = !open;
    }
</script>

<SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Referensi</SidebarGroupLabel>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton
                isActive={isMasterSectionActive}
                onclick={toggleOpen}
                type="button"
                data-state={open ? 'open' : 'closed'}
            >
                <Database class="size-4 shrink-0" />
                <span>Data Master</span>
                <ChevronDown
                    class={cn(
                        'ml-auto size-4 shrink-0 transition-transform duration-200',
                        open && 'rotate-180',
                    )}
                />
            </SidebarMenuButton>
        </SidebarMenuItem>

        {#if open}
            {#each subItems as item (item.type)}
                {@const href = manage(item.type)}
                <SidebarMenuItem>
                    <SidebarMenuButton
                        asChild
                        class="pl-6"
                        isActive={url.isCurrentUrl(href, url.currentUrl)}
                        tooltip={item.title}
                    >
                        {#snippet children(props)}
                            <Link {...props} href={toUrl(href)} class={props.class}>
                                <item.icon class="size-4 shrink-0" />
                                <span>{item.title}</span>
                            </Link>
                        {/snippet}
                    </SidebarMenuButton>
                </SidebarMenuItem>
            {/each}
        {/if}
    </SidebarMenu>
</SidebarGroup>
