{{-- File Upload Component --}}
{{-- Drag & drop file upload dengan preview, theme-aware --}}

@props([
    'name' => 'file', // Input name
    'label' => null, // Field label
    'accept' => null, // Accept file types (e.g., "image/*,.pdf")
    'multiple' => false, // Allow multiple files
    'maxFiles' => null, // Max number of files (null = unlimited)
    'maxSize' => 5120, // Max file size in KB (default: 5MB)
    'preview' => true, // Show image previews
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => ''
])

@php
    $uniqueId = 'file_' . uniqid();
@endphp

<div class="file-upload-wrapper {{ $class }}" x-data="fileUploadController({
    name: '{{ $name }}',
    accept: '{{ $accept ?? '' }}',
    multiple: {{ $multiple ? 'true' : 'false' }},
    maxFiles: {{ $maxSize ?? 'null' }},
    maxSize: {{ $maxSize }},
    preview: {{ $preview ? 'true' : 'false' }}
    })">
    {{-- Label --}}
    @if($label)
        <label class="block text-sm font-medium text-text-primary mb-2">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    {{-- Drop Zone --}}
    <div
        class="file-upload-dropzone relative border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200
               border-border bg-surface/50 hover:border-primary-400 hover:bg-primary-50/30
               dark:hover:border-primary-500 dark:hover:bg-primary-900/20
               @if($disabled) opacity-50 cursor-not-allowed @else cursor-pointer @endif
               @if($error) border-danger-500 bg-danger-50/30 dark:bg-danger-900/20 @endif
               theme-stylis:border-teal-300/50 theme-stylis:hover:border-teal-400"
        :class="{
            'border-primary-500 bg-primary-50/50 dark:bg-primary-900/30 ring-2 ring-primary-500/20': isDragging,
            'border-success-500 bg-success-50/30 dark:bg-success-900/20': hasFiles
        }"
        @click="!disabled && $refs.fileInput.click()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        @paste.prevent="handlePaste($event)"
    >
        {{-- Hidden Input --}}
        <input
            x-ref="fileInput"
            type="file"
            name="{{ $name }}"
            @if($accept) accept="{{ $accept }}" @endif
            @if($multiple) multiple @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            x-on:change="handleFiles($event.target.files)"
            class="hidden"
        />

        {{-- Upload Icon --}}
        <div class="upload-icon mx-auto mb-4">
            <svg class="w-12 h-12 mx-auto text-text-tertiary transition-colors"
                 :class="isDragging ? 'text-primary-500 scale-110' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>

        {{-- Upload Text --}}
        <div class="upload-text space-y-1">
            <p class="text-base font-medium text-text-primary">
                {{ __('messages.upload_drag_drop') ?? 'Drag & drop files here' }}
            </p>
            <p class="text-sm text-text-secondary">
                {{ __('messages.upload_or') ?? 'or' }}
                <span class="text-primary-500 hover:text-primary-600 font-medium">
                    {{ __('messages.upload_browse') ?? 'browse files' }}
                </span>
            </p>
            @if($accept)
                <p class="text-xs text-text-tertiary mt-2">
                    {{ __('messages.upload_accept') ?? 'Accepted formats' }}: {{ $accept }}
                </p>
            @endif
            @if($maxSize)
                <p class="text-xs text-text-tertiary">
                    {{ __('messages.upload_max_size') ?? 'Max file size' }}: {{ number_format($maxSize / 1024, 2) }} MB
                </p>
            @endif
        </div>
    </div>

    {{-- Error Message --}}
    @if($error)
        <p class="mt-2 text-sm text-danger-500">{{ $error }}</p>
    @endif

    {{-- File Preview List --}}
    <div x-show="files.length > 0" class="file-list mt-4 space-y-2">
        <template x-for="(file, index) in files" :key="file.id">
            <div class="file-item flex items-center gap-3 p-3 bg-surface border border-border rounded-xl
                          theme-stylis:bg-white/50 theme-stylis:border-teal-200/50
                          dark:theme-stylis:bg-surface/50 dark:theme-stylis:border-teal-700/30">
                {{-- File Preview/Icon --}}
                <div class="file-preview flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <template x-if="file.preview">
                        <img :src="file.preview" :alt="file.name" class="w-full h-full object-cover" />
                    </template>
                    <template x-if="!file.preview">
                        <svg class="w-6 h-6 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </template>
                </div>

                {{-- File Info --}}
                <div class="file-info flex-1 min-w-0">
                    <p class="text-sm font-medium text-text-primary truncate" x-text="file.name"></p>
                    <p class="text-xs text-text-secondary">
                        <span x-text="formatSize(file.size)"></span>
                        <template x-if="file.error">
                            <span class="text-danger-500 ml-2" x-text="file.error"></span>
                        </template>
                    </p>
                    {{-- Progress Bar --}}
                    <div x-show="file.uploading" class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                        <div class="bg-primary-500 h-1 rounded-full transition-all duration-300"
                             :style="'width: ' + file.progress + '%'"></div>
                    </div>
                </div>

                {{-- Remove Button --}}
                <button type="button"
                        @click="removeFile(index)"
                        class="flex-shrink-0 p-2 rounded-lg text-text-secondary hover:text-danger-500
                               hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Clear All Button --}}
    <div x-show="files.length > 1" class="mt-3 text-right">
        <button type="button"
                @click="clearAll()"
                class="text-sm text-text-secondary hover:text-danger-500 transition-colors">
            {{ __('messages.upload_clear_all') ?? 'Clear All Files' }}
        </button>
    </div>
