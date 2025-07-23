<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center mb-6">
        <svg class="w-5 h-5 text-gray-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
        </svg>
        <h2 class="text-xl font-bold text-gray-900">Filters</h2>
    </div>

    <div class="space-y-6">
        <!-- Price Range -->
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Price Range</h3>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <!-- Condition -->
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Condition</h3>
                <svg class="w-4 h-4 text-gray-400 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="condition" value="new" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">New</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">180</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="condition" value="refurbished" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Refurbished</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">320</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="condition" value="used" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Used</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">150</span>
                </label>
            </div>
        </div>

        <!-- Brand -->
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Brand</h3>
                <svg class="w-4 h-4 text-gray-400 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="brand" value="samsung" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Samsung</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">120</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="brand" value="apple" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Apple</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">95</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="brand" value="huawei" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Huawei</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">80</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="brand" value="oppo" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">OPPO</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">65</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="brand" value="tecno" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Tecno</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">55</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="brand" value="xiaomi" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Xiaomi</span>
                    <span class="ml-auto bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">45</span>
                </label>
            </div>
        </div>

        <!-- Internal Storage -->
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Internal Storage</h3>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <!-- Location -->
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Location</h3>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </div>
</div> 