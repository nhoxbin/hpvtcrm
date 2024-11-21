<template>
  <Head title="Khách hàng" />

  <AuthenticatedLayout>
    <template #header> Khách hàng </template>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="mb-4 w-full">
        <SecondaryButton @click="actions.isUploadCustomer = true"
          >Upload</SecondaryButton
        >
        <PrimaryButton @click="actions.isDistributeCustomer = true"
          >Phân phối</PrimaryButton
        >
        <PrimaryButton
          @click="exportExcel(route('admin.onebss.customers.export'))"
          >Export</PrimaryButton
        >
        <DangerButton
          class="float-right"
          @click="actions.isDeleteCustomer = true"
          >Delete</DangerButton
        >
      </div>
      <div class="mb-4 w-full">
        <label for="auth_status">{{ auth_status }}</label>
      </div>
      <div class="mb-4 w-full">
        <label for="process"
          >Đã chạy:
          {{
            process_customers["processing"] + "/" + process_customers["total"]
          }}</label
        >
      </div>

      <div class="relative mb-4 flex flex-wrap items-stretch">
        <div class="m-2">
          <div class="flex items-center mb-4">
            <input
              id="default-checkbox"
              type="checkbox"
              :value="0"
              v-model="formSearch.tra_sau"
              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <label
              for="default-checkbox"
              class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
              >Trả trước</label
            >
          </div>
          <div class="flex items-center">
            <input
              id="checked-checkbox"
              type="checkbox"
              :value="1"
              v-model="formSearch.tra_sau"
              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <label
              for="checked-checkbox"
              class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
              >Trả sau</label
            >
          </div>
        </div>
        <vue3-tags-input
          :tags="formSearch.goi_data"
          @on-tags-changed="(newTags) => (formSearch.goi_data = newTags)"
        />
        <input
          type="text"
          class="relative m-1 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-white bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-black dark:placeholder:text-neutral-200 dark:focus:border-primary"
          placeholder="Trước ngày hết hạn"
          aria-label="Trước ngày hết hạn"
          v-model="formSearch.expires_in"
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
                <th class="px-4 py-3">Loại thuê bao</th>
                <th class="px-4 py-3">Gói cước TS</th>
                <th class="px-4 py-3">Gói cước</th>
                <th class="px-4 py-3">Gói data</th>
                <th class="px-4 py-3">Ngày hết hạn</th>
                <th class="px-4 py-3">Người làm việc</th>
                <th class="px-4 py-3">Người check</th>
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
                <td class="px-4 py-3 text-sm">{{ customer.phone }}</td>
                <td class="px-4 py-3 text-sm">
                  {{ vnd_format(customer.core_balance) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_trasau(customer.tra_sau) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_cuoc_ts) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_cuoc) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_data) }}
                </td>
                <td class="px-4 py-3 text-sm">{{ customer.user?.name }}</td>
                <td class="px-4 py-3 text-sm">
                  {{ customer.checked_by?.name }}
                </td>
                <td class="px-4 py-3 text-sm">{{ customer.state }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.sales_note }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.admin_note }}</td>
                <!-- <td>
                  <div class="btn-group">
                    <button class="btn btn-info" @click="isEditUser = true">Sửa</button>
                    <button class="btn btn-danger" @click="deleteUser(customer.id)">Xóa</button>
                  </div>
                </td> -->
                <td class="px-4 py-3 text-sm">
                  <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button
                      @click="edit(customer)"
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
                    <button
                      @click="del(customer.id)"
                      type="button"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                    >
                      <svg
                        class="h-3 w-3 text-red-500"
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
                        <line x1="4" y1="7" x2="20" y2="7" />
                        <line x1="10" y1="11" x2="10" y2="17" />
                        <line x1="14" y1="11" x2="14" y2="17" />
                        <path
                          d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"
                        />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!customers.data.length">
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

    <EditCustomerForm
      v-if="actions.isEditCustomer"
      :isEditCustomer="actions.isEditCustomer"
      :customer="currentCustomer"
      @closeForm="onCloseForm"
    />
    <UploadCustomerForm
      :isUploadCustomer="actions.isUploadCustomer"
      @closeForm="onCloseForm"
    />
    <DeleteCustomerForm
      :isDeleteCustomer="actions.isDeleteCustomer"
      @closeForm="onCloseForm"
    />
    <DistributeCustomerForm
      :users="users"
      :isDistributeCustomer="actions.isDistributeCustomer"
      @closeForm="onCloseForm"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import Pagination from "@/Components/Admin/Pagination.vue";
