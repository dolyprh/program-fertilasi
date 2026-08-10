<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Activity from 'lucide-svelte/icons/activity';
    import ClipboardList from 'lucide-svelte/icons/clipboard-list';
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
    import { toUrl } from '@/lib/utils';
    import { manage } from '@/routes/master-data';
    import type { MasterDataType } from '@/types';

    const url = currentUrlState();

    const subItems: Array<{
        title: string;
        type: MasterDataType;
        icon: typeof Activity;
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
</script>

<SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel class="px-3 text-xs font-semibold tracking-wider uppercase">
        Data Master
    </SidebarGroupLabel>
    <SidebarMenu>
        {#each subItems as item (item.type)}
            {@const href = manage(item.type)}
            <SidebarMenuItem>
                <SidebarMenuButton
                    asChild
                    class="nav-pill-active rounded-xl"
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
    </SidebarMenu>
</SidebarGroup>
