<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">DigiShop Customers</h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white rounded-lg shadow-xs">
                    <form @submit.prevent="getInfo" class="mt-6 space-y-6">
                        <div>
                            <InputLabel for="phone_numbers" value="Phone Numbers" />

                            <textarea
                                id="phone_numbers"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="phone_numbers"
                                rows=10
                                required
                                autofocus
                                autocomplete="phone_numbers"
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="0">Lấy dữ liệu DigiShop</PrimaryButton>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-if="1" class="text-sm text-gray-600">Saved.</p>
                            </Transition>
                            <a :href="route('digishop.export')" class="rounded-lg border border-transparent bg-purple-600 px-4 py-2 text-center text-sm font-medium leading-5 text-white transition-colors duration-150 hover:bg-purple-700 focus:outline-none focus:ring active:bg-purple-600">Export</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import InputError from '@/Components/Admin/InputError.vue';
import InputLabel from '@/Components/Admin/InputLabel.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const phone_numbers = ref('');

const getInfo = () => {
    let phones = phone_numbers.value.split("\n");
    console.log(phones);
    phones.forEach(phone => {
        if (phone.length > 0) {
            axios.post(route('digishop.store', {
                phone_number: phone
            })).then((({data}) => {
                products.value = data;
            }));
        }
    });
}

const exportDigiShop = () => {
    return 1;
}
</script>
