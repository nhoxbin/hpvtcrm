<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import 'element-plus/es/components/message/style/css'
import { ElMessage } from 'element-plus'
import { debounce } from 'lodash';

defineProps({
    customers: {
        type: Object,
        required: true,
    },
});

const one_sell = reactive({
    search: '',
    page: 1,
    limit: 10
});
const products = ref({});

const onSearching = debounce((e) => {
    one_sell.search = e.target.value;
    getProducts();
}, 500)

onMounted(() => {
    getProducts();
});

function getProducts(url = route('products.index', one_sell)) {
    axios.get(url).then((({data}) => {
        products.value = data;
    }));
}

const workingData = reactive([]);

const regisMethods = reactive([
    {name: 'otp', checked: true, disable: false, show: true},
    {name: 'sms', checked: false, disable: true, show: false},
]);

const onClickData = async (product) => {
    workingData.push({
        product: product,
        phoneNumber: '',
        otp: '',
        regisMsg: '',
        transaction_id: '',
        regisMethod: 'otp',
        processing: {
            regis: false,
            otp: false,
        },
    });
}

const regis = async (index) => {
    workingData[index]['processing']['regis'] = true;
    await axios.post(route('transactions.store'), {
        product: workingData[index]['product'],
        regisMethod: workingData[index]['regisMethod'],
        phoneNumber: workingData[index]['phoneNumber'],
    }).then(({data}) => {
        workingData[index]['transaction_id'] = data.data.id;
        workingData[index]['regisMsg'] = 'Nhập mã OTP để hoàn tất đăng ký';
    }).catch(({response}) => {
        workingData[index]['regisMsg'] = response.data.msg;
    });
    workingData[index]['processing']['regis'] = false;
}