</div>

@once
@push('scripts')
<script>
function fileUploadController(options = {}) {
    return {
        name: options.name || 'file',
        accept: options.accept || '',
        multiple: options.multiple || false,
        maxFiles: options.maxFiles || null,
        maxSize: options.maxSize || 5120, // KB
        preview: options.preview !== false,
        files: [],
        isDragging: false,

        get hasFiles() {
            return this.files.length > 0;
        },

        handleFileList(fileList) {
            const newFiles = Array.from(fileList);

            // Check max files
            if (this.maxFiles && this.files.length + newFiles.length > this.maxFiles) {
                this.$toast?.error?.(`Maximum ${this.maxFiles} files allowed`);
                return;
            }

            newFiles.forEach(file => {
                // Check file size
                if (file.size > this.maxSize * 1024) {
                    this.files.push({
                        id: Date.now() + Math.random(),
                        file: file,
                        name: file.name,
                        size: file.size,
                        preview: null,
                        error: `File too large (max ${this.maxSize / 1024}MB)`,
                        uploading: false,
                        progress: 0
                    });
                    return;
                }

                // Create preview for images
                if (this.preview && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const fileObj = this.files.find(f => f.file === file);
                        if (fileObj) {
                            fileObj.preview = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }

                this.files.push({
                    id: Date.now() + Math.random(),
                    file: file,
                    name: file.name,
                    size: file.size,
                    preview: null,
                    error: null,
                    uploading: false,
                    progress: 0
                });
            });

            // Reset input
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        handleFiles(fileList) {
            if (!this.multiple) {
                this.files = []; // Clear existing for single file mode
            }
            this.handleFileList(fileList);
        },

        handleDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            this.handleFiles(files);
        },

        handlePaste(event) {
            const items = event.clipboardData.items;
            const files = [];

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    files.push(items[i].getAsFile());
                }
            }

            if (files.length > 0) {
                this.handleFiles(files);
            }
        },

        removeFile(index) {
            this.files.splice(index, 1);
        },

        clearAll() {
            this.files = [];
        },

        formatSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        // Get all valid files for form submission
        getValidFiles() {
            return this.files.filter(f => !f.error).map(f => f.file);
        },

        // Simulate upload progress (demo only)
        simulateUpload(index) {
            const file = this.files[index];
            if (!file || file.uploading) return;

            file.uploading = true;
            file.progress = 0;

            const interval = setInterval(() => {
                file.progress += 10;
                if (file.progress >= 100) {
                    file.progress = 100;
                    file.uploading = false;
                    clearInterval(interval);
                }
            }, 200);
        }
    };
}
</script>
@endpush
@endonce

@once
@push('styles')
<style>
/* File upload dropzone animations */
.file-upload-dropzone {
    transition: all 0.2s ease-in-out;
}

.file-upload-dropzone:hover .upload-icon svg {
    transform: scale(1.05);
}

.file-upload-dropzone .upload-icon svg {
    transition: transform 0.2s ease-in-out, color 0.2s ease-in-out;
}

/* File item animations */
.file-item {
    animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stylis theme overrides */
.theme-stylis .file-upload-dropzone {
    backdrop-filter: blur(8px);
}

.theme-stylis .file-item {
    backdrop-filter: blur(4px);
}

/* Dark mode adjustments */
.dark .file-upload-dropzone:hover {
    background: rgba(59, 130, 246, 0.1);
}

.dark .file-item {
    background: rgba(30, 41, 59, 0.5);
}
</style>
@endpush
@endonce
