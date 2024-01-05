<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    products: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div> -->

        <!-- <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">{{ products }}</div>
            </div>
        </div>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">{{ products }}</div>
            </div>
        </div> -->
        <div class="py-12">
            <section class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="grid h-screen grid-cols-2">
                    <div class="">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            a
                        </div>
                    </div>
    
                    <div class="">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            a
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
