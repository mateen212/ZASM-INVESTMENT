<script setup lang="ts">
import { ref, onMounted, shallowRef } from 'vue';
import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import { useSortable } from '@vueuse/integrations/useSortable';

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

const showModal = ref(false);
const filePath = ref('');
const templateName = ref('');
const documentId = ref('');
const templateId = ref('');
const fields = ref<{ type: string; x: number; y: number; page: number; value?: string; pageWidth: number; pageHeight: number; }[]>([]);
const availableFields = ref([
  { type: 'signature', label: 'Signature' },
  { type: 'name', label: 'Name' },
  { type: 'text', label: 'Text' },
  { type: 'date', label: 'Date' },
]);
const pdfDoc = shallowRef<any>(null);
const currentPage = ref(1);
const totalPages = ref(0);
const canvasContainerRef = ref<HTMLDivElement | null>(null);
const fieldsPanelRef = ref<HTMLDivElement | null>(null);
const savedJson = ref<string>('');

onMounted(() => {
  window.addEventListener('open-documenso-popup', async (event: any) => {
    console.log('open-documenso-popup event:', event.detail);
    filePath.value = event.detail.filePath;
    templateName.value = event.detail.templateName;
    documentId.value = event.detail.documentId;
    templateId.value = event.detail.templateId;

    if (!templateId.value || !documentId.value || !filePath.value) {
      console.error('Missing required data:', {
        templateId: templateId.value,
        documentId: documentId.value,
        filePath: filePath.value,
      });
      alert('Cannot open editor: Missing template data.');
      return;
    }

    showModal.value = true;
    console.log('Modal data:', {
      filePath: filePath.value,
      templateName: templateName.value,
      documentId: documentId.value,
      templateId: templateId.value,
    });
    await loadPDF();
  });

  if (fieldsPanelRef.value) {
    useSortable(() => fieldsPanelRef.value, [], {
      group: { name: 'fields', pull: 'clone', put: false },
      sort: false,
      onStart: (event: any) => {
        const fieldType = availableFields.value[event.item.dataset.index].type; // Use dataset to get the index
        event.originalEvent?.dataTransfer?.setData('text/plain', fieldType);
      },
    });
  }
});

// Load PDF
const loadPDF = async () => {
  try {
    const loadingTask = pdfjsLib.getDocument(filePath.value);
    pdfDoc.value = await loadingTask.promise;
    totalPages.value = pdfDoc.value.numPages;
    await renderAllPages();
  } catch (error) {
    console.error('Error loading PDF:', error);
  }
};

// Render all pages of the PDF
const renderAllPages = async () => {
  if (!pdfDoc.value || !canvasContainerRef.value) return;

  canvasContainerRef.value.innerHTML = ''; // Clear existing canvases

  for (let pageNum = 1; pageNum <= totalPages.value; pageNum++) {
    const page = await pdfDoc.value.getPage(pageNum);
    const scale = 1;
    const viewport = page.getViewport({ scale });

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d')!;
    canvas.height = viewport.height;
    canvas.width = viewport.width;
    canvas.classList.add('mb-3');

    const renderContext = {
      canvasContext: context,
      viewport: viewport,
    };
    await page.render(renderContext).promise;

    const wrapper = document.createElement('div');
    wrapper.style.position = 'relative';
    wrapper.appendChild(canvas);

    wrapper.addEventListener('dragover', (e) => e.preventDefault());
    wrapper.addEventListener('drop', (e) => dropField(e, pageNum, canvas));

    canvasContainerRef.value.appendChild(wrapper);
  }
};

// Change page function
const changePage = (offset: number) => {
  const newPage = currentPage.value + offset;
  if (newPage >= 1 && newPage <= totalPages.value) {
    currentPage.value = newPage;
    renderPage(newPage);
  }
};

// Start dragging a field
let dragIndex = ref<number | null>(null);
let offsetX = 0;
let offsetY = 0;

const startDragging = (event: MouseEvent, index: number) => {
  dragIndex.value = index;
  const field = fields.value[index];
  offsetX = event.clientX - field.x;
  offsetY = event.clientY - field.y;

  document.addEventListener('mousemove', onDrag);
  document.addEventListener('mouseup', stopDragging);

  event.preventDefault();
};

// On drag movement
const onDrag = (event: MouseEvent) => {
  if (dragIndex.value !== null) {
    const field = fields.value[dragIndex.value];
    const canvasRect = canvasContainerRef.value?.getBoundingClientRect();

    field.x = event.clientX - offsetX;
    field.y = event.clientY - offsetY;

    if (canvasRect) {
      field.x = Math.max(0, Math.min(field.x, canvasRect.width - 30));
      field.y = Math.max(0, Math.min(field.y, canvasRect.height - 20));
    }
  }
};

// Stop dragging
const stopDragging = () => {
  dragIndex.value = null;
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDragging);
};

// Drop field on canvas
const dropField = (event: DragEvent, pageNum: number, canvas: HTMLCanvasElement) => {
  event.preventDefault();

  if (dragIndex.value === null) return;

  const rect = canvas.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;

  const fieldType = event.dataTransfer?.getData('text/plain');
  if (fieldType) {
    const field = fields.value[dragIndex.value];
    field.x = x;
    field.y = y;
    field.page = pageNum;
    field.type = fieldType;

    dragIndex.value = null;
  }
};

// Allow drop on canvas
const allowDrop = (event: DragEvent) => {
  event.preventDefault();
};

