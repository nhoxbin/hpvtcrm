<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white rounded-lg shadow-xs">
          <!-- <div class="relative mb-4 flex flex-wrap items-stretch">
            <input
              type="text"
              class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-white bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-black dark:placeholder:text-neutral-200 dark:focus:border-primary"
              placeholder="Tìm kiếm"
              aria-label="Tìm kiếm"
              v-model="search.phone"
              @input="onSearching()"
            />
            <button
              class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
              data-te-ripple-init
              type="button"
              @click="onSearching()"
            >
              <svg
                class="h-8 w-8 text-red-500"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path stroke="none" d="M0 0h24v24H0z" />
                <circle cx="10" cy="10" r="7" />
                <line x1="21" y1="21" x2="15" y2="15" />
              </svg>
            </button>
          </div> -->
          <form @submit.prevent="onSearching()">
            <div class="grid gap-6 mb-6 md:grid-cols-4">
              <div>
                <label
                  for="phone"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Số điện thoại</label
                >
                <input
                  type="text"
                  id="last_name"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="search.phone"
                  placeholder="VD: 849xxxxxxxx"
                />
              </div>
              <div>
                <label
                  for="expires_in"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Trước ngày hết hạn</label
                >
                <input
                  type="text"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="Trước ngày hết hạn"
                  aria-label="Trước ngày hết hạn"
                  v-model="search.expires_in"
                />
              </div>
              <div>
                <label
                  for="sales_state"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Trạng thái</label
                >
                <select
                  id="sales_state"
                  v-model="search.sales_state"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                  <option value="0">Chọn trạng thái</option>
                  <option
                    v-for="(state, key) in sales_states"
                    :key="key"
                    :value="key"
                  >
                    {{ state }}
                  </option>
                </select>
              </div>
              <div>
                <label
                  for="sales_note"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Sales ghi chú</label
                >
                <input
                  type="text"
                  id="last_name"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="search.sales_note"
                />
              </div>
            </div>
            <div class="mb-6">
              <label
                for="goi_data"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                >Gói</label
              >
              <vue3-tags-input
                :tags="search.goi_data"
                @on-tags-changed="(newTags) => (search.goi_data = newTags)"
              />
            </div>
            <!-- <button
              class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
              data-te-ripple-init
              type="submit"
            >
              <svg
                class="h-8 w-8 text-red-500"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path stroke="none" d="M0 0h24v24H0z" />
                <circle cx="10" cy="10" r="7" />
                <line x1="21" y1="21" x2="15" y2="15" />
              </svg>
            </button> -->
            <button
              type="submit"
              class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Tìm kiếm
            </button>
          </form>
          <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
            <div class="overflow-x-auto w-full">
              <table class="w-full whitespace-no-wrap">
                <thead>
                  <tr
                    class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b"
                  >
                    <th class="px-4 py-3">Số điện thoại</th>
                    <th class="px-4 py-3">Tài khoản chính</th>
                    <th class="px-4 py-3">Loại thuê bao</th>
                    <th class="px-4 py-3">Gói cước TS</th>
                    <th class="px-4 py-3">Gói cước</th>
                    <th class="px-4 py-3">Gói data</th>
                    <th class="px-4 py-3">Ngày hết hạn gói data</th>
                    <th class="px-4 py-3">Gói</th>
                    <th class="px-4 py-3">Ngày hết hạn</th>
                    <th class="px-4 py-3">Tích hợp</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Sales Ghi chú</th>
                    <th class="px-4 py-3">Admin Ghi chú</th>
                    <th class="px-4 py-3">Thao tác</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y">
                  <tr
                    v-for="customer in customers.data"
                    :key="customer.id"
                    class="text-gray-700"
                  >
                    <td class="px-4 py-3 text-sm phone">
                      {{ customer.phone }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <button
                        @click="reload_balance(customer)"
                        class="font-medium text-blue-600 dark:text-blue-500"
                      >
                        {{ vnd_format(customer.core_balance) }}
                      </button>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_trasau(customer.tra_sau) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customerInfo.goi_cuoc_ts) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customerInfo.goi_cuoc) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customer.goi_data) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_expires_date(customer.goi_data) }}
                    </td>
                    <td class="px-4 py-3 text-sm">{{ customer.goi }}</td>
                    <td class="px-4 py-3 text-sm">{{ customer.expired_at }}</td>
                    <td class="px-4 py-3 text-sm">
                      {{ customer.integration }}
                    </td>
                    <td class="px-4 py-3 text-sm">{{ customer.state }}</td>
                    <td class="px-4 py-3 text-sm">{{ customer.sales_note }}</td>
                    <td class="px-4 py-3 text-sm">{{ customer.admin_note }}</td>
                    <td class="px-4 py-3 text-sm">
                      <button
                        @click="editCustomer(customer)"
                        type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                      >
                        <svg
                          class="h-3 w-3 text-red-500"
                          viewBox="0 0 24 24"
                          stroke-width="2"
                          stroke="currentColor"
                          fill="none"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        >
                          <path stroke="none" d="M0 0h24v24H0z" />
                          <path
                            d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"
                          />
                          <path
                            d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"
                          />
                          <line x1="16" y1="5" x2="19" y2="8" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="!customers.total">
                    <td class="px-4 py-3 text-sm text-center" colspan="14">
                      Không có dữ liệu
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div
              class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
            >
              <pagination :links="customers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="py-1">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white rounded-lg shadow-xs">
          <div class="relative mb-4 flex flex-wrap items-stretch">
            <input
              type="text"
              class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-white bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-black dark:placeholder:text-neutral-200 dark:focus:border-primary"
              placeholder="Tìm kiếm"
              aria-label="Tìm kiếm"
              v-model="directSearch.phone"
              @input="getDirectPhoneData()"
            />
            <button
              class="z-[2] inline-block rounded-r bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:z-[3] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
              data-te-ripple-init
              type="button"
              @click="getDirectPhoneData()"
            >
              <svg
                class="h-8 w-8 text-red-500"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path stroke="none" d="M0 0h24v24H0z" />
                <circle cx="10" cy="10" r="7" />
                <line x1="21" y1="21" x2="15" y2="15" />
              </svg>
            </button>
          </div>
          <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
            <div class="overflow-x-auto w-full">
              <table class="w-full whitespace-no-wrap">
                <thead>
                  <tr
                    class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b"
                  >
                    <th class="px-4 py-3">Số điện thoại</th>
                    <th class="px-4 py-3">Tài khoản chính</th>
                    <th class="px-4 py-3">Tích hợp</th>
                    <th class="px-4 py-3">Loại thuê bao</th>
                    <th class="px-4 py-3">Gói cước TS</th>
                    <th class="px-4 py-3">Gói cước</th>
                    <th class="px-4 py-3">Gói data</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y">
                  <tr class="text-gray-700" v-if="customerInfo.is_request">
                    <td class="px-4 py-3 text-sm phone">
                      {{ customerInfo.phone }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <button
                        @click="reload_balance(customerInfo)"
                        class="font-medium text-blue-600 dark:text-blue-500"
                      >
                        {{ vnd_format(customerInfo.core_balance) }}
                      </button>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <span
                        v-if="typeof customerInfo.integration == 'object'"
                        >{{ get_integration(customerInfo.integration) }}</span
                      >
                      <button
                        v-else
                        @click="get_digishop_integrate(customerInfo)"
                        class="font-medium text-blue-600 dark:text-blue-500"
                      >
                        Click
                      </button>
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_trasau(customerInfo.tra_sau) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customerInfo.goi_cuoc_ts) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customerInfo.goi_cuoc) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {{ get_goi_data_to_string(customerInfo.goi_data) }}
                    </td>
                  </tr>
                  <tr class="text-gray-700" v-else>
                    <td class="px-4 py-3 text-sm text-center" colspan="9">
                      Không có dữ liệu
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div
              class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
            >
              <pagination :links="customers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <EditCustomerForm
      v-if="actions.isEditCustomer"
      :customer="currentCustomer"
      :sales_states="sales_states"
      :isEditCustomer="actions.isEditCustomer"
      @closeForm="onCloseForm"
    />
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Admin/Pagination.vue";
import EditCustomerForm from "./Partials/EditCustomerForm.vue";
import Vue3TagsInput from "vue3-tags-input";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import "element-plus/es/components/message/style/css";
import { ElMessage } from "element-plus";
import { debounce, startCase, toLower } from "lodash";
import _ from "lodash";