import { reactive, ref } from "vue";
import SecondaryButton from "@/Components/Admin/SecondaryButton.vue";
import EditCustomerForm from "./Partials/EditCustomerForm.vue";
import UploadCustomerForm from "./Partials/UploadCustomerForm.vue";
import DeleteCustomerForm from "./Partials/DeleteCustomerForm.vue";
import "element-plus/es/components/message/style/css";
import "element-plus/es/components/message-box/style/css";
import { ElMessage, ElMessageBox } from "element-plus";
import DangerButton from "@/Components/DangerButton.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import DistributeCustomerForm from "./Partials/DistributeCustomerForm.vue";
import PrimaryButton from "@/Components/Admin/PrimaryButton.vue";
import _, { debounce } from "lodash";
import Vue3TagsInput from "vue3-tags-input";
import { defineComponent } from "vue";

defineComponent({
  components: {
    Vue3TagsInput,
  },
});

const props = defineProps({
  auth: Object,
  customers: Object,
  users: Array,
  msg: String,
  error: String,
  process_customers: Object,
  auth_status: String,
});

if (props.msg) {
  ElMessage({
    type: "info",
    message: props.msg,
  });
}
if (props.error) {
  ElMessage({
    type: "info",
    message: props.error,
  });
}
const page = usePage();

const tra_sau = ref(page.props.query.tra_sau || []);
const formSearch = useForm({
  tra_sau: tra_sau,
  goi_data: page.props.query.goi_data || [],
  expires_in: page.props.query.expires_in || "",
});

const currentCustomer = ref(null);

const actions = reactive({
  isUploadCustomer: false,
  isEditCustomer: false,
  isDeleteCustomer: false,
  isDistributeCustomer: false,
});

const vnd_format = (num) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(num);
};
/* const get_goidata = (goi_data) => {
  return _.join(_.map(goi_data, function(data) {
    return 'Tên gói: ' + data['PACKAGE_NAME'] + ', dịch vụ: ' + data['SERVICES'];
  }), "\n");
}; */
const get_trasau = (tra_sau) => {
  let label = null;
  if (tra_sau == 0) label = "Trả trước";
  else if (tra_sau == 1) label = "Trả sau";
  return label;
};
/* const get_expires_date = (goi_data) => {
  return _.join(_.map(goi_data, 'TIME_END'), "\n");
}; */

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

const exportExcel = (url) => {
  document.location = url;
};

const onCloseForm = (prop) => {
  actions[prop] = false;
};

const edit = (customer) => {
  currentCustomer.value = customer;
  actions.isEditCustomer = true;
};

const onSearching = debounce(() => {
  formSearch.get(route("admin.onebss.customers.index"), {
    preserveScroll: true,
    onSuccess: () => {},
  });
}, 500);

const del = (customer) => {
  ElMessageBox.confirm("Bạn có chắc muốn xóa khách hàng này chứ?", "Warning", {
    confirmButtonText: "OK",
    cancelButtonText: "Cancel",
    type: "warning",
  })
    .then(() => {
      axios
        .delete(route("admin.onebss.customers.destroy", customer))
        .then(({ data }) => {
          ElMessage({
            type: "success",
            message: "Xóa thành công",
          });
          router.reload({ only: ["customers"] });
        })
        .catch(function (err) {
          // console.log(err.responseText);
        });
    })
    .catch(() => {
      /* ElMessage({
      type: 'info',
      message: 'Delete cancelled',
    }) */
    });
};
</script>
