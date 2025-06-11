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
  class?: string;
}>();

const page = usePage<SharedData>();

const openMenus = ref<Record<string, boolean>>({});
const toggleMenu = (title: string) => {
  openMenus.value[title] = !openMenus.value[title];
};
</script>

<template>
  <SidebarGroup :class="`px-3 py-4 ${$props.class || ''}`">
    <SidebarGroupLabel class="text-emerald-100 text-xs font-medium uppercase tracking-wider mb-4 px-2">
      <div class="flex items-center gap-2">
        <div class="w-1 h-4 bg-emerald-300 rounded-full"></div>
        <span>Aplikasi Monitoring dan Evaluasi Kegiatan Pembangunan</span>
      </div>
    </SidebarGroupLabel>
    
    <SidebarMenu class="space-y-1.5">
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <template v-if="!item.guard || item.guard.includes(page.props.auth.user.role)">
          <!-- Dropdown parent -->
          <div v-if="item.children" class="group">
            <SidebarMenuButton as-child>
              <button
                class="flex items-center w-full gap-3 px-3 py-2.5 text-sm text-emerald-100 
                       hover:bg-white/10 hover:text-white transition-all duration-300 
                       rounded-lg group-hover:shadow-lg backdrop-blur-sm
                       hover:transform hover:translate-x-1"
                @click="toggleMenu(item.title)"
              >
                <div class="p-1.5 rounded-md bg-white/10 group-hover:bg-white/20 transition-colors duration-300">
                  <component :is="item.icon" class="w-4 h-4" />
                </div>
                <span class="flex-1 text-left font-medium">{{ item.title }}</span>
                <ChevronDown
                  class="w-4 h-4 transition-all duration-300 opacity-70"
                  :class="{ 
                    'rotate-180 opacity-100': openMenus[item.title],
                    'group-hover:opacity-100': !openMenus[item.title]
                  }"
                />
              </button>
            </SidebarMenuButton>

            <!-- Dropdown children with smooth animation -->
            <Transition
              enter-active-class="transition-all duration-300 ease-out"
              enter-from-class="opacity-0 max-h-0 -translate-y-2"
              enter-to-class="opacity-100 max-h-96 translate-y-0"
              leave-active-class="transition-all duration-200 ease-in"
              leave-from-class="opacity-100 max-h-96 translate-y-0"
              leave-to-class="opacity-0 max-h-0 -translate-y-2"
            >
              <div v-if="openMenus[item.title]" class="ml-4 mt-2 space-y-1 border-l-2 border-emerald-400/30 pl-4 overflow-hidden">
                <SidebarMenuItem
                  v-for="child in item.children.filter(child => !child.guard || child.guard.includes(page.props.auth.user.role))"
                  :key="child.title"
                  class="relative"
                >
                  <SidebarMenuButton
                    as-child
                    :is-active="child.href === page.url"
                    :tooltip="child.title"
                    class="group/child"
                  >
                    <Link 
                      :href="child.href"
                      class="flex items-center gap-2 px-3 py-2 text-sm text-emerald-200 
                             hover:text-white hover:bg-white/10 transition-all duration-300 
                             rounded-md hover:transform hover:translate-x-1
                             data-[active=true]:bg-white/20 data-[active=true]:text-white
                             data-[active=true]:shadow-lg"
                    >
                      <div class="w-1.5 h-1.5 rounded-full bg-emerald-300 opacity-60 group-hover/child:opacity-100 transition-opacity duration-300"></div>
                      <span class="font-medium">{{ child.title }}</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </div>
            </Transition>
          </div>

          <!-- Single-level item -->
          <SidebarMenuButton
            v-else
            as-child
            :is-active="item.href === page.url"
            :tooltip="item.title"
            class="group"
          >
            <Link 
              :href="item.href"
              class="flex items-center gap-3 px-3 py-2.5 text-sm text-emerald-100 
                     hover:bg-white/10 hover:text-white transition-all duration-300 
                     rounded-lg group-hover:shadow-lg backdrop-blur-sm
                     hover:transform hover:translate-x-1
                     data-[active=true]:bg-white/20 data-[active=true]:text-white
                     data-[active=true]:shadow-lg data-[active=true]:transform data-[active=true]:translate-x-1"
            >
              <div class="p-1.5 rounded-md bg-white/10 group-hover:bg-white/20 transition-colors duration-300 group-data-[active=true]:bg-white/30">
                <component :is="item.icon" class="w-4 h-4" />
              </div>
              <span class="font-medium">{{ item.title }}</span>
            </Link>
          </SidebarMenuButton>
        </template>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>