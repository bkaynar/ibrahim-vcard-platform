<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Users, User, Eye, Settings, Landmark } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

// Kullanıcı rolüne göre nav items
const mainNavItems = computed((): NavItem[] => {
    const user = page.props.auth?.user;
    const isAdmin = user?.main_role?.toLowerCase() === 'admin';

    if (isAdmin) {
        // Admin menüleri
        return [
            {
                title: 'Yönetim Paneli',
                href: route('admin.dashboard'),
                icon: LayoutGrid,
            },
            {
                title: 'Kullanıcılar',
                href: route('admin.users.index'),
                icon: Users,
            },
            {
                title: 'Bankalar',
                href: route('admin.banks.index'),
                icon: Landmark,
            },
            {
                title: 'Sistem Ayarları',
                href: route('admin.settings.index'),
                icon: Settings,
            }
        ];
    } else {
        // Normal kullanıcı menüleri
        return [
            {
                title: 'Ana Sayfa',
                href: route('dashboard'),
                icon: LayoutGrid,
            },
            {
                title: 'Profilim',
                href: route('user.profile.edit'),
                icon: User,
            },
            {
                title: 'VCard İstatistikleri',
                href: route('user.statistics'),
                icon: Eye,
            }
        ];
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
