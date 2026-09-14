import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

// Troca o confirm() nativo do navegador por um modal do SweetAlert2 antes de
// enviar o formulário de exclusão. Usa requestSubmit() (não submit()) para
// que o evento "submit" seja disparado de verdade e o Turbo intercepte.
export default class extends Controller {
    static values = { message: { type: String, default: 'Tem certeza que deseja excluir?' } };

    submit(event) {
        if (this.element.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        Swal.fire({
            title: this.messageValue,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            this.element.dataset.confirmed = 'true';
            this.element.requestSubmit();
            delete this.element.dataset.confirmed;
        });
    }
}
