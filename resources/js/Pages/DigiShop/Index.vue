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
                            >856666339
856666323
856666318
856666316
856666305
856666304</textarea>
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
                                <p v-if="1" @click="exportDigiShop" class="text-sm text-gray-600">Xuất Excel.</p>
                            </Transition>
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

const phone_numbers = ref([]);

const getInfo = () => {
    let phones = phone_numbers.value.split("\n");
    console.log(phones);
    phones.forEach(phone => {
        axios.post(route('digishop.store', {
            phone_number: phone
        })).then((({data}) => {
            products.value = data;
        }));
    });
}

const exportDigiShop = () => {
    return 1;
}
</script>
