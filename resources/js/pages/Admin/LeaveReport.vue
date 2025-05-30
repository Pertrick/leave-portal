<template>
  <AppLayout title="Leave Reports">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Reports
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Comprehensive leave statistics and analysis
          </p>
        </div>
        <div class="flex space-x-2">
          <button
            @click="exportReport"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
          >
            <ArrowDownTrayIcon class="w-4 h-4 mr-2" />
            Export
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Year</label>
                <select
                  v-model="filters.year"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Department</label>
                <select
                  v-model="filters.department_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Leave Type</label>
                <select
                  v-model="filters.leave_type_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Types</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="filters.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                <DateRangePicker
                  v-model="filters.date_range"
                  class="mt-1 block w-full"
                />
              </div>
              <div class="lg:col-span-5 flex justify-end">
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Apply Filters
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                  <DocumentTextIcon class="w-6 h-6" />
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Total Applications</h3>
                  <p class="text-2xl font-bold text-blue-600">
                    {{ statistics.total_applications }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                  <CalendarIcon class="w-6 h-6" />
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Total Days</h3>
                  <p class="text-2xl font-bold text-green-600">
                    {{ statistics.total_days }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                  <ClockIcon class="w-6 h-6" />
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Average Duration</h3>
                  <p class="text-2xl font-bold text-purple-600">
                    {{ Number(statistics.average_duration || 0).toFixed(1) }} days
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                  <CalendarDaysIcon class="w-6 h-6" />
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Longest Leave</h3>
                  <p class="text-2xl font-bold text-yellow-600">
                    {{ statistics.longest_leave }} days
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Department Comparison -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Comparison</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Applications</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Days/User</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="dept in statistics.department_comparison" :key="dept.department">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ dept.department }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ dept.total_applications }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                          <div class="bg-green-600 h-2.5 rounded-full" :style="{ width: `${dept.approval_rate}%` }"></div>
                        </div>
                        <span class="ml-2 text-sm text-gray-500">{{ dept.approval_rate }}%</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ dept.total_days }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ dept.average_days_per_user }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Leave Balance Analysis -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Balance Analysis</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <div v-for="(dept, index) in statistics.leave_balance_analysis" :key="index" class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">{{ index }}</h4>
                <div class="space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Allocated:</span>
                    <span class="font-medium">{{ dept.total_allocated }} days</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Taken:</span>
                    <span class="font-medium">{{ dept.total_taken }} days</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Remaining:</span>
                    <span class="font-medium">{{ dept.total_remaining }} days</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Utilization:</span>
                    <span class="font-medium">{{ dept.utilization_rate }}%</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Detailed Balance by Employee</h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taken</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <template v-for="(dept, deptName) in statistics.leave_balance_analysis" :key="deptName">
                      <tr v-for="detail in dept.details" :key="`${deptName}-${detail.employee}-${detail.leave_type}`">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ deptName }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ detail.employee }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ detail.leave_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ detail.allocated }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ detail.taken }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ detail.remaining }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                              <div 
                                class="h-2.5 rounded-full" 
                                :class="{
                                  'bg-red-600': detail.utilization > 90,
                                  'bg-yellow-600': detail.utilization > 70 && detail.utilization <= 90,
                                  'bg-green-600': detail.utilization <= 70
                                }"
                                :style="{ width: `${detail.utilization}%` }"
                              ></div>
                            </div>
                            <span class="ml-2 text-sm text-gray-500">{{ detail.utilization }}%</span>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Leave Distribution -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Distribution</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Department Distribution -->
              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-4">By Department</h4>
                <div class="h-80">
                  <BarChart
                    :data="departmentChartData"
                    :options="{
                      ...chartOptions,
                      plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              const label = context.dataset.label || '';
                              const value = context.raw || 0;
                              const total = context.dataset.data.reduce((a, b) => a + b, 0);
                              const percentage = Math.round((value / total) * 100);
                              return `${label}: ${value} (${percentage}%)`;
                            }
                          }
                        }
                      },
                      scales: {
                        x: {
                          stacked: true,
                          grid: {
                            display: false
                          },
                          ticks: {
                            font: {
                              size: 11
                            }
                          }
                        },
                        y: {
                          stacked: true,
                          beginAtZero: true,
                          ticks: {
                            stepSize: 1,
                            font: {
                              size: 11
                            }
                          },
                          grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                          }
                        }
                      }
                    }"
                  />
                </div>
              </div>

              <!-- Leave Type Distribution -->
              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-4">By Leave Type</h4>
                <div class="h-80">
                  <BarChart
                    :data="leaveTypeChartData"
                    :options="{
                      ...chartOptions,
                      plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              const label = context.dataset.label || '';
                              const value = context.raw || 0;
                              const total = context.dataset.data.reduce((a, b) => a + b, 0);
                              const percentage = Math.round((value / total) * 100);
                              return `${label}: ${value} (${percentage}%)`;
                            }
                          }
                        }
                      },
                      scales: {
                        x: {
                          stacked: true,
                          grid: {
                            display: false
                          },
                          ticks: {
                            font: {
                              size: 11
                            }
                          }
                        },
                        y: {
                          stacked: true,
                          beginAtZero: true,
                          ticks: {
                            stepSize: 1,
                            font: {
                              size: 11
                            }
                          },
                          grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                          }
                        }
                      }
                    }"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trend</h3>
            <div class="h-80">
              <LineChart
                :data="monthlyTrendData"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Department and Leave Type Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Department Analysis -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Analysis</h3>
              <div class="space-y-4">
                <div v-for="dept in statistics.by_department" :key="dept.department" class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">{{ dept.department }}</span>
                    <span class="text-sm text-gray-500">{{ dept.count }} applications</span>
                  </div>
                  <div class="relative h-2 bg-gray-200 rounded-full">
                    <div
                      class="absolute h-2 bg-indigo-600 rounded-full"
                      :style="{ width: `${(dept.count / statistics.total_applications) * 100}%` }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500">
                    Total Days: {{ dept.total_days }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Leave Type Analysis -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Type Analysis</h3>
              <div class="space-y-4">
                <div v-for="type in statistics.by_type" :key="type.type" class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">{{ type.type }}</span>
                    <span class="text-sm text-gray-500">{{ type.count }} applications</span>
                  </div>
                  <div class="relative h-2 bg-gray-200 rounded-full">
                    <div
                      class="absolute h-2 bg-green-600 rounded-full"
                      :style="{ width: `${(type.count / statistics.total_applications) * 100}%` }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500">
                    Total Days: {{ type.total_days }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Leave Applications Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Applications</h3>
            <div class="relative">
              <!-- Loading overlay -->
              <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
              </div>
              
              <!-- Table content -->
              <div class="overflow-x-auto" style="max-height: 600px;">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Leave Type
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Duration
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Applied On
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="leave in leaves.data" :key="leave.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <UserAvatar
                              :user="leave.user"
                              :name="`${leave.user.firstname} ${leave.user.lastname}`"
                              class="h-10 w-10"
                            />
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ leave.user.firstname }} {{ leave.user.lastname }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ leave.user.department.name }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ leave.leave_type.name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ leave.working_days }} working days
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          :class="{
                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                            'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                            'bg-green-100 text-green-800': leave.status === 'approved',
                            'bg-red-100 text-red-800': leave.status === 'rejected'
                          }"
                        >
                          {{ leave.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(leave.created_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <Link
                          :href="route('leaves.show', leave.id)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          View Details
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pagination -->
            <div class="mt-4">
              <Pagination :links="leaves.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import DateRangePicker from '../../components/DateRangePicker.vue'
import LineChart from '@/components/charts/LineChart.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import {
  DocumentTextIcon,
  CalendarIcon,
  ClockIcon,
  CalendarDaysIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import BarChart from '@/components/charts/BarChart.vue'

const props = defineProps({
  statistics: Object,
  leaves: Object,
  departments: Array,
  leaveTypes: Array,
  filters: Object
})

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 5 }, (_, i) => currentYear - i)
})

