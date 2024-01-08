<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

defineProps({
    products: {
        type: Object,
        required: true,
    },
});

const workingProducts = ref([]);
const regisMethods = reactive([
    {name: 'otp', checked: true, disable: false, show: true},
    {name: 'sms', checked: false, disable: false, show: true},
]);
const regisMethod = ref(null);

const onClickData = (product) => {
    product.sendingRegis = false;
    product.regisMethods = regisMethods;
    workingProducts.value.push(product);
}

const regis = async (product) => {
    let regisMethod = product.regisMethods.filter(i => i.checked)[0].name;
    product.sendingRegis = true;
    await axios.post(route('transactions.store'), {product: product, regisMethod: regisMethod}).then(({data}) => {
        product.regisMsg = 'Nhập mã OTP để hoàn tất đăng ký';
    }).catch((err) => {
        product.regisMsg = 'Mã OTP không đúng';
        product.sendingRegis = false;
    });
}
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
        <!-- <div class="p-6 text-gray-900">{{ products }}</div> -->
        <div class="py-12">
            <section class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="grid h-screen grid-cols-2 gap-4">
                    <!-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"> -->
                        <section class="max-w-4xl lg:px-4">
                            <div class="grid grid-cols-2 md:grid-cols-3">
                                <div class="max-w-sm p-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-2"
                                    v-for="product in products.data" :key="product.id" @click="onClickData(product)">
                                    <button>
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ product.title }}</h5>
                                    </button>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Gói {{ product.expiry }}</p>
                                    <!-- <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        Read more
                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                    </a> -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Giá: {{ product.price }}</span>
                                        <!-- <a href="#" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add to cart</a> -->
                                        <svg class="h-8 w-8 text-red-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <!-- </div> -->
    
                    <!-- <div class="">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            {{ workingDatas }}
                        </div>
                    </div> -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <section class="max-w-4xl sm:px-2 lg:px-3">
                            <div class="grid grid-cols-1 md:grid-cols-2">
                                <div class="max-w-sm p-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-2" v-for="product in workingProducts" :key="product.id">
                                    <a href="#">
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gói cước {{ product.title }}</h5>
                                    </a>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ product.price }}</p>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                                        v-if="!product.sendingRegis">{{ product.phoneNumber }}</p>
                                    <!-- <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        Read more
                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                    </a> -->
                                    <div class="relative mb-4 flex flex-wrap items-stretch">
                                        <input
                                            type="text"
                                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                                            placeholder="Số thuê bao"
                                            aria-label="Số thuê bao"
                                            v-model="product.phoneNumber"
                                            v-if="!product.sendingRegis"
                                            aria-describedby="button-addon2" />
                                        <input
                                            type="text"
                                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                                            placeholder="Nhập OTP"
                                            aria-label="Nhập OTP"
                                            v-if="product.sendingRegis"
                                            v-model="product.otp"
                                            aria-describedby="button-addon2" />
                                        <button
                                            class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                                            data-te-ripple-init
                                            type="button"
                                            @click="regis(product, )">
                                            <svg class="h-8 w-8 text-red-500"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="22" y1="2" x2="11" y2="13" />  <polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                        </button>
                                    </div>
                                    <div v-if="!product.sendingRegis">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">Hình thức mua</span>
                                            <!-- <a href="#" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add to cart</a> -->
                                        </div>
                                        <div class="flex justify-center">
                                            <div class="mb-[0.125rem] mr-4 inline-block min-h-[1.5rem] pl-[1.5rem]"
                                                v-for="method in product.regisMethods" :key="method.name" v-show="method.show">
                                                <input
                                                    class="relative float-left -ml-[1.5rem] mr-1 mt-0.5 h-5 w-5 appearance-none rounded-full border-2 border-solid border-neutral-300 before:pointer-events-none before:absolute before:h-4 before:w-4 before:scale-0 before:rounded-full before:bg-transparent before:opacity-0 before:shadow-[0px_0px_0px_13px_transparent] before:content-[''] after:absolute after:z-[1] after:block after:h-4 after:w-4 after:rounded-full after:content-[''] checked:border-primary checked:before:opacity-[0.16] checked:after:absolute checked:after:left-1/2 checked:after:top-1/2 checked:after:h-[0.625rem] checked:after:w-[0.625rem] checked:after:rounded-full checked:after:border-primary checked:after:bg-primary checked:after:content-[''] checked:after:[transform:translate(-50%,-50%)] hover:cursor-pointer hover:before:opacity-[0.04] hover:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:shadow-none focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] checked:focus:border-primary checked:focus:before:scale-100 checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:border-neutral-600 dark:checked:border-primary dark:checked:after:border-primary dark:checked:after:bg-primary dark:focus:before:shadow-[0px_0px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:border-primary dark:checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca]"
                                                    type="radio"
                                                    :name="'regisMethod'+product.id"
                                                    :value="method.name"
                                                    :checked="method.checked"
                                                    :disabled="method.disable" />
                                                <label class="mt-px inline-block pl-[0.15rem] hover:cursor-pointer" for="inlineRadio1">{{ method.name }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="product.sendingRegis">{{ product.regisMsg }}</div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