const props = defineProps({
  sales_states: {
    type: Object,
    required: true,
  },
  customers: {
    type: Object,
    required: true,
  },
});

const page = usePage();

const search = reactive({
  phone: page.props.query.search?.phone || "",
  sales_state: page.props.query.search?.sales_state || 0,
  sales_note: page.props.query.search?.sales_note || "",
  goi_data: page.props.query.search?.goi_data || [],
});

const directSearch = reactive({
  phone: page.props.query.search?.direct?.phone || "",
});

const customerInfo = reactive({
  core_balance: 0,
  integration: "",
  goi_cuoc: [],
  goi_cuoc_ts: [],
  goi_data: [],
  phone: "",
  tra_sau: null,
  is_request: 0,
});

const onSearching = debounce(() => {
  router.reload({
    preserveState: true,
    preserveScroll: true,
    only: ["customers"],
    data: {
      search,
    },
  });
}, 500);

const vnd_format = (num) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(num);
};

const get_integration = (integrate) => {
  return _.join(integrate, ", ");
};

const get_goi_data_to_string = (goi_data) => {
  return _.join(
    _.map(goi_data, function (data) {
      return (
        "Tên gói: " +
        data["PACKAGE_NAME"] +
        ", dịch vụ: " +
        data["SERVICES"] +
        ", Ngày hết hạn: " +
        data["TIME_END"]
      );
    }),
    "\n"
  );
};

