import { createApp } from 'vue';
import DocumensoTemplate from './components/Documenso/DocumensoTemplate.vue';

const documenso = createApp();


documenso.component('documenso-template', DocumensoTemplate);
documenso.mount('#v-documenso');

document.querySelectorAll('#editTemplate').forEach(button => {
    button.addEventListener('click', () => {
        const templateId = button.getAttribute('data-template-id');
        const filePath = button.getAttribute('data-file-path');
        const templateName = button.getAttribute('data-template-name');
        const documentId = button.getAttribute('data-document-id');
        console.log(documentId ??= 'undefined' ? 'undefined' : documentId);
        window.dispatchEvent(new CustomEvent('open-documenso-popup', {
            detail: {
                filePath: filePath,
                templateName: templateName,
                documentId: documentId,
                templateId: templateId,
                save_fields: window.documensoRoutes.save_fields,
            }
        }));
    });
});