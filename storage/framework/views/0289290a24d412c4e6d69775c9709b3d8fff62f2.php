<?php $__env->startSection('title', 'Add New OLT Location'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Add New OLT Location</h2>
                <a href="<?php echo e(route('admin.olt.index')); ?>" 
                   class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.olt.store')); ?>" class="p-6 space-y-6" x-data="oltForm()">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- State -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                    <select name="state_id" 
                            x-model="formData.state_id"
                            @change="updateDistricts()"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('state_id') border-red-500 @enderror">
                        <option value="">Select State</option>
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <option value="<?php echo e($state->state_id); ?>" <?php echo e(old('state_id') == $state->state_id ? 'selected' : ''); ?>>
                                <?php echo e($state->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </select>
                    @error('state_id')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>

                <!-- District -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                    <select name="district_id" 
                            x-model="formData.district_id"
                            @change="updateBlocks()"
                            :disabled="!formData.state_id"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('district_id') border-red-500 @enderror"
                            :class="!formData.state_id ? 'bg-gray-100 cursor-not-allowed' : ''">
                        <option value="">Select District</option>
                        <template x-for="district in districts" :key="district">
                            <option :value="district.id" x-text="district.name" :selected="'<?php echo e(old('district_id')); ?>' == district.id"></option>
                        </template>
                    </select>
                    @error('district_id')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>

                <!-- Block -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Block *</label>
                    <select name="block_id" 
                            x-model="formData.block_id"
                            :disabled="!formData.district_id"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('block_id') border-red-500 @enderror"
                            :class="!formData.district_id ? 'bg-gray-100 cursor-not-allowed' : ''">
                        <option value="">Select Block</option>
                        <template x-for="block in blocks" :key="block">
                            <option :value="block.id" x-text="block.name" :selected="'<?php echo e(old('block_id')); ?>' == block.id"></option>
                        </template>
                    </select>
                    @error('block_id')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- OLT Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">OLT Location *</label>
                    <input type="text" 
                           name="olt_location" 
                           value="<?php echo e(old('olt_location')); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('olt_location') border-red-500 @enderror"
                           placeholder="Enter OLT location">
                    @error('olt_location')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>

                <!-- OLT Location Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">OLT Location Code *</label>
                    <input type="text" 
                           name="olt_location_code" 
                           value="<?php echo e(old('olt_location_code')); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('olt_location_code') border-red-500 @enderror"
                           placeholder="Enter location code">
                    @error('olt_location_code')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- LGD Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">LGD Code *</label>
                    <input type="text" 
                           name="lgd_code" 
                           value="<?php echo e(old('lgd_code')); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('lgd_code') border-red-500 @enderror"
                           placeholder="Enter LGD code">
                    @error('lgd_code')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>

                <!-- OLT IP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">OLT IP Address *</label>
                    <input type="text" 
                           name="olt_ip" 
                           value="<?php echo e(old('olt_ip')); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('olt_ip') border-red-500 @enderror"
                           placeholder="192.168.1.10">
                    @error('olt_ip')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>

                <!-- Number of GPs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Number of GPs *</label>
                    <input type="number" 
                           name="no_of_gps" 
                           value="<?php echo e(old('no_of_gps')); ?>"
                           min="0"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('no_of_gps') border-red-500 @enderror"
                           placeholder="25">
                    @error('no_of_gps')
                        <div class="flex items-center mt-1 text-sm text-red-600">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            <?php echo e($message); ?>

                        </div>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="<?php echo e(route('admin.olt.index')); ?>" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function oltForm() {
    return {
        formData: {
            state_id: '<?php echo e(old('state_id')); ?>',
            district_id: '<?php echo e(old('district_id')); ?>',
            block_id: '<?php echo e(old('block_id')); ?>'
        },
        districts: [],
        blocks: [],
        
        init() {
            if (this.formData.state_id) {
                this.updateDistricts();
                if (this.formData.district_id) {
                    this.updateBlocks();
                }
            }
        },
        
        updateDistricts() {
            if (!this.formData.state_id) {
                this.districts = [];
                this.blocks = [];
                this.formData.district_id = '';
                this.formData.block_id = '';
                return;
            }
            
            fetch(`/api/districts?state_id=${this.formData.state_id}`)
                .then(response => response.json())
                .then(data => {
                    this.districts = data;
                    this.formData.district_id = '';
                    this.formData.block_id = '';
                    this.blocks = [];
                });
        },
        
        updateBlocks() {
            if (!this.formData.district_id) {
                this.blocks = [];
                this.formData.block_id = '';
                return;
            }
            
            fetch(`/api/blocks?district_id=${this.formData.district_id}`)
                .then(response => response.json())
                .then(data => {
                    this.blocks = data;
                    this.formData.block_id = '';
                });
        }
    }
}
</script>
            this.blocks = [];
        },
        
        updateBlocks() {
            this.blocks = (this.formData.state && this.formData.district) ? 
                (this.stateData[this.formData.state][this.formData.district] || []) : [];
            this.formData.block = '';
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>