const get_trasau = (tra_sau) => {
  let label = null;
  if (tra_sau == 0) label = "Trả trước";
  else if (tra_sau == 1) label = "Trả sau";
  return label;
};

const get_expires_date = (goi_data) => {
  return _.join(_.map(goi_data, "TIME_END"), "\n");
};

const reload_balance = (customer) => {
  axios
    .get(route("onebss.customers.reload_balance", customer.id))
    .then(({ data }) => {
      let resp = data.data.data;
      if (resp.length) {
        let index = _.findIndex(resp, { ID: "1" });
        customer.core_balance = resp[index]["REMAIN"];
      }
    })
    .catch(({ response }) => {
      if (response) {
        ElMessage({
          type: "error",
          message: response.data.msg,
        });
      }
    });
};

const get_digishop_integrate = (customer) => {
  axios
    .get(route("digishop.customers.get_object", customer.phone))
    .then(({ data }) => {
      customerInfo.integration = data.data;
    })
    .catch(({ response }) => {
      if (response) {
        ElMessage({
          type: "error",
          message: response.data.msg,
        });
      }
    });
};

const getDirectPhoneData = () => {
  if (directSearch.phone.length == 11) {
    axios
      .get(route("onebss.customers.get_direct_phone_data", directSearch.phone))
      .then(({ data }) => {
        let info = data.data.info;
        if (info["error_code"] != "BSS-00000500") {
          customerInfo.core_balance = info.core_balance;
          customerInfo.goi_cuoc = info.goi_cuoc;
          customerInfo.goi_cuoc_ts = info.goi_cuoc_ts;
          customerInfo.goi_data = info.goi_data;
          customerInfo.phone = info.phone;
          customerInfo.tra_sau = info.tra_sau;
          customerInfo.is_request = info.is_request;
          customerInfo.is_request = info.is_request;
          customerInfo.id = info.id;
          customerInfo.integration = "";
        } else {
          ElMessage({
            type: "error",
            message: info["message"],
          });
        }
      })
      .catch(({ response }) => {
        console.log(response);
        if (response) {
          ElMessage({
            type: "error",
            message: response.data.msg,
          });
        }
      });
  }
};

/* const searchFuncs = {
    getProducts: (searchText = null, url = null) => {
        axios.get(url || route('products.index', {
            search: {
                products: searchText
            }
        })).then((({data}) => {
            products.value = data;
        }));
    },
    getCustomers: (searchText = null) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['customers'],
            data: {
                search: {
                    customers: searchText
                }
            }
        });
    }
};

searchFuncs['getProducts']();

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
} */

const currentCustomer = ref({});
const actions = reactive({
  isEditCustomer: false,
});
const editCustomer = (customer) => {
  currentCustomer.value = customer;
  actions.isEditCustomer = true;
};
const onCloseForm = (prop) => {
  actions[prop] = false;
};
</script>
