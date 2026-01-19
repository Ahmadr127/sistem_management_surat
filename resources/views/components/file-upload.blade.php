@props([
    'name' => 'file',
    'label' => 'Upload File',
    'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png',
    'multiple' => true,
    'maxSize' => '2MB',
    'hint' => 'PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 2MB per file)',
    'required' => false,
    'existingFiles' => [],
    'downloadRoute' => null,
    'deleteRoute' => null,
])

<div class="space-y-4">
    @if($label)
        <label class="text-sm font-semibold text-gray-800">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Existing Files -->
    @if(count($existingFiles) > 0)
        <div class="space-y-3">
            <h4 class="text-sm font-medium text-gray-700">File Terlampir</h4>
            @foreach($existingFiles as $file)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <i class="ri-file-text-line text-2xl text-gray-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $file['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $file['type'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($downloadRoute)
                            <a href="{{ $downloadRoute }}?file_id={{ $file['id'] }}"
                                class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                                <i class="ri-download-line mr-1"></i>
                                Download
                            </a>
                        @endif
                        @if($deleteRoute)
                            <button type="button" 
                                onclick="deleteFile({{ $file['id'] }})"
                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <i class="ri-delete-bin-line mr-1"></i>
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- File Upload Area -->
    <div class="w-full">
        <input 
            type="file" 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
            id="file-input-{{ $name }}" 
            class="hidden" 
            accept="{{ $accept }}"
            {{ $multiple ? 'multiple' : '' }}
            {{ $required ? 'required' : '' }}
        >
        <label for="file-input-{{ $name }}" class="w-full">
            <div class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                <!-- Upload Text -->
                <div id="upload-text-{{ $name }}" class="flex flex-col items-center justify-center pt-5 pb-6">
                    <i class="ri-upload-cloud-line text-3xl text-gray-400 mb-3"></i>
                    <p class="text-sm text-gray-600 font-medium">Klik untuk upload atau drag and drop file</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $hint }}</p>
                </div>
                <!-- Selected File Info -->
                <div id="file-selected-{{ $name }}" class="hidden flex-col items-center justify-center pt-5 pb-6 w-full">
                    <div id="selected-files-list-{{ $name }}" class="w-full space-y-2 px-4"></div>
                    <button type="button" id="remove-all-files-{{ $name }}"
                        class="mt-3 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                        <i class="ri-delete-bin-line mr-1"></i>
                        Hapus semua file
                    </button>
                </div>
            </div>
        </label>
    </div>

    @error($name)
        <p class="text-red-500 text-xs">
            <i class="ri-error-warning-line mr-1"></i>
            {{ $message }}
        </p>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input-{{ $name }}');
    const uploadText = document.getElementById('upload-text-{{ $name }}');
    const fileSelected = document.getElementById('file-selected-{{ $name }}');
    const selectedFilesList = document.getElementById('selected-files-list-{{ $name }}');
    const removeAllBtn = document.getElementById('remove-all-files-{{ $name }}');

    if (!fileInput) return;

    // Initialize file upload handler for this specific input
    new FileUploadHandler({
        fileInput: fileInput,
        uploadText: uploadText,
        fileSelected: fileSelected,
        selectedFilesList: selectedFilesList,
        removeAllBtn: removeAllBtn,
        dropZone: fileInput.closest('label')
    });
});
</script>
@endpush
