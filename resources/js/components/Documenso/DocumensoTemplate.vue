<script setup lang="ts">
import { ref, onMounted, shallowRef } from 'vue';
import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import { useSortable } from '@vueuse/integrations/useSortable'

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

const showModal = ref(false);
const filePath = ref('');
const templateName = ref('');
const save_fields = ref('');
const documentId = ref('');
const templateId = ref('');
const loading = ref(false);
const fields = ref<{ type: string; x: number; y: number; page: number; value?: string; pageWidth: number; pageHeight: number }[]>([]);
const availableFields = ref([
  { type: 'signature', label: 'Signature' },
  { type: 'name', label: 'Name' },
  { type: 'text', label: 'Text' },
  { type: 'date', label: 'Date' },
]);
const pdfDoc = shallowRef<any>(null);
const totalPages = ref(0);
const canvasRefs = ref<HTMLCanvasElement[]>([]);
const fieldsPanelRef = ref<HTMLDivElement | null>(null);
const savedJson = ref<string>('');

onMounted(() => {
  window.addEventListener('open-documenso-popup', async (event: any) => {
    filePath.value = event.detail.filePath;
    templateName.value = event.detail.templateName;
    documentId.value = event.detail.documentId;
    templateId.value = event.detail.templateId;
    save_fields.value = event.detail.save_fields;
    if (!templateId.value || !documentId.value || !filePath.value) {
      console.error('Missing required data:', { templateId: templateId.value, documentId: documentId.value, filePath: filePath.value });
      alert('Cannot open editor: Missing template data.');
      return;
    }

    showModal.value = true;
    await loadPDF();
  });

  if (fieldsPanelRef.value) {
    useSortable(() => fieldsPanelRef.value, [], {
      group: { name: 'fields', pull: 'clone', put: false },
      sort: false,
      onStart: (event: any) => {
        const fieldType = availableFields.value[event.item.dataset.index].type;
        event.originalEvent?.dataTransfer?.setData('text/plain', fieldType);
      },
    });
  }
});

const loadPDF = async () => {
  loading.value = true;
  try {
    const loadingTask = pdfjsLib.getDocument(filePath.value);
    pdfDoc.value = await loadingTask.promise;
    totalPages.value = pdfDoc.value.numPages;
    canvasRefs.value = new Array(totalPages.value);
    renderAllPages();
  } catch (error) {
    console.error('Error loading PDF:', error);
  }
  loading.value = false;
};

const renderAllPages = () => {
  for (let pageNum = 1; pageNum <= totalPages.value; pageNum++) {
    pdfDoc.value.getPage(pageNum).then((page: any) => {
      const scale = 1;
      const viewport = page.getViewport({ scale });
      const canvas = canvasRefs.value[pageNum - 1];
      if (!canvas) return;
      const context = canvas.getContext('2d')!;
      canvas.height = viewport.height;
      canvas.width = viewport.width;
      const renderContext = { canvasContext: context, viewport };
      page.render(renderContext);

      // Add page number overlay
      const pageNumberOverlay = document.createElement('div');
      pageNumberOverlay.innerText = `${pageNum} / ${totalPages.value}`;

      // Apply inline styles
      Object.assign(pageNumberOverlay.style, {
        position: 'absolute',
        top: '94%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        color: 'white',
        fontSize: '14px',
        padding: '5px',
        borderRadius: '4px',
      });

      canvas.parentElement?.appendChild(pageNumberOverlay);
    }).catch((error: any) => {
      console.error('Error rendering page:', error);
    });
  }
};


const dropField = (event: DragEvent, pageNum: number) => {
  if (!event) return;
  event.preventDefault();
  const canvas = canvasRefs.value[pageNum - 1];
  if (!canvas) return;

  const rect = canvas.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;
  const fieldType = event.dataTransfer?.getData('text/plain');

  if (fieldType) {
    // Add the field to all pages with the same initial position
    const pageCanvas = canvasRefs.value[pageNum - 1];
    const pageRect = pageCanvas.getBoundingClientRect();
    fields.value.push({
      type: fieldType,
      x: Math.max(0, Math.min(x, canvas.width - 30)),
      y: Math.max(0, Math.min(y, canvas.height - 20)),
      page: pageNum,
      pageWidth: canvas.width,
      pageHeight: canvas.height,
    });

    console.log('Dropped field on all pages:', fields.value);
  }
};

