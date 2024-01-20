<template>
  <div>
    <div class="flex h-screen bg-gray-50">
      <Navigation />
      <NavigationMobile />

      <div class="flex flex-col flex-1 w-full">
        <TopMenu />

        <main class="h-full overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2 class="my-6 text-2xl font-semibold text-gray-700">
              <slot name="header" />
            </h2>

            <slot />
          </div>
        </main>
      </div>
    </div>
    <ElMessage v-if="flash.msg" :message="flash.msg" :type="flash.status" />
  </div>
</template>

<script setup>
import Navigation from './Navigation.vue';
import TopMenu from "./TopMenu.vue";
import NavigationMobile from './NavigationMobile.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import 'element-plus/es/components/message/style/css'
import { ElMessage } from 'element-plus';

const page = usePage();

const flash = computed(() => page.props.flash);
</script>
