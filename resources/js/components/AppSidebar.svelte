<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ClipboardPlus from 'lucide-svelte/icons/clipboard-plus';
    import GitBranch from 'lucide-svelte/icons/git-branch';
    import History from 'lucide-svelte/icons/history';
    import LayoutDashboard from 'lucide-svelte/icons/layout-dashboard';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavMasterMenu from '@/components/NavMasterMenu.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard, modelInformation } from '@/routes';
    import { create, index } from '@/routes/classifications';
    import type { NavItem } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutDashboard,
        },
        {
            title: 'Klasifikasi Baru',
            href: create(),
            icon: ClipboardPlus,
        },
        {
            title: 'Riwayat Klasifikasi',
            href: index(),
            icon: History,
        },
        {
            title: 'Informasi Model',
            href: modelInformation(),
            icon: GitBranch,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={toUrl(dashboard())}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
        <NavMasterMenu />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={[]} />
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