const confirmOtp = async (index) => {
    workingData[index]['processing']['otp'] = true;
    await axios.put(route('transactions.update', {transaction: workingData[index]['transaction_id']}),
        {otp: workingData[index]['otp']}
    ).then(({data}) => {
        ElMessage({
            message: data.msg,
            type: 'success',
        });
        workingData.splice(index, 1);
        router.reload({only: ['customers']});
    }).catch(({response}) => {
        workingData[index]['regisMsg'] = response.data.msg;
        workingData[index]['processing']['otp'] = false;
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white rounded-lg shadow-xs">
                    <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
                        <div class="overflow-x-auto w-full">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                                        <th class="px-4 py-3">Số điện thoại</th>
                                        <th class="px-4 py-3">Gói hiện tại</th>
                                        <th class="px-4 py-3">Ngày bắt đầu</th>
                                        <th class="px-4 py-3">Ngày kết thúc</th>
                                        <th class="px-4 py-3">Gói có sẵn</th>
                                        <th class="px-4 py-3">Người làm việc</th>
                                        <th class="px-4 py-3">Trạng thái</th>
                                        <th class="px-4 py-3">Sales Ghi chú</th>
                                        <th class="px-4 py-3">Admin Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y">
                                    <tr v-for="customer in customers.data" :key="customer.id" class="text-gray-700">
                                        <td class="px-4 py-3 text-sm">{{ customer.phone }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.data }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.registered_at }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.expired_at }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.available_data }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.user?.name }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.state }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.sales_note }}</td>
                                        <td class="px-4 py-3 text-sm">{{ customer.admin_note }}</td>
                                        <!-- <td>
                                        <div class="btn-group">
                                            <button class="btn btn-info" @click="isEditUser = true">Sửa</button>
                                            <button class="btn btn-danger" @click="deleteUser(customer.id)">Xóa</button>
                                        </div>
                                        </td> -->
                                    </tr>
                                    <tr v-if="!customers.data.length">
                                        <td class="px-4 py-3 text-sm text-center" colspan="9">Không có dữ liệu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
                            <pagination :links="customers.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="">
            <section class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <!-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"> -->
                        <section class="max-w-4xl lg:px-4">
                            <div class="relative mb-4 flex flex-wrap items-stretch">
                                <input
                                    type="text"
                                    class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-white bg-clip-padding px-3 py-[0.25rem] text-black font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                                    placeholder="Tìm kiếm"
                                    aria-label="Tìm kiếm"
                                    :value="one_sell.search"
                                    @input="onSearching($event)" />
                                <button
                                    class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                                    data-te-ripple-init
                                    type="button"
                                    @click="getProducts()">
                                    <svg class="h-8 w-8 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="10" cy="10" r="7" />  <line x1="21" y1="21" x2="15" y2="15" /></svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3">
                                <div class="max-w-sm p-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-2"
                                    v-for="product in products.data" :key="product.product_id" @click="onClickData(product)">
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
                            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
                                <div v-if="products.links && products.links.length > 3">
                                    <div class="-mb-1 flex flex-wrap">
                                        <template v-for="(link, key) in products.links">
                                            <div v-if="link.url === null" :key="key" class="mr-1 mb-1 rounded border px-2 py-1 text-sm leading-4 text-gray-400" v-html="link.label" />
                                            <button v-else :key="key + 1" class="mr-1 mb-1 rounded border px-2 py-1 text-sm leading-4 hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'text-white bg-purple-600 hover:bg-purple-600': link.active }" @click.prevent="getProducts(link.url)" v-html="link.label"></button>
                                        </template>
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
                                <!-- <div class="relative items-center block max-w-sm p-6 bg-white border border-gray-100 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-800 dark:hover:bg-gray-700"></div> -->
                                <div :class="{'pointer-events-none': workingData[index]['processing']['regis'] || workingData[index]['processing']['otp']}" class="relative items-center block max-w-sm p-2 bg-white border border-gray-100 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-2"
                                    v-for="(product, index) in workingData" :key="product.product_id">
                                    <div class="flex items-center justify-between rounded-t dark:border-gray-600">
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gói cước {{ workingData[index].product.title }}</h5>
                                        <button type="button" @click="workingData.splice(index, 1)" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close data</span>
                                        </button>
                                    </div>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ workingData[index].product.price }}</p>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                                        v-if="workingData[index]['transaction_id'].length">Số điện thoại: {{ workingData[index]['phoneNumber'] }}</p>
                                    <div class="relative mb-4 flex flex-wrap items-stretch">
                                        <input
                                            type="text"
                                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                                            placeholder="Số thuê bao"
                                            aria-label="Số thuê bao"
                                            v-model="workingData[index]['phoneNumber']"
                                            v-if="!workingData[index]['transaction_id'].length" />
                                        <input
                                            type="text"
                                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                                            placeholder="Nhập OTP"
                                            aria-label="Nhập OTP"
                                            v-model="workingData[index]['otp']"
                                            v-else />
                                        <button
                                            class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                                            data-te-ripple-init
                                            type="button"
                                            v-if="!workingData[index]['transaction_id'].length"
                                            @click="regis(index)">
                                            <svg class="h-8 w-8 text-red-500"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="22" y1="2" x2="11" y2="13" />  <polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                        </button>
                                        <button
                                            class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                                            data-te-ripple-init
                                            type="button"
                                            v-else
                                            @click="confirmOtp(index)">
                                            <svg class="h-8 w-8 text-red-500"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <line x1="22" y1="2" x2="11" y2="13" />  <polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                        </button>
                                    </div>
                                    <div v-if="!workingData[index]['transaction_id'].length">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">Hình thức mua</span>
                                        </div>
                                        <div class="flex justify-center">
                                            <div class="mb-[0.125rem] mr-4 inline-block min-h-[1.5rem] pl-[1.5rem]"
                                                v-for="method in regisMethods" :key="method.name" v-show="method.show">
                                                <input
                                                    class="relative float-left -ml-[1.5rem] mr-1 mt-0.5 h-5 w-5 appearance-none rounded-full border-2 border-solid border-neutral-300 before:pointer-events-none before:absolute before:h-4 before:w-4 before:scale-0 before:rounded-full before:bg-transparent before:opacity-0 before:shadow-[0px_0px_0px_13px_transparent] before:content-[''] after:absolute after:z-[1] after:block after:h-4 after:w-4 after:rounded-full after:content-[''] checked:border-primary checked:before:opacity-[0.16] checked:after:absolute checked:after:left-1/2 checked:after:top-1/2 checked:after:h-[0.625rem] checked:after:w-[0.625rem] checked:after:rounded-full checked:after:border-primary checked:after:bg-primary checked:after:content-[''] checked:after:[transform:translate(-50%,-50%)] hover:cursor-pointer hover:before:opacity-[0.04] hover:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:shadow-none focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[0px_0px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] checked:focus:border-primary checked:focus:before:scale-100 checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:border-neutral-600 dark:checked:border-primary dark:checked:after:border-primary dark:checked:after:bg-primary dark:focus:before:shadow-[0px_0px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:border-primary dark:checked:focus:before:shadow-[0px_0px_0px_13px_#3b71ca]"
                                                    type="radio"
                                                    v-model="workingData[index]['regisMethod']"
                                                    :name="'regisMethod'+method.name+workingData[index].product.product_id"
                                                    :value="method.name"
                                                    :checked="method.checked"
                                                    :disabled="method.disable" />
                                                <label class="mt-px inline-block pl-[0.15rem] hover:cursor-pointer" :for="'regisMethod'+method.name+product.product_id">{{ method.name }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="workingData[index]['regisMsg'].length">{{ workingData[index]['regisMsg'] }}</div>
                                    <div v-if="workingData[index]['processing']['regis'] || workingData[index]['processing']['otp']" role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
