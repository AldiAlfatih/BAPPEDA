<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

defineProps<{
  items: NavItem[];
}>();

const page = usePage<SharedData>();

const openMenus = ref<Record<string, boolean>>({});
const toggleMenu = (title: string) => {
  openMenus.value[title] = !openMenus.value[title];
};
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Menu</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <template v-if="!item.guard || item.guard.includes(page.props.auth.user.role)">
          <!-- Dropdown parent -->
          <div v-if="item.children">
            <SidebarMenuButton as-child>
              <button
                class="flex items-center w-full gap-2 px-2 py-2 text-sm hover:bg-gray-100 transition"
                @click="toggleMenu(item.title)"
              >
                <component :is="item.icon" />
                <span class="flex-1 text-left">{{ item.title }}</span>
                <ChevronDown
                  class="w-4 h-4 transition-transform"
                  :class="{ 'rotate-180': openMenus[item.title] }"
                />
              </button>
            </SidebarMenuButton>

            <!-- Dropdown children -->
            <div v-if="openMenus[item.title]" class="ml-6 mt-1 space-y-1">
            <SidebarMenuItem
                v-for="child in item.children"
                :key="child.title"
            >
                <SidebarMenuButton
                as-child
                :is-active="child.href === page.url"
                :tooltip="child.title"
                >
                <Link :href="child.href">
                    <span class="ml-1 text-sm">{{ child.title }}</span>
                </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            </div>

          </div>

          <!-- Single-level item -->
          <SidebarMenuButton
            v-else
            as-child
            :is-active="item.href === page.url"
            :tooltip="item.title"
          >
            <Link :href="item.href">
              <component :is="item.icon" />
              <span>{{ item.title }}</span>
            </Link>
          </SidebarMenuButton>
        </template>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>