const closeModal = () => {
  showModal.value = false;
  filePath.value = '';
  templateName.value = '';
  documentId.value = '';
  templateId.value = '';
  fields.value = [];
  savedJson.value = '';
};

const saveFields = async () => {
  debugger;
  loading.value = true;
  const savedData = {
    template_id: templateId.value,
    fields: fields.value,
  };
  savedJson.value = JSON.stringify(savedData, null, 2);
    const url = window.documensoRoutes.save_fields;

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: savedJson.value,
    });
    const result = await response.json();
    if (response.ok) {
      showSuccessToast("Fields saved successfully!");
      closeModal(); // Close modal after success
      window.location.reload(); // Reload the page to reflect changes

    } else {
      showErrorToast("Failed to save fields: " + result.error);
    }
  } catch (error) {
    showErrorToast("An error occurred while saving fields.");
    console.error("Fetch Error:", error);
  } finally {
    loading.value = false;
  }
};
const createToast = (message: string, bgColor: string) => {
  const toast = document.createElement('div');
  const progress = document.createElement('div');
  const closeBtn = document.createElement('span');

  // Toast styling
  toast.innerText = message;
  toast.style.background = bgColor;
  toast.style.color = '#fff';
  toast.style.padding = '12px 20px';
  toast.style.borderRadius = '8px';
  toast.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
  toast.style.fontSize = '14px';
  toast.style.display = 'flex';
  toast.style.alignItems = 'center';
  toast.style.gap = '12px';
  toast.style.minWidth = '300px';
  toast.style.maxWidth = '90%';

  // Progress bar
  progress.style.position = 'absolute';
  progress.style.bottom = '0';
  progress.style.left = '0';
  progress.style.height = '4px';
  progress.style.width = '100%';
  progress.style.background = 'rgba(255,255,255,0.7)';
  progress.style.transition = 'width 3s linear';

  // Close button
  closeBtn.innerHTML = '&times;';
  closeBtn.style.cursor = 'pointer';
  closeBtn.style.fontWeight = 'bold';
  closeBtn.style.marginLeft = 'auto';
  closeBtn.style.fontSize = '18px';

  // Toast wrapper styling — position at top center
  const toastWrapper = document.createElement('div');
  toastWrapper.style.position = 'fixed';
  toastWrapper.style.top = '20px';
  toastWrapper.style.left = '50%';
  toastWrapper.style.transform = 'translateX(-50%)';
  toastWrapper.style.zIndex = '10000';
  toastWrapper.style.pointerEvents = 'none'; // allow clicks to pass through if needed

  toastWrapper.appendChild(toast);
  toast.appendChild(closeBtn);
  toastWrapper.appendChild(progress);
  document.body.appendChild(toastWrapper);

  // Animate progress bar
  requestAnimationFrame(() => {
    progress.style.width = '0%';
  });

  const timeout = setTimeout(() => {
    document.body.removeChild(toastWrapper);
  }, 3000);

  closeBtn.addEventListener('click', () => {
    clearTimeout(timeout);
    document.body.removeChild(toastWrapper);
  });
};
const showSuccessToast = (message: string) => {
  createToast(message, '#28a745');
};

const showErrorToast = (message: string) => {
  createToast(message, '#dc3545');
};



const getFieldIcon = (type: string): string => {
  switch (type) {
    case 'signature': return 'fas fa-pen-nib';
    case 'name': return 'fas fa-user';
    case 'text': return 'fas fa-font';
    case 'date': return 'fas fa-calendar-alt';
    default: return 'fas fa-square';
  }
};

let draggedField = ref<typeof fields.value[0] | null>(null);
let offsetX = ref(0);
let offsetY = ref(0);

const startDragging = (event: MouseEvent, field: typeof fields.value[0]) => {
  draggedField.value = field;
  const canvas = canvasRefs.value[field.page - 1];
  const rect = canvas.getBoundingClientRect();
  offsetX.value = event.clientX - field.x - rect.left;
  offsetY.value = event.clientY - field.y - rect.top;

  document.addEventListener('mousemove', onDrag);
  document.addEventListener('mouseup', stopDragging);
};