const departmentChartData = computed(() => ({
  labels: props.statistics.department_comparison.map(dept => dept.department),
  datasets: [
    {
      label: 'Approved',
      data: props.statistics.department_comparison.map(dept => dept.approved_applications),
      backgroundColor: '#34D399', // green-400
      borderColor: '#059669', // green-600
      borderWidth: 1
    },
    {
      label: 'Pending',
      data: props.statistics.department_comparison.map(dept => dept.pending_applications),
      backgroundColor: '#FCD34D', // yellow-300
      borderColor: '#D97706', // yellow-600
      borderWidth: 1
    },
    {
      label: 'Rejected',
      data: props.statistics.department_comparison.map(dept => dept.rejected_applications),
      backgroundColor: '#F87171', // red-400
      borderColor: '#DC2626', // red-600
      borderWidth: 1
    }
  ]
}))

const leaveTypeChartData = computed(() => ({
  labels: props.statistics.by_type.map(type => type.type),
  datasets: [
    {
      label: 'Approved',
      data: props.statistics.by_type.map(type => {
        // Calculate approved count based on the type's total count and overall approval rate
        return Math.round(type.count * (props.statistics.by_status.approved / props.statistics.total_applications));
      }),
      backgroundColor: '#34D399', // green-400
      borderColor: '#059669', // green-600
      borderWidth: 1
    },
    {
      label: 'Pending',
      data: props.statistics.by_type.map(type => {
        // Calculate pending count based on the type's total count and overall pending rate
        return Math.round(type.count * (props.statistics.by_status.pending / props.statistics.total_applications));
      }),
      backgroundColor: '#FCD34D', // yellow-300
      borderColor: '#D97706', // yellow-600
      borderWidth: 1
    },
    {
      label: 'Rejected',
      data: props.statistics.by_type.map(type => {
        // Calculate rejected count based on the type's total count and overall rejection rate
        return Math.round(type.count * (props.statistics.by_status.rejected / props.statistics.total_applications));
      }),
      backgroundColor: '#F87171', // red-400
      borderColor: '#DC2626', // red-600
      borderWidth: 1
    }
  ]
}))

const monthlyTrendData = computed(() => ({
  labels: props.statistics.monthly_trend.map(item => item.month),
  datasets: [{
    label: 'Applications',
    data: props.statistics.monthly_trend.map(item => item.count),
    borderColor: '#4F46E5',
    tension: 0.4
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        padding: 20,
        usePointStyle: true,
        pointStyle: 'circle'
      }
    },
    title: {
      display: false
    }
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const loading = ref(false);

const applyFilters = async () => {
  loading.value = true;
  try {
    await router.get(
      route('admin.leave.report'),
      props.filters,
      {
        preserveState: true,
        preserveScroll: true,
        replace: true
      }
    );
  } finally {
    loading.value = false;
  }
};

const exportReport = () => {
  window.location.href = route('admin.leave.export', props.filters)
}
</script> 