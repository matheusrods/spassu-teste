import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

const ICONS = {
    success: 'success',
    error: 'error',
    danger: 'error',
    warning: 'warning',
    info: 'info',
};

// O elemento (renderizado por base.html.twig/login.html.twig) fica oculto
// (d-none) e só carrega o texto/tipo; este controller lê o conteúdo dele.
export default class extends Controller {
    static values = { type: String };

    connect() {
        Swal.fire({
            icon: ICONS[this.typeValue] ?? 'info',
            text: this.element.textContent.trim(),
            toast: true,
            position: 'top-end',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }
}