const onDrag = (event: MouseEvent) => {
  if (!draggedField.value) return;

  for (let pageNum = 1; pageNum <= totalPages.value; pageNum++) {
    const canvas = canvasRefs.value[pageNum - 1];
    const rect = canvas.getBoundingClientRect();

    if (event.clientY >= rect.top && event.clientY <= rect.bottom) {
      const newX = event.clientX - offsetX.value - rect.left;
      const newY = event.clientY - offsetY.value - rect.top;

      draggedField.value.page = pageNum;
      draggedField.value.x = Math.max(0, Math.min(newX, rect.width - 30));
      draggedField.value.y = Math.max(0, Math.min(newY, rect.height - 20));
      draggedField.value.pageWidth = rect.width;
      draggedField.value.pageHeight = rect.height;
      break;
    }
  }
};

const stopDragging = () => {
  draggedField.value = null;
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDragging);
};

</script>

<template>
  <div>
    <div v-if="showModal" class="modal fade show" style="display: block;" tabindex="-1">
      <div class="modal-dialog modal-xl" style="max-width: 80%;">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">Edit Document: {{ templateName }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body d-flex p-0" style="height: 80vh;">
            <div v-if="loading" class="custom-loader-overlay">
              <div class="custom-loader"></div>
            </div>
            <div class="pdf-preview"
              style="width: 75%; overflow-y: auto; background-color: #000; display: flex; flex-direction: column; align-items: center;">
              <div v-for="pageNum in totalPages" :key="pageNum"
                style="position: relative; margin-bottom: 10px; margin-top: 10px; ">
                <canvas style="width: 677px; height: auto; border-radius: 6px;"
                  :ref="el => canvasRefs[pageNum - 1] = el as HTMLCanvasElement" @dragover.prevent
                  @drop="event => dropField(event, pageNum)"></canvas>
                <div v-for="(field, index) in fields.filter(f => f.page === pageNum)" :key="index"
                  class="field-nonselectable" :style="{
                    position: 'absolute',
                    left: `${field.x}px`,
                    top: `${field.y}px`,
                    background: 'rgba(255, 255, 255, 0.9)',
                    border: '1px dashed #555',
                    padding: '4px 8px',
                    borderRadius: '6px',
                    fontSize: '12px',
                    cursor: 'move',
                    zIndex: 10,
                    maxWidth: '120px',
                    whiteSpace: 'nowrap',
                    overflow: 'hidden',
                    textOverflow: 'ellipsis',

                  }" @mousedown="event => startDragging(event, field)">
                  <span :title="field.type">{{ field.type }}</span>
                </div>
              </div>
            </div>
            <div ref="fieldsPanelRef" class="p-3 text-light"
              style="width: 25%; border-left: 1px solid #333; overflow: hidden;">
              <h6 class="mb-3">Fields</h6>
              <div class="available-fields-grid">
                <div v-for="(field, index) in availableFields" :key="index" :data-index="index" draggable="true"
                  @dragstart="event => event.dataTransfer?.setData('text/plain', field.type)"
                  class="btn border rounded text-start d-flex align-items-center gap-2 px-3 py-2 field-box">
                  <i :class="getFieldIcon(field.type)" style="font-size: 1.1rem;"></i>
                  <span :style="{ fontFamily: field.type === 'signature' ? 'cursive' : 'inherit' }">
                    {{ field.label }}
                  </span>
                </div>
              </div>
              <p class="text-white pt-2 mb-3" style="font-size: 0.85rem;">
                Drag and drop a field onto the document. After placing it, you can click and move it anywhere across all
                pages.
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
.pdf-preview {
  scrollbar-width: thin;
  scrollbar-color: #888 #000;
}

.pdf-preview::-webkit-scrollbar {
  width: 8px;
}

.pdf-preview::-webkit-scrollbar-track {
  background: #000;
}

.pdf-preview::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.pdf-preview::-webkit-scrollbar-thumb:hover {
  background: #aaa;
}

.available-fields-grid {
  display: grid;
  gap: 0.5rem;
  width: 100%;
}

@media (max-width: 575.98px) {
  .available-fields-grid {
    grid-template-columns: 1fr;
  }
}

@media (min-width: 576px) and (max-width: 991.98px) {
  .available-fields-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 992px) {
  .available-fields-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
  transition: box-shadow 0.2s ease;
}

.field-box:hover {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}

.field-box i {
  flex-shrink: 0;
}

.field-box span {
  flex-grow: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: 575.98px) {
  .field-box {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }

  .field-box i {
    font-size: 1rem;
  }
}

/* Prevent text selection on draggable fields */
.field-nonselectable {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}

.custom-loader-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.custom-loader {
  border: 6px solid #ccc;
  border-top: 6px solid #007bff;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 0.9s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>