// Close the modal
const closeModal = () => {
  showModal.value = false;
  filePath.value = '';
  templateName.value = '';
  documentId.value = '';
  templateId.value = '';
  fields.value = [];
  currentPage.value = 1;
  savedJson.value = ''; // Reset savedJson
};

// Save fields function
const saveFields = async () => {
  const savedData = {
    template_id: templateId.value,
    fields: fields.value,
  };

  savedJson.value = JSON.stringify(savedData, null, 2);

  try {
    const response = await fetch('/admin/save-fields-to-document', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: savedJson.value,
    });

    const result = await response.json();
    if (response.ok) {
      alert("Fields saved and document status updated to PENDING successfully!");
      console.log("API Response:", result);
    } else {
      alert("Failed to save fields: " + result.error);
      console.error("API Error:", result);
    }
  } catch (error) {
    alert("An error occurred while saving fields.");
    console.error("Fetch Error:", error);
  }
};

// Get field icon based on type
const getFieldIcon = (type: string): string => {
  switch (type) {
    case 'signature': return 'fas fa-pen-nib';
    case 'name': return 'fas fa-user';
    case 'text': return 'fas fa-font';
    case 'date': return 'fas fa-calendar-alt';
    case 'email': return 'fas fa-envelope';
    default: return 'fas fa-square';
  }
};

</script>


<template>
  <div>
    <!-- Modal for Document Preview and Editing -->
    <div v-if="showModal" class="modal fade show" style="display: block;" tabindex="-1">
      <div class="modal-dialog modal-xl" style="max-width: 70%;">
        <div class="modal-content">
          <div class="modal-header bg-primary ">
            <h5 class="modal-title text-white">Edit Document: {{ templateName }}</h5>
            <button type="button" class="btn-close btn-close-white text-white" @click="closeModal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body d-flex p-0" style="height: 80vh; overflow: hidden;">
            <!-- PDF Preview Area -->
            <div class="p-5"
              style="width: 75%; background-color: #000; overflow-y: auto; position: relative; max-height: 100%;"
              @dragover.prevent @drop="handleDrop">

              <div ref="canvasContainerRef" class="pdf-canvas-container" style="position: relative;" @dragover.prevent
                @drop="allowDrop">

                <!-- Loop over fields and render them as draggable items -->
                <div v-for="(field, index) in fields" :key="index" v-if="canvasContainerRef" :style="{
                  position: 'absolute',
                  left: `${field.x}px`,
                  top: `${field.y}px`,
                  background: 'rgba(255, 255, 255, 0.9)',
                  border: '1px dashed #555',
                  padding: '4px 8px',
                  borderRadius: '6px',
                  fontSize: '12px',
                  cursor: 'move',
                  zIndex: 10
                }" @mousedown="event => startDragging(event, index)" draggable="true" @dragstart="onDragStart(index)">
                  <span>{{ field.type }}</span>
                </div>
              </div>
            </div>



            <!-- Fields Sidebar -->
            <!-- Fields Sidebar -->
            <div ref="fieldsPanelRef" class="p-3 text-light" style="width: 25%;  border-left: 1px solid #333;">

              <h6 class="mb-3">Fields</h6>

              <div class="available-fields-grid">
                <div v-for="(field, index) in availableFields" :key="index" :data-index="index" draggable="true"
                  @dragstart="event => event.dataTransfer?.setData('text/plain', field.type)"
                  class="btn  border rounded text-start d-flex align-items-center gap-2 px-3 py-2 field-box">
                  <i :class="getFieldIcon(field.type)" style="font-size: 1.1rem;"></i>
                  <span :style="{ fontFamily: field.type === 'signature' ? 'cursive' : 'inherit' }">
                    {{ field.label }}
                  </span>
                </div>
              </div>
              <p class="text-white pt-2 mb-3" style="font-size: 0.85rem;">
                Drag and drop a field onto the document. After placing it, you can click and move it anywhere on the
                page.
              </p>
            </div>
          </div>


          <div class="modal-footer">
            <button class="btn btn-primary" @click="saveFields">Save Fields</button>
            <button class="btn btn-secondary" @click="closeModal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="modal-backdrop fade show" @click="closeModal"></div>
  </div>
</template>

<style scoped>
.available-fields-grid {
  display: grid;
  gap: 0.5rem;
  width: 100%;
  /* Stay within parent container */
}

/* Small screens (e.g., mobile, < 576px) */
@media (max-width: 575.98px) {
  .available-fields-grid {
    grid-template-columns: 1fr;
    /* 1 column */
  }
}

/* Laptop screens (e.g., 576px - 991px) */
@media (min-width: 576px) and (max-width: 991.98px) {
  .available-fields-grid {
    grid-template-columns: repeat(2, 1fr);
    /* 2 columns */
  }
}

/* Big screens (e.g., >= 992px) */
@media (min-width: 992px) {
  .available-fields-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    /* As many as fit */
  }
}

.field-box {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #555;
  border-radius: 0.25rem;
  background-color: #fff;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
  /* ✨ Add this line */
  transition: box-shadow 0.2s ease;
}

.field-box:hover {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  /* ✨ Slightly more on hover */
}

.field-box i {
  flex-shrink: 0;
}

.field-box span {
  flex-grow: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Ensure content stays within modal on small screens */
@media (max-width: 575.98px) {
  .field-box {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }

  .field-box i {
    font-size: 1rem;
  }
}
</style>