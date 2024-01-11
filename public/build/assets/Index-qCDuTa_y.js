import{_ as f}from"./AuthenticatedLayout-d3yDobfS.js";import{_ as y}from"./Pagination-CCg_6cxq.js";import{r as a,o as c,c as p,a as o,u as g,w as i,F as u,Z as b,b as r,d as s,e as w,t as e}from"./app-CTb-M7it.js";import{_ as v}from"./SecondaryButton-w7D2jSMh.js";import C from"./UploadCustomerForm-Py1eSdAf.js";import{E as k}from"./index-9CTvVAPQ.js";import{D as N}from"./DangerButton-MaNJq58t.js";import{_ as $}from"./PrimaryButton-DSPDhFND.js";import"./DropdownLink-6XMNmVrP.js";import"./_plugin-vue_export-helper-x3n3nnut.js";import"./Modal-HvQIsPGa.js";const B={class:"p-4 bg-white rounded-lg shadow-xs mb-2"},D={class:"p-4 bg-white rounded-lg shadow-xs"},E={class:"overflow-hidden mb-8 w-full rounded-lg border shadow-xs"},F={class:"overflow-x-auto w-full"},U={class:"w-full whitespace-no-wrap"},G=s("thead",null,[s("tr",{class:"text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b"},[s("th",{class:"px-4 py-3"},"Số điện thoại"),s("th",{class:"px-4 py-3"},"Tên gói"),s("th",{class:"px-4 py-3"},"Ngày bắt đầu"),s("th",{class:"px-4 py-3"},"Ngày kết thúc"),s("th",{class:"px-4 py-3"},"Gói có sẵn"),s("th",{class:"px-4 py-3"},"Người làm việc"),s("th",{class:"px-4 py-3"},"Trạng thái"),s("th",{class:"px-4 py-3"},"Sales Ghi chú"),s("th",{class:"px-4 py-3"},"Admin Ghi chú")])],-1),S={class:"bg-white divide-y"},T={class:"px-4 py-3 text-sm"},V={class:"px-4 py-3 text-sm"},j={class:"px-4 py-3 text-sm"},A={class:"px-4 py-3 text-sm"},O={class:"px-4 py-3 text-sm"},I={class:"px-4 py-3 text-sm"},L={class:"px-4 py-3 text-sm"},M={class:"px-4 py-3 text-sm"},Z={class:"px-4 py-3 text-sm"},q={class:"px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"},ts={__name:"Index",props:{auth:Object,customers:Object,users:Array},setup(n){const d=a(!1);a(!1),a(!1),a(null),a(!1),a(null);const x=()=>{d.value=!1},h=()=>{axios.post(route("admin.customers.export")).then(function({data:l}){k({message:l.msg,type:"success"})}).catch(function(l){console.log(l)})};return(l,_)=>(c(),p(u,null,[o(g(b),{title:"Customers"}),o(f,null,{header:i(()=>[r(" Customers ")]),default:i(()=>[s("div",B,[o(v,{onClick:_[0]||(_[0]=t=>d.value=!0)},{default:i(()=>[r("Upload")]),_:1}),o($,{onClick:h},{default:i(()=>[r("Export")]),_:1}),o(N,{onClick:l.deleteCustomer},{default:i(()=>[r("Delete")]),_:1},8,["onClick"])]),s("div",D,[s("div",E,[s("div",F,[s("table",U,[G,s("tbody",S,[(c(!0),p(u,null,w(n.customers.data,t=>{var m;return c(),p("tr",{key:t.id,class:"text-gray-700"},[s("td",T,e(t.phone),1),s("td",V,e(t.data),1),s("td",j,e(t.registered_at),1),s("td",A,e(t.expired_at),1),s("td",O,e(t.available_data),1),s("td",I,e((m=t.user)==null?void 0:m.name),1),s("td",L,e(t.sales_state),1),s("td",M,e(t.sales_note),1),s("td",Z,e(t.admin_note),1)])}),128))])])]),s("div",q,[o(y,{links:n.customers.links},null,8,["links"])])])]),o(C,{users:n.users,"is-upload-customer":d.value,onCloseUploadCustomerForm:x},null,8,["users","is-upload-customer"])]),_:1})],64))}};export{ts as